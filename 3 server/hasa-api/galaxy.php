<?php
declare(strict_types=1);
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
  input { width: 9rem; background: #fff; color: #111827; }
  button { background: #2563eb; color: white; cursor: pointer; border-color: #2563eb; }
  button:focus-visible, input:focus-visible { outline: 3px solid #facc15; outline-offset: 2px; }
  #status { margin: 1rem 0; min-height: 1.5rem; }
  article { background: #1f2937; margin: 1rem 0; padding: 1rem; border-radius: .7rem; }
  h2 { margin: 0 0 .5rem; font-size: 1.2rem; }
  .scroll { overflow-x: auto; }
  table { border-collapse: collapse; width: 100%; min-width: 680px; }
  th, td { padding: .45rem; border-bottom: 1px solid #4b5563; text-align: left; }
  th { color: #bfdbfe; }
</style>
</head>
<body>
<h1>HASA – Galaxiedatenbank</h1>
<p>Erste Stufe: In den Galaxien 1 bis 6 sind alle erfassten Systeme und Planeten für jeden lesbar.</p>
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
    status.textContent = 'Lade Einträge aus den Galaxien 1 bis 6 …';
    try {
      const response = await fetch('galaxy-read.php?' + params, { credentials: 'omit' });
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
      status.textContent = 'Die Galaxiedatenbank ist derzeit nicht erreichbar.';
    }
  });
})();
</script>
</body>
</html>
