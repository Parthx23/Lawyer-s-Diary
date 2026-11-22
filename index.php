<?php
require_once __DIR__ . '/config.php';
$databaseReady = true;
try { db()->query('SELECT 1 FROM diary_entries LIMIT 1'); } catch (Throwable $error) { $databaseReady = false; }
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Lawyer's Diary</title>
  <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Lora:wght@500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<section class="landing" id="landing">
  <div class="closed-book" id="closedBook">
    <div class="cover-inner">
      <div class="cover-mark">LD</div>
      <div class="eyebrow">A PERSONAL COURT PLANNER</div>
      <h1>Lawyer's Diary</h1>
      <p>Keep every hearing in one place.</p>
      <form id="lawyerForm">
        <label for="lawyerNameInput">Lawyer's name</label>
        <input id="lawyerNameInput" name="name" maxlength="120" required placeholder="Enter your name" autocomplete="name">
        <button class="button cover-button" id="openDiaryButton" type="submit">Open diary <span>&rarr;</span></button>
      </form>
      <div class="cover-help">Enter your name to open or create your diary.</div>
    </div>
    <div class="page-turn" aria-hidden="true"></div>
  </div>
  <div class="landing-error" id="lawyerError" role="alert"></div>
</section>

<main class="main-content" id="workspace" hidden>
  <header class="topbar">
    <a class="brand" href="#"><span class="brand-mark">LD</span><span>Lawyer's Diary<small id="currentLawyerName">HEARING PLANNER</small></span></a>
    <div class="top-actions"><span id="todayLabel"></span><button class="button change-button" id="changeLawyerBtn" type="button">Change lawyer</button><button class="button primary" id="addEntryBtn" type="button"><span>+</span> Add hearing</button></div>
  </header>
  <?php if (!$databaseReady): ?><div class="setup-alert">Database not connected. Check the database settings in <code>config.php</code>.</div><?php endif; ?>
  <section class="book-spread" aria-label="Hearing diary">
    <div class="page page-main">
      <div class="page-heading"><div><div class="eyebrow">YOUR SCHEDULE</div><h1>Hearing diary</h1></div><button class="text-button" id="clearSearch">Clear filters</button></div>
      <div class="toolbar">
        <label class="search-box"><span>⌕</span><input id="searchInput" type="search" placeholder="Search diary"></label>
        <select id="statusFilter" aria-label="Filter by status"><option value="">All status</option><option>Upcoming</option><option>Completed</option><option>Adjourned</option></select>
        <input id="dateFilter" type="date" aria-label="Filter by hearing date">
      </div>
      <div class="table-wrap"><table><thead><tr><th>DATE / TIME</th><th>CLIENT / CASE</th><th>COURT</th><th>STATUS</th><th></th></tr></thead><tbody id="entriesBody"><tr><td colspan="5" class="empty-state">Loading diary...</td></tr></tbody></table></div>
      <div class="table-footer"><span id="resultCount">-</span><span>All hearings</span></div>
    </div>
    <aside class="page page-side">
      <div class="date-stamp"><span id="dayNumber"></span><span><b id="monthName"></b><small id="weekdayName"></small></span></div>
      <div class="eyebrow">AT A GLANCE</div><h2>This week</h2>
      <div class="quick-counts">
        <div><span>Upcoming</span><strong id="upcomingCount">-</strong></div>
        <div><span>Clients</span><strong id="weekCount">-</strong></div>
        <div><span>Completed</span><strong id="completedCount">-</strong></div>
      </div>
      <div class="next-heading"><div class="eyebrow">ON THE CALENDAR</div><h2>Next hearings</h2></div>
      <div class="next-list" id="nextHearings"><div class="next-empty">Your next hearings will appear here.</div></div>
      <p class="page-note">One day at a time.</p>
    </aside>
  </section>
  <footer>Lawyer's Diary <span>&middot;</span> Keep every hearing in view</footer>
</main>
<div class="modal-backdrop" id="modalBackdrop" hidden>
  <section class="modal" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
    <div class="modal-head"><div><div class="eyebrow">DIARY ENTRY</div><h2 id="modalTitle">Add hearing</h2></div><button class="close-button" id="closeModal" aria-label="Close">&times;</button></div>
    <form id="entryForm">
      <input type="hidden" name="id">
      <div class="form-grid">
        <label>Client<input name="client_name" required maxlength="120" placeholder="Client name"></label>
        <label>Case<input name="case_title" required maxlength="180" placeholder="Case or matter"></label>
        <label class="full">Court<input name="court_name" required maxlength="160" placeholder="Court name and number"></label>
        <label>Date<input name="hearing_date" type="date" required></label>
        <label>Time<input name="hearing_time" type="time" required></label>
        <label>Status<select name="status"><option>Upcoming</option><option>Completed</option><option>Adjourned</option></select></label>
        <label class="full">Notes <span class="optional">(optional)</span><textarea name="notes" rows="3" placeholder="Reminder or next step"></textarea></label>
      </div>
      <div class="form-error" id="formError"></div>
      <div class="modal-actions"><button type="button" class="button quiet" id="cancelModal">Cancel</button><button class="button primary" type="submit" id="saveButton">Save hearing</button></div>
    </form>
  </section>
</div>
<div class="toast" id="toast"></div>
<script src="assets/app.js"></script>
</body>
</html>
