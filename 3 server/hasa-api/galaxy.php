<?php
declare(strict_types=1);
// HASA Webansicht 1.2.0-web.2 – Sondenberichte und auswählbare Mittelwerte.
header('Content-Type: text/html; charset=utf-8');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: no-referrer');
header("Content-Security-Policy: default-src 'none'; style-src 'unsafe-inline'; script-src 'nonce-hasa-galaxy-v1'; connect-src 'self'; base-uri 'none'; form-action 'none'");
?>
<!doctype html>
<html lang="de">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>HASA – Galaxiedatenbank</title>
<style>
  :root { color-scheme: dark; font: 18px/1.5 system-ui, sans-serif; background: #111827; color: #f3f4f6; }
  body { margin: 0 auto; max-width: 1100px; padding: 1.5rem; }
  h1 { margin: 0 0 .2rem; font-size: 1.6rem; }
  p { margin: .3rem 0 1rem; }
  form { display: flex; flex-wrap: wrap; gap: .8rem; align-items: end; padding: 1rem; background: #1f2937; border-radius: .7rem; }
  label { display: grid; gap: .25rem; }
  input, button { font: inherit; padding: .55rem; border-radius: .4rem; border: 1px solid #9ca3af; }
  input { width: 9rem; background: #172033; color: #f3f4f6; }
  input[type=checkbox] { width: 1.2rem; height: 1.2rem; accent-color: #60a5fa; }
  button { background: #2563eb; color: white; cursor: pointer; border-color: #2563eb; }
  button:focus-visible, input:focus-visible { outline: 3px solid #facc15; outline-offset: 2px; }
  #status { margin: 1rem 0; min-height: 1.5rem; }
  article { background: #1f2937; margin: 1rem 0; padding: 1rem; border-radius: .7rem; }
  h2 { margin: 0 0 .5rem; font-size: 1.2rem; }
  .scroll { overflow-x: auto; }
  table { border-collapse: collapse; width: 100%; min-width: 680px; }
  th, td { padding: .45rem; border-bottom: 1px solid #4b5563; text-align: left; }
  th { color: #bfdbfe; }
  .scan { background: #facc15; color: #111827; border-color: #facc15; padding: .1rem .5rem; margin-right: .5rem; }
  .report { margin: .7rem 0; padding: .8rem; background: #111827; border-radius: .4rem; }
  .report label { display: flex; align-items: center; flex-wrap: wrap; gap: .6rem; font-weight: 600; }
  .report table { min-width: 0; }
  .muted { color: #cbd5e1; font-size: .9rem; }
  .average { padding: .8rem; border: 1px solid #60a5fa; border-radius: .4rem; margin: .7rem 0; }
</style>
</head>
<body>
<h1>HASA – Galaxiedatenbank</h1>
<p>Galaxien 1 bis 6 · <span style="color:#facc15">★</span> Sondenberichte am Planeten öffnen</p>
<form id="search">
  <label>Galaxie <input name="galaxy" type="number" min="1" max="6" step="1" inputmode="numeric"></label>
  <label>System <input name="system" type="number" min="0" max="999999" step="1" inputmode="numeric"></label>
  <button type="submit">Suchen</button>
</form>
<div id="status" role="status" aria-live="polite"></div>
<main id="results"></main>
<script nonce="hasa-galaxy-v1">
(() => {
  'use strict';
  const form = document.querySelector('#search');
  const status = document.querySelector('#status');
  const results = document.querySelector('#results');
  const columns = ['Bahn', 'Name', 'Typ', 'Spieler', 'Allianz', 'Status', 'Beobachtet (UTC)'];
  const fields = ['orbit', 'name', 'type', 'ruler', 'alliance', 'status', 'last_observed_at'];
  const number = new Intl.NumberFormat('de-DE', { maximumFractionDigits: 3 });
  let searchController;
  function element(tag, text, className) {
    const node = document.createElement(tag);
    if (text !== undefined) node.textContent = text;
    if (className) node.className = className;
    return node;
  }
  function metricsTable(values) {
    const table = element('table');
    const head = table.createTHead().insertRow();
    for (const label of ['Merkmal', 'Schätzwert (%)', 'Messungen']) head.append(element('th', label));
    const body = table.createTBody();
    for (const metric of values) {
      const row = body.insertRow();
      row.insertCell().textContent = metric.metric_name;
      row.insertCell().textContent = number.format(Number(metric.value_percent));
      row.insertCell().textContent = metric.count ?? '1';
    }
    return table;
  }
  async function showReports(container, item, planet) {
    container.textContent = 'Lade Sondenberichte …';
    try {
      const params = new URLSearchParams({ galaxy: item.galaxy, system: item.system, orbit: planet.orbit });
      const response = await fetch('prospection-read.php?' + params, { credentials: 'same-origin' });
      const payload = await response.json();
      if (!response.ok || !payload.ok || !Array.isArray(payload.data)) throw new Error('read_failed');
      container.replaceChildren();
      const reports = payload.data;
      if (!reports.length) { container.textContent = 'Noch keine Sondenberichte vorhanden.'; return; }
      const average = element('div', undefined, 'average');
      const selected = new Set();
      function updateAverage() {
        average.replaceChildren();
        const chosen = [...selected].map(index => reports[index]);
        if (!chosen.length) { average.textContent = 'Berichte für einen Mittelwert auswählen.'; return; }
        const types = new Set(chosen.map(r => r.probe_type_code || r.probe_type_name || 'unbekannt'));
        if (types.size > 1) { average.textContent = 'Für einen Mittelwert bitte denselben Sondentyp auswählen.'; return; }
        const known = chosen.filter(r => r.probe_count !== null && r.probe_count !== undefined);
        const sum = known.reduce((total, r) => total + Number(r.probe_count), 0);
        const times = chosen.map(r => r.observed_at).sort();
        average.append(element('strong', `Mittelwerte · ${chosen.length} Messungen · ${number.format(sum)} Sonden${known.length < chosen.length ? ' bekannt (Summe unvollständig)' : ' insgesamt'}`));
        average.append(element('p', `${times[0]} bis ${times[times.length - 1]} UTC · Arithmetische Mittelwerte je Merkmal, ohne Zerfallskorrektur`, 'muted'));
        const totals = new Map();
        for (const report of chosen) {
          for (const metric of report.measurements || []) {
            const current = totals.get(metric.metric_name) || { sum: 0, count: 0 };
            current.sum += Number(metric.value_percent); current.count++;
            totals.set(metric.metric_name, current);
          }
        }
        const values = [...totals].sort(([a], [b]) => a.localeCompare(b, 'de')).map(([name, value]) => ({ metric_name: name, value_percent: value.sum / value.count, count: value.count }));
        average.append(metricsTable(values));
      }
      container.append(average);
      updateAverage();
      if (Number(planet.report_count) > reports.length) container.append(element('p', `Die neuesten ${reports.length} von ${planet.report_count} Berichten werden angezeigt.`, 'muted'));
      reports.forEach((report, index) => {
        const card = element('section', undefined, 'report');
        const label = element('label');
        const checkbox = element('input'); checkbox.type = 'checkbox';
        checkbox.addEventListener('change', () => { checkbox.checked ? selected.add(index) : selected.delete(index); updateAverage(); });
        label.append(checkbox, element('span', `${report.observed_at} UTC · ${report.probe_count ?? '?'} ${report.probe_type_name || report.probe_type_code || 'Sonden'}`));
        card.append(label, metricsTable(report.measurements || []));
        container.append(card);
      });
    } catch (_) { container.textContent = 'Die Sondenberichte konnten nicht geladen werden. Bitte schließen und erneut öffnen.'; }
  }

  form.addEventListener('submit', async event => {
    event.preventDefault();
    if (!form.reportValidity()) return;
    const galaxy = form.elements.galaxy.value.trim();
    const system = form.elements.system.value.trim();
    if (system && !galaxy) {
      status.textContent = 'Bitte für eine Systemsuche auch die Galaxie angeben.';
      return;
    }
    const params = new URLSearchParams();
    if (galaxy) params.set('galaxy', galaxy);
    if (system) params.set('system', system);
    results.replaceChildren();
    if (searchController) searchController.abort();
    searchController = new AbortController();
    status.textContent = 'Lade Einträge aus den Galaxien 1 bis 6 …';
    try {
      const response = await fetch('galaxy-read.php?' + params, { credentials: 'same-origin', signal: searchController.signal });
      const payload = await response.json();
      if (!response.ok || !payload.ok || !Array.isArray(payload.data)) throw new Error('read_failed');
      status.textContent = payload.data.length
        ? `${payload.data.length} System(e) angezeigt (höchstens ${payload.limit}).`
        : 'Keine erfassten Einträge für diese Suche vorhanden.';
      for (const item of payload.data) {
        const card = document.createElement('article');
        const title = document.createElement('h2');
        title.textContent = `Galaxie ${item.galaxy} · System ${item.system}${item.system_name ? ' · ' + item.system_name : ''}`;
        card.append(title);
        const wrap = document.createElement('div');
        wrap.className = 'scroll';
        const table = document.createElement('table');
        const head = table.createTHead().insertRow();
        for (const label of columns) { const th = document.createElement('th'); th.textContent = label; head.append(th); }
        const body = table.createTBody();
        for (const planet of item.planets || []) {
          const row = body.insertRow();
          for (const field of fields) row.insertCell().textContent = planet[field] ?? '–';
          if (Number(planet.report_count) > 0) {
            const button = element('button', `★${planet.report_count}`, 'scan');
            button.type = 'button';
            button.setAttribute('aria-label', `${planet.report_count} Sondenberichte für ${planet.name || 'Bahn ' + planet.orbit} öffnen`);
            button.setAttribute('aria-expanded', 'false');
            row.cells[1].prepend(button);
            const detailRow = body.insertRow(); detailRow.hidden = true;
            const cell = detailRow.insertCell(); cell.colSpan = columns.length;
            let loaded = false;
            let loading = false;
            button.addEventListener('click', async () => {
              detailRow.hidden = !detailRow.hidden;
              button.setAttribute('aria-expanded', String(!detailRow.hidden));
              if (!detailRow.hidden && !loaded && !loading) {
                loading = true;
                await showReports(cell, item, planet);
                loaded = !!cell.querySelector('.report');
                loading = false;
              }
            });
          }
        }
        if (!body.rows.length) {
          const cell = body.insertRow().insertCell();
          cell.colSpan = columns.length;
          cell.textContent = 'Keine erfassten Planeten.';
        }
        wrap.append(table);
        card.append(wrap);
        results.append(card);
      }
    } catch (_) {
      if (_.name === 'AbortError') return;
      status.textContent = 'Die Galaxiedatenbank ist derzeit nicht erreichbar.';
    }
  });
  const initial = new URLSearchParams(location.search);
  if (initial.has('galaxy')) form.elements.galaxy.value = initial.get('galaxy');
  if (initial.has('system')) form.elements.system.value = initial.get('system');
  form.requestSubmit();
})();
</script>
</body>
</html>
