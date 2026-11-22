const API = 'api/entries.php';
let entries = [];
const $ = (selector) => document.querySelector(selector);
const escapeHtml = (value) => String(value ?? '').replace(/[&<>"']/g, (char) => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[char]));
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
      <td><div class="row-actions"><button class="icon-button" data-edit="${entry.id}" aria-label="Edit entry">Edit</button><button class="icon-button delete" data-delete="${entry.id}" aria-label="Delete entry">Delete</button></div></td>
    </tr>`).join('') : '<tr><td colspan="5" class="empty-state">No entries match your filters.</td></tr>';
  $('#resultCount').textContent = `${filtered.length} ${filtered.length === 1 ? 'entry' : 'entries'}`;
  const today = new Date().toISOString().slice(0,10);
  $('#upcomingCount').textContent = entries.filter((e) => e.status === 'Upcoming' && e.hearing_date >= today).length;
  $('#completedCount').textContent = entries.filter((e) => e.status === 'Completed').length;
  const weekEnd = new Date(); weekEnd.setDate(weekEnd.getDate()+7);
  const clients = new Set(entries.filter((e) => e.status === 'Upcoming' && e.hearing_date >= today && e.hearing_date <= weekEnd.toISOString().slice(0,10)).map((e) => e.client_name));
  $('#weekCount').textContent = clients.size;
}
async function loadEntries() {
  try { const response = await fetch(API); const data = await response.json(); if (!response.ok) throw Error(data.error); entries = data.entries; render(); }
  catch { $('#entriesBody').innerHTML = '<tr><td colspan="5" class="empty-state">Could not load entries. Check your database connection.</td></tr>'; }
}
function openModal(entry = null) {
  const form = $('#entryForm'); form.reset(); form.elements.id.value = '';
  $('#formError').textContent = '';
  $('#modalTitle').textContent = entry ? 'Edit diary entry' : 'Add diary entry';
  $('#saveButton').innerHTML = entry ? 'Save changes <span>→</span>' : 'Save entry <span>→</span>';
  if (entry) Object.keys(entry).forEach((key) => { if (form.elements[key]) form.elements[key].value = entry[key] ?? ''; });
  $('#modalBackdrop').hidden = false;
  form.elements.client_name.focus();
}
function closeModal() { $('#modalBackdrop').hidden = true; }
function toast(message) { const item = $('#toast'); item.textContent = message; item.classList.add('show'); setTimeout(() => item.classList.remove('show'), 2400); }

$('#todayLabel').textContent = new Date().toLocaleDateString(undefined, {weekday:'short', month:'short', day:'numeric', year:'numeric'});
$('#addEntryBtn').addEventListener('click', () => openModal());
$('#closeModal').addEventListener('click', closeModal); $('#cancelModal').addEventListener('click', closeModal);
$('#modalBackdrop').addEventListener('click', (event) => { if (event.target === $('#modalBackdrop')) closeModal(); });
$('#searchInput').addEventListener('input', render); $('#statusFilter').addEventListener('change', render); $('#dateFilter').addEventListener('change', render);
$('#clearSearch').addEventListener('click', () => { $('#searchInput').value=''; $('#statusFilter').value=''; $('#dateFilter').value=''; render(); });
$('#entriesBody').addEventListener('click', async (event) => {
  const editButton = event.target.closest('[data-edit]'); const deleteButton = event.target.closest('[data-delete]');
  if (editButton) openModal(entries.find((entry) => entry.id == editButton.dataset.edit));
  if (deleteButton && confirm('Delete this diary entry?')) {
    const response = await fetch(API, {method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({action:'delete', id:deleteButton.dataset.delete})});
    const data = await response.json(); if (!response.ok) return toast(data.error || 'Could not delete entry.');
    toast(data.message); loadEntries();
  }
});
$('#entryForm').addEventListener('submit', async (event) => {
  event.preventDefault(); const form = event.currentTarget; const values = Object.fromEntries(new FormData(form));
  values.action = values.id ? 'update' : 'create';
  const button = $('#saveButton'); button.disabled = true;
  try {
    const response = await fetch(API, {method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify(values)});
    const data = await response.json(); if (!response.ok) throw Error(data.error || 'Could not save entry.');
    closeModal(); toast(data.message); await loadEntries();
  } catch (error) { $('#formError').textContent = error.message; }
  finally { button.disabled = false; }
});
loadEntries();
