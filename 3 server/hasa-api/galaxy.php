<?php
declare(strict_types=1);
require __DIR__ . '/auth.php';
$hasaUser = hasaRequireUser();
$hasaRound = hasaRound($_GET['round'] ?? null);
// HASA Webansicht 1.2.0-web.4 – Standort, Planetensuche und kompakter Vergleich.
$hasaNonce = base64_encode(random_bytes(18));
header('Content-Type: text/html; charset=utf-8');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: no-referrer');
header("Content-Security-Policy: default-src 'none'; style-src 'unsafe-inline'; script-src 'nonce-$hasaNonce'; connect-src 'self'; base-uri 'none'; form-action 'self'; frame-ancestors 'none'");
?>
<!doctype html>
<html lang="de">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>HASA – Galaxiedatenbank</title>
<style>
  :root { color-scheme: dark; font: 18px/1.45 system-ui,sans-serif; background:#111827; color:#f3f4f6; }
  [hidden] { display:none !important; }
  * { box-sizing:border-box; } body { margin:0 auto; max-width:1600px; padding:1rem; }
  h1 { margin:0; font-size:1.5rem; } h2 { margin:0 0 .4rem; font-size:1.15rem; }
  p { margin:.4rem 0 .8rem; } form { padding:.8rem; background:#1f2937; border-radius:.6rem; }
  .filters,.toolbar { display:flex; flex-wrap:wrap; gap:.6rem; align-items:end; }
  label { display:grid; gap:.2rem; } input,button { font:inherit; padding:.45rem; border:1px solid #64748b; border-radius:.35rem; }
  input { width:9rem; background:#172033; color:#f3f4f6; } input[name=q] { width:min(24rem,100%); }
  button { background:#2563eb; color:white; cursor:pointer; } a { color:#bfdbfe; } button:disabled { opacity:.5; cursor:default; }
  button:focus-visible,input:focus-visible,summary:focus-visible,.scroll:focus-visible { outline:3px solid #facc15; outline-offset:2px; }
  details.filters-more { margin-top:.6rem; } summary { cursor:pointer; color:#bfdbfe; } .filters-more .filters { padding-top:.6rem; }
  #status { margin:.7rem 0; min-height:1.4rem; } article { background:#1f2937; padding:.8rem; border-radius:.6rem; margin:.8rem 0; }
  .system-grid { display:grid; grid-template-columns:210px minmax(0,1fr); gap:.8rem; }
  .system-summary { border-left:4px solid #64748b; padding:.6rem; align-self:start; position:sticky; top:.5rem; }
  .current .system-summary { border-color:#facc15; background:#263249; }
  .coordinate { font-size:1.5rem; font-weight:700; } .scroll { overflow-x:auto; scrollbar-color:#60a5fa #111827; scrollbar-width:auto; padding-bottom:.4rem; }
  table { border-collapse:separate; border-spacing:0; width:100%; } th,td { padding:.35rem .55rem; border-bottom:1px solid #475569; text-align:left; }
  .comparison th,.comparison td { min-width:185px; max-width:230px; overflow-wrap:anywhere; vertical-align:top; }
  .comparison th:first-child { position:sticky; left:0; background:#1f2937; z-index:1; min-width:190px; width:190px; color:#bfdbfe; }
  .comparison thead th { background:#172033; } .comparison thead th:first-child { z-index:2; }
  .scan { background:#facc15; color:#111827; padding:.05rem .4rem; margin-right:.4rem; }
  .report { margin:.6rem 0; padding:.7rem; background:#111827; border-radius:.4rem; }
  .report label { display:flex; align-items:center; gap:.6rem; flex-wrap:wrap; font-weight:600; }
  input[type=checkbox] { width:1.2rem; height:1.2rem; accent-color:#60a5fa; }
  .muted { color:#cbd5e1; font-size:.85rem; } .average { padding:.7rem; border:1px solid #60a5fa; border-radius:.4rem; margin:.6rem 0; }
  .report-pane { margin-top:.6rem; } #pages { display:flex; gap:.6rem; align-items:center; }
  @media(max-width:700px) { .system-grid { grid-template-columns:minmax(0,1fr); } .system-summary { position:static; } .comparison th:first-child { min-width:145px; width:145px; } body { padding:.5rem; } }
</style>
</head>
<body>
<h1>HASA – Galaxiedatenbank</h1>
<p class="muted"><?= hasaAuthEscape($hasaUser['player_name']) ?> · <a href="password-change.php">Passwort ändern</a> · <a href="logout.php">Abmelden</a></p>
<p class="muted">Spielrunde <?= $hasaRound ?> · Galaxien 1 bis 6 · Daten anderer Runden werden nicht beigemischt</p>
<form id="search">
  <input name="round" type="hidden" value="<?= $hasaRound ?>">
  <div class="filters">
    <label>Galaxie <input name="galaxy" type="number" min="1" max="6" step="1" inputmode="numeric"></label>
    <label>System <input name="system" type="number" min="0" max="999999" step="1" inputmode="numeric"></label>
    <label>Suchbegriff <input name="q" type="search" maxlength="160" placeholder="Name, Spieler, Allianz, Typ …"></label>
    <button type="submit">Suchen</button>
    <button id="position" type="button" disabled>Standort anzeigen</button>
    <button id="all" type="button">Alle Galaxien durchsuchen</button>
  </div>
  <details class="filters-more"><summary>Weitere Suchfelder</summary><div class="filters">
    <label>Spieler <input name="player" maxlength="120"></label>
    <label>Allianz <input name="alliance" maxlength="80"></label>
    <label>Umlaufbahn <input name="orbit" type="number" min="1" max="65535" step="1"></label>
    <label>Planetenname <input name="name" maxlength="160"></label>
    <label>Planetentyp <input name="type" maxlength="40"></label>
    <label>Status <input name="status" maxlength="80"></label>
  </div></details>
</form>
<div id="status" role="status" aria-live="polite"></div>
<main id="results"></main>
<nav id="pages" aria-label="Suchergebnisse" hidden><button id="previous" type="button">Zurück</button><span id="page-info"></span><button id="next" type="button">Weiter</button></nav>
<script nonce="<?= hasaAuthEscape($hasaNonce) ?>">
(() => {
  'use strict';
  const form = document.querySelector('#search');
  const status = document.querySelector('#status');
  const results = document.querySelector('#results');
  const columns = ['Bahn', 'Name', 'Typ', 'Spieler', 'Allianz', 'Status', 'Beobachtet (UTC)'];
  const fields = ['orbit', 'name', 'type', 'ruler', 'alliance', 'status', 'last_observed_at'];
  const number = new Intl.NumberFormat('de-DE', { maximumFractionDigits: 3 });
  let searchController;
  let offset = 0;
  let pageLimit = 20;
  let activeParams = new URLSearchParams();
  let position = null;
  const filterNames = ['galaxy','system','q','player','alliance','orbit','name','type','status'];
  const positionButton = document.querySelector('#position');
  const activeRound = String(<?= $hasaRound ?>);
  const pages = document.querySelector('#pages');
  function validPosition(galaxy, system) {
    return /^\d+$/.test(String(galaxy)) && /^\d+$/.test(String(system)) && Number(galaxy) >= 1 && Number(galaxy) <= 6 && Number(system) <= 999999;
  }
  function rememberPosition(galaxy, system) {
    if (!validPosition(galaxy, system)) return;
    position = { galaxy: String(Number(galaxy)), system: String(Number(system)) };
    positionButton.disabled = false;
    try { localStorage.setItem('hasa_web_position_v1', JSON.stringify(position)); } catch (_) {}
  }
  function clearFilters() { for (const name of filterNames) form.elements[name].value = ''; }
  positionButton.addEventListener('click', () => {
    if (!position) return;
    clearFilters();
    form.elements.galaxy.value = position.galaxy;
    form.elements.system.value = position.system;
    form.requestSubmit();
  });
  document.querySelector('#all').addEventListener('click', () => {
    form.elements.galaxy.value = ''; form.elements.system.value = ''; form.requestSubmit();
  });
  document.querySelector('#previous').addEventListener('click', () => { offset = Math.max(0, offset - pageLimit); loadResults(); });
  document.querySelector('#next').addEventListener('click', () => { offset += pageLimit; loadResults(); });
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
      const params = new URLSearchParams({ round: activeRound, galaxy: item.galaxy, system: item.system, orbit: planet.orbit });
      const response = await fetch('prospection-read.php?' + params, { credentials: 'same-origin' });
      if (response.status === 401) { location.assign('login.php'); return; }
      if (response.status === 403) { location.assign('password-change.php'); return; }
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

  function renderSystem(item) {
    const card = element('article');
    const isCurrent = position && String(item.galaxy) === position.galaxy && String(item.system) === position.system;
    if (isCurrent) card.className = 'current';
    const grid = element('div', undefined, 'system-grid');
    const summary = element('aside', undefined, 'system-summary');
    summary.append(element('h2', isCurrent ? 'Aktuelles / letztes System' : 'System'));
    summary.append(element('div', `${item.galaxy}:${item.system}`, 'coordinate'));
    if (item.system_name) summary.append(element('strong', item.system_name));
    summary.append(element('p', `${(item.planets || []).length} Planeten in dieser Auswahl`, 'muted'));
    if (item.last_observed_at) summary.append(element('p', `System beobachtet: ${item.last_observed_at} UTC`, 'muted'));
    grid.append(summary);
    const wrap = element('div', undefined, 'scroll');
    wrap.tabIndex = 0; wrap.setAttribute('role', 'region');
    wrap.setAttribute('aria-label', `Planetenvergleich im System ${item.galaxy}:${item.system}`);
    const table = element('table', undefined, 'comparison');
    const header = table.createTHead().insertRow();
    header.append(element('th', 'Merkmal'));
    const pane = element('section', undefined, 'report-pane'); pane.hidden = true;
    let opened = null;
    const reportPanels = new Map();
    for (const planet of item.planets || []) {
      const th = element('th'); th.scope = 'col';
      if (Number(planet.report_count) > 0) {
        const button = element('button', `★${planet.report_count}`, 'scan'); button.type = 'button';
        button.setAttribute('aria-label', `${planet.report_count} Sondenberichte für ${planet.name || 'Bahn ' + planet.orbit} öffnen`);
        button.setAttribute('aria-expanded', 'false');
        button.addEventListener('click', async () => {
          if (opened === button && !pane.hidden) {
            pane.hidden = true; button.setAttribute('aria-expanded', 'false'); return;
          }
          if (opened) opened.setAttribute('aria-expanded', 'false');
          opened = button; button.setAttribute('aria-expanded', 'true'); pane.hidden = false;
          for (const panel of reportPanels.values()) panel.section.hidden = true;
          let panel = reportPanels.get(button);
          if (!panel) {
            const section = element('section');
            const title = element('h2', `Sondenberichte · ${item.galaxy}:${item.system}:${planet.orbit} · ${planet.name || 'Unbenannt'}`);
            const detail = element('div'); section.append(title, detail); pane.append(section);
            panel = { section, detail, loading: false, loaded: false }; reportPanels.set(button, panel);
          }
          panel.section.hidden = false;
          if (!panel.loaded && !panel.loading) {
            panel.loading = true;
            await showReports(panel.detail, item, planet);
            panel.loaded = !!panel.detail.querySelector('.report');
            panel.loading = false;
          }
        });
        th.append(button);
      }
      th.append(element('span', `${planet.orbit} · ${planet.name || 'Unbenannt'}`)); header.append(th);
    }
    const body = table.createTBody();
    function compareRow(label, getter) {
      const row = body.insertRow(); const th = element('th', label); th.scope = 'row'; row.append(th);
      for (const planet of item.planets || []) row.insertCell().textContent = getter(planet) ?? '–';
    }
    compareRow('Koordinate', p => `${item.galaxy}:${item.system}:${p.orbit}`);
    for (const [label, field] of [['Typ','type'],['Spieler','ruler'],['Allianz','alliance'],['Status','status']]) compareRow(label, p => p[field]);
    compareRow('Planet beobachtet (UTC)', p => p.last_observed_at);
    compareRow('Letzter Sondenbericht (UTC)', p => p.latest_scan?.observed_at);
    compareRow('Sonden', p => p.latest_scan ? `${p.latest_scan.probe_count ?? '?'} · ${p.latest_scan.probe_type_name || p.latest_scan.probe_type_code || 'Typ unbekannt'}` : null);
    const metricNames = new Set((item.planets || []).flatMap(p => (p.latest_scan?.measurements || []).map(m => m.metric_name)));
    for (const name of [...metricNames].sort((a,b) => a.localeCompare(b,'de'))) {
      compareRow(name + ' (%)', p => {
        const metric = (p.latest_scan?.measurements || []).find(m => m.metric_name === name);
        return metric ? number.format(Number(metric.value_percent)) : '–';
      });
    }
    wrap.append(table); grid.append(wrap); card.append(grid);
    if (metricNames.size) card.append(element('p', 'Schätzwerte aus dem jeweils letzten Sondenbericht. ★ öffnet Einzelberichte und Mittelwertauswahl.', 'muted'));
    if (!(item.planets || []).length) wrap.append(element('p','Keine erfassten Planeten.'));
    card.append(pane); return card;
  }
  form.addEventListener('submit', event => {
    event.preventDefault();
    if (!form.reportValidity()) return;
    const galaxy = form.elements.galaxy.value.trim(), system = form.elements.system.value.trim();
    if (system && !galaxy) { status.textContent = 'Bitte für eine Systemsuche auch die Galaxie angeben.'; return; }
    activeParams = new URLSearchParams({ round: activeRound });
    for (const name of filterNames) { const value = form.elements[name].value.trim(); if (value !== '') activeParams.set(name, value); }
    if (galaxy && system !== '') rememberPosition(galaxy, system);
    offset = 0; loadResults();
  });
  async function loadResults() {
    if (searchController) searchController.abort();
    const controller = new AbortController(); searchController = controller;
    document.querySelector('#previous').disabled = true; document.querySelector('#next').disabled = true;
    status.textContent = 'Suche …';
    const params = new URLSearchParams(activeParams); params.set('offset', String(offset));
    try {
      const response = await fetch('galaxy-read.php?' + params, { credentials:'same-origin', signal:controller.signal });
      if (response.status === 401) { location.assign('login.php'); return; }
      if (response.status === 403) { location.assign('password-change.php'); return; }
      const payload = await response.json();
      if (!response.ok || !payload.ok || !Array.isArray(payload.data)) throw new Error('read_failed');
      if (controller !== searchController) return;
      pageLimit = payload.limit;
      const total = Number(payload.total ?? payload.data.length);
      status.textContent = total ? `${total} System(e) gefunden · ${payload.data.reduce((sum,item) => sum + (item.planets || []).length,0)} Planeten auf dieser Seite` : 'Keine erfassten Einträge für diese Suche vorhanden.';
      results.replaceChildren();
      const sorted = [...payload.data].sort((a,b) => {
        const current = x => position && String(x.galaxy) === position.galaxy && String(x.system) === position.system ? 1 : 0;
        return current(b) - current(a);
      });
      for (const item of sorted) results.append(renderSystem(item));
      pages.hidden = total <= pageLimit;
      document.querySelector('#previous').disabled = offset === 0;
      document.querySelector('#next').disabled = !payload.has_more;
      document.querySelector('#page-info').textContent = `Seite ${Math.floor(offset / pageLimit) + 1} von ${Math.max(1,Math.ceil(total / pageLimit))}`;
    } catch (error) {
      if (error.name === 'AbortError' || controller !== searchController) return;
      status.textContent = 'Die Suche konnte nicht geladen werden. Bitte erneut versuchen.';
    }
  }
  const initial = new URLSearchParams(location.search);
  try {
    const saved = JSON.parse(localStorage.getItem('hasa_web_position_v1') || 'null');
    if (saved && validPosition(saved.galaxy,saved.system)) rememberPosition(saved.galaxy,saved.system);
  } catch (_) {}
  if (validPosition(initial.get('galaxy'),initial.get('system'))) rememberPosition(initial.get('galaxy'),initial.get('system'));
  if (position) { form.elements.galaxy.value = position.galaxy; form.elements.system.value = position.system; }
  for (const name of filterNames) if (initial.has(name)) form.elements[name].value = initial.get(name);
  form.requestSubmit();
})();
</script>
</body>
</html>
