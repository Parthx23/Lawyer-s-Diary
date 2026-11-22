const API = 'api/entries.php';
let entries = [];
let currentLawyer = null;
const $ = (selector) => document.querySelector(selector);
const escapeHtml = (value) => String(value ?? '').replace(/[&<>"']/g, (char) => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[char]));
const localDate = (date) => `${date.getFullYear()}-${String(date.getMonth()+1).padStart(2,'0')}-${String(date.getDate()).padStart(2,'0')}`;
const prettyDate = (value) => new Date(`${value}T00:00:00`).toLocaleDateString(undefined, {month:'short', day:'numeric', year:'numeric'});
const prettyTime = (value) => new Date(`2000-01-01T${value}`).toLocaleTimeString(undefined, {hour:'numeric', minute:'2-digit'});

function render() {
  const query = $('#searchInput').value.trim().toLowerCase();
  const status = $('#statusFilter').value;
  const date = $('#dateFilter').value;
  const filtered = entries.filter((entry) => {
    const matchesText = `${entry.client_name} ${entry.case_title} ${entry.court_name} ${entry.notes}`.toLowerCase().includes(query);
    return matchesText && (!status || entry.status === status) && (!date || entry.hearing_date === date);
  });
  $('#entriesBody').innerHTML = filtered.length ? filtered.map((entry) => `
    <tr>
      <td><div class="date-main">${escapeHtml(prettyDate(entry.hearing_date))}</div><div class="date-sub">${escapeHtml(prettyTime(entry.hearing_time))}</div></td>
      <td><div class="client-main">${escapeHtml(entry.client_name)}</div><div class="client-sub">${escapeHtml(entry.case_title)}</div></td>
      <td class="court-cell">${escapeHtml(entry.court_name)}</td>
      <td><span class="status-pill status-${entry.status.toLowerCase()}">${escapeHtml(entry.status)}</span></td>
      <td><div class="row-actions"><button class="icon-button" data-edit="${entry.id}" aria-label="Edit hearing">Edit</button><button class="icon-button delete" data-delete="${entry.id}" aria-label="Delete hearing">Delete</button></div></td>
    </tr>`).join('') : '<tr><td colspan="5" class="empty-state">No hearings found.</td></tr>';
  $('#resultCount').textContent = `${filtered.length} ${filtered.length === 1 ? 'hearing' : 'hearings'}`;

  const today = localDate(new Date());
  const upcoming = entries.filter((entry) => entry.status === 'Upcoming' && entry.hearing_date >= today);
  $('#upcomingCount').textContent = upcoming.length;
  $('#completedCount').textContent = entries.filter((entry) => entry.status === 'Completed').length;
  const weekEndDate = new Date(); weekEndDate.setDate(weekEndDate.getDate()+7);
  const clients = new Set(upcoming.filter((entry) => entry.hearing_date <= localDate(weekEndDate)).map((entry) => entry.client_name));
  $('#weekCount').textContent = clients.size;
  $('#nextHearings').innerHTML = upcoming.length ? upcoming.slice(0,4).map((entry) => `
    <div class="next-item"><div class="next-date">${escapeHtml(prettyDate(entry.hearing_date))} &middot; ${escapeHtml(prettyTime(entry.hearing_time))}</div><div class="next-client">${escapeHtml(entry.client_name)}</div><div class="next-case">${escapeHtml(entry.case_title)} &middot; ${escapeHtml(entry.court_name)}</div></div>`).join('') : '<div class="next-empty">No upcoming hearings yet.</div>';
}
async function loadEntries() {
  try {
    const response = await fetch(`${API}?lawyer_id=${encodeURIComponent(currentLawyer.id)}`);
    const data = await response.json();
    if (!response.ok) throw Error(data.error || 'Could not load diary.');
    entries = data.entries;
    render();
  } catch (error) {
    $('#entriesBody').innerHTML = `<tr><td colspan="5" class="empty-state">${escapeHtml(error.message || 'Could not load diary.')}</td></tr>`;
    $('#nextHearings').innerHTML = '<div class="next-empty">Could not load hearings.</div>';
  }
}
async function openSelectedDiary(lawyer) {
  currentLawyer = lawyer;
  $('#currentLawyerName').textContent = lawyer.name;
  $('#closedBook').classList.add('opening');
  await new Promise((resolve) => setTimeout(resolve, 900));
  $('#landing').hidden = true;
  $('#workspace').hidden = false;
  await loadEntries();
}
function openModal(entry = null) {
  const form = $('#entryForm'); form.reset(); form.elements.id.value = '';
  $('#formError').textContent = '';
  $('#modalTitle').textContent = entry ? 'Edit hearing' : 'Add hearing';
  $('#saveButton').textContent = entry ? 'Save changes' : 'Save hearing';
  if (entry) Object.keys(entry).forEach((key) => { if (form.elements[key]) form.elements[key].value = entry[key] ?? ''; });
  $('#modalBackdrop').hidden = false;
  form.elements.client_name.focus();
}
function closeModal() { $('#modalBackdrop').hidden = true; }
function toast(message) { const item = $('#toast'); item.textContent = message; item.classList.add('show'); setTimeout(() => item.classList.remove('show'), 2400); }

const now = new Date();
$('#todayLabel').textContent = now.toLocaleDateString(undefined, {weekday:'short', month:'short', day:'numeric'});
$('#dayNumber').textContent = now.getDate();
$('#monthName').textContent = now.toLocaleDateString(undefined, {month:'long'});
$('#weekdayName').textContent = now.toLocaleDateString(undefined, {weekday:'long'});
$('#lawyerNameInput').focus();
$('#lawyerForm').addEventListener('submit', async (event) => {
  event.preventDefault();
  const button = $('#openDiaryButton'); const errorBox = $('#lawyerError');
  errorBox.textContent = ''; button.disabled = true; button.textContent = 'Opening...';
  try {
    const response = await fetch('api/lawyers.php', {method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({name:$('#lawyerNameInput').value.trim()})});
    const data = await response.json();
    if (!response.ok) throw Error(data.error || 'Could not open diary.');
    await openSelectedDiary(data.lawyer);
  } catch (error) {
    errorBox.textContent = error.message || 'Could not open diary. Check the database setup.';
    $('#closedBook').classList.remove('opening');
    button.innerHTML = 'Open diary <span>&rarr;</span>';
  } finally { button.disabled = false; }
});
$('#changeLawyerBtn').addEventListener('click', () => {
  currentLawyer = null; entries = [];
  $('#workspace').hidden = true; $('#landing').hidden = false;
  $('#closedBook').classList.remove('opening');
  $('#lawyerNameInput').value = ''; $('#lawyerError').textContent = '';
  $('#openDiaryButton').innerHTML = 'Open diary <span>&rarr;</span>';
  $('#lawyerNameInput').focus();
});
$('#addEntryBtn').addEventListener('click', () => openModal());
$('#closeModal').addEventListener('click', closeModal); $('#cancelModal').addEventListener('click', closeModal);
$('#modalBackdrop').addEventListener('click', (event) => { if (event.target === $('#modalBackdrop')) closeModal(); });
$('#searchInput').addEventListener('input', render); $('#statusFilter').addEventListener('change', render); $('#dateFilter').addEventListener('change', render);
$('#clearSearch').addEventListener('click', () => { $('#searchInput').value=''; $('#statusFilter').value=''; $('#dateFilter').value=''; render(); });
$('#entriesBody').addEventListener('click', async (event) => {
  const editButton = event.target.closest('[data-edit]'); const deleteButton = event.target.closest('[data-delete]');
  if (editButton) openModal(entries.find((entry) => entry.id == editButton.dataset.edit));
  if (deleteButton && confirm('Delete this hearing?')) {
    const response = await fetch(API, {method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({action:'delete', id:deleteButton.dataset.delete, lawyer_id:currentLawyer.id})});
    const data = await response.json(); if (!response.ok) return toast(data.error || 'Could not delete hearing.');
    toast(data.message); loadEntries();
  }
});
$('#entryForm').addEventListener('submit', async (event) => {
  event.preventDefault(); const form = event.currentTarget; const values = Object.fromEntries(new FormData(form));
  values.action = values.id ? 'update' : 'create'; values.lawyer_id = currentLawyer.id;
  const button = $('#saveButton'); button.disabled = true;
  try {
    const response = await fetch(API, {method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify(values)});
    const data = await response.json(); if (!response.ok) throw Error(data.error || 'Could not save hearing.');
    closeModal(); toast(data.message); await loadEntries();
  } catch (error) { $('#formError').textContent = error.message; }
  finally { button.disabled = false; }
});
