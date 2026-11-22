<?php
require_once __DIR__ . '/config.php';
$databaseReady = true;
try { db()->query('SELECT 1 FROM diary_entries LIMIT 1'); } catch (Throwable $error) { $databaseReady = false; }
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Briefcase — Lawyer's Diary</title>
  <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Lora:wght@500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="app-shell">
  <aside class="sidebar">
    <a class="brand" href="#"><span class="brand-mark">B</span><span>briefcase<small>LEGAL DESK</small></span></a>
    <div class="nav-label">WORKSPACE</div>
    <a class="nav-item active" href="#"><span class="nav-icon">▦</span> Overview</a>
    <div class="sidebar-note"><span class="note-icon">✦</span><p>“The first duty of society is justice.”</p><small>— Alexander Hamilton</small></div>
    <div class="profile"><div class="avatar">AK</div><div><strong>Adv. A. Khanna</strong><small>Independent practice</small></div><span class="dots">···</span></div>
  </aside>
  <main class="main-content">
    <header class="topbar"><div class="breadcrumb">Workspace <span>/</span> Overview</div><div class="top-actions"><span class="today-label" id="todayLabel"></span><div class="top-avatar">AK</div></div></header>
    <section class="welcome-row"><div><div class="eyebrow">YOUR PRACTICE, IN ORDER</div><h1>A clear day starts here.</h1><p class="subheading">Keep every hearing, client, and next step in view.</p></div><button class="button primary" id="addEntryBtn"><span>＋</span> Add diary entry</button></section>
    <?php if (!$databaseReady): ?><div class="setup-alert">Database not connected. Import <code>database.sql</code> in MySQL and check your credentials in <code>config.php</code>.</div><?php endif; ?>
    <section class="stats-grid">
      <article class="stat-card"><div class="stat-top"><span>UPCOMING HEARINGS</span><span class="stat-icon sand">◷</span></div><div class="stat-number" id="upcomingCount">—</div><div class="stat-caption">Scheduled ahead</div></article>
      <article class="stat-card"><div class="stat-top"><span>CLIENTS THIS WEEK</span><span class="stat-icon sage">♧</span></div><div class="stat-number" id="weekCount">—</div><div class="stat-caption">Distinct clients on your calendar</div></article>
      <article class="stat-card"><div class="stat-top"><span>COMPLETED</span><span class="stat-icon blue">✓</span></div><div class="stat-number" id="completedCount">—</div><div class="stat-caption">Matters marked complete</div></article>
    </section>
    <section class="diary-section" id="diary">
      <div class="section-heading"><div><div class="eyebrow">YOUR CALENDAR</div><h2>Hearing diary</h2></div><button class="text-button" id="clearSearch">Clear filters <span>↗</span></button></div>
      <div class="toolbar"><label class="search-box"><span>⌕</span><input id="searchInput" type="search" placeholder="Search clients, cases, courts..."></label><select id="statusFilter"><option value="">All statuses</option><option>Upcoming</option><option>Completed</option><option>Adjourned</option></select><input id="dateFilter" type="date" aria-label="Filter by hearing date"></div>
      <div class="table-wrap"><table><thead><tr><th>DATE & TIME</th><th>CLIENT / MATTER</th><th>COURT</th><th>STATUS</th><th></th></tr></thead><tbody id="entriesBody"><tr><td colspan="5" class="empty-state">Loading your diary…</td></tr></tbody></table></div>
      <div class="table-footer"><span id="resultCount">—</span><span>Showing all diary entries</span></div>
    </section>
    <footer>Briefcase <span>·</span> A calmer way to run your practice</footer>
  </main>
</div>
<div class="modal-backdrop" id="modalBackdrop" hidden><section class="modal" role="dialog" aria-modal="true" aria-labelledby="modalTitle"><div class="modal-head"><div><div class="eyebrow">HEARING DETAILS</div><h2 id="modalTitle">Add diary entry</h2></div><button class="close-button" id="closeModal" aria-label="Close">×</button></div><form id="entryForm"><input type="hidden" name="id"><div class="form-grid"><label>Client name<input name="client_name" required maxlength="120" placeholder="e.g. Aarav Mehta"></label><label>Case / matter<input name="case_title" required maxlength="180" placeholder="e.g. Mehta vs. Sharma"></label><label class="full">Court<input name="court_name" required maxlength="160" placeholder="e.g. City Civil Court · Court 4"></label><label>Hearing date<input name="hearing_date" type="date" required></label><label>Hearing time<input name="hearing_time" type="time" required></label><label>Status<select name="status"><option>Upcoming</option><option>Completed</option><option>Adjourned</option></select></label><label class="full">Notes <span class="optional">(optional)</span><textarea name="notes" rows="3" placeholder="Preparation reminders or next steps…"></textarea></label></div><div class="form-error" id="formError"></div><div class="modal-actions"><button type="button" class="button quiet" id="cancelModal">Cancel</button><button class="button primary" type="submit" id="saveButton">Save entry <span>→</span></button></div></form></section></div>
<div class="toast" id="toast"></div>
<script src="assets/app.js"></script>
</body></html>
