const userForm = document.getElementById('user-form');
const userBody = document.getElementById('users-body');

async function loadUsers() {
  try {
    showMessage('list-message', 'Loading users…');
    const result = await apiRequest('users.php');
    userBody.innerHTML = '';

    if (result.data.length === 0) userBody.innerHTML = '<tr><td colspan="4">No users yet.</td></tr>';

    result.data.forEach(user => {
      const row = document.createElement('tr');
      row.innerHTML = `<td>${user.id}</td><td>${escapeHTML(user.name)}</td><td>${escapeHTML(user.email)}</td>
        <td><button class="small" data-edit="${user.id}">Edit</button>
        <button class="small danger" data-delete="${user.id}">Delete</button></td>`;
      userBody.appendChild(row);
    });
    showMessage('list-message', '');
  } catch (error) {
    showMessage('list-message', error.message, 'error');
  }
}

function resetUserForm() {
  userForm.reset();
  document.getElementById('user-id').value = '';
  document.getElementById('form-title').textContent = 'Add user';
  document.getElementById('password').required = true;
  showMessage('form-message', '');
}

userForm.addEventListener('submit', async event => {
  event.preventDefault();
  const id = document.getElementById('user-id').value;
  const payload = {
    name: document.getElementById('name').value.trim(),
    email: document.getElementById('email').value.trim(),
    password: document.getElementById('password').value
  };
  if (id) payload.id = Number(id);

  try {
    const result = await apiRequest('users.php', {
      method: id ? 'PUT' : 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    });
    showMessage('form-message', result.message, 'success');
    resetUserForm();
    await loadUsers();
  } catch (error) {
    showMessage('form-message', error.message, 'error');
  }
});

userBody.addEventListener('click', async event => {
  const editId = event.target.dataset.edit;
  const deleteId = event.target.dataset.delete;

  if (editId) {
    try {
      const result = await apiRequest(`users.php?id=${editId}`);
      document.getElementById('user-id').value = result.data.id;
      document.getElementById('name').value = result.data.name;
      document.getElementById('email').value = result.data.email;
      document.getElementById('password').value = '';
      document.getElementById('password').required = false;
      document.getElementById('form-title').textContent = `Edit user #${result.data.id}`;
      showMessage('form-message', 'Leave password blank to keep the existing password.');
      window.scrollTo({ top: 0, behavior: 'smooth' });
    } catch (error) {
      showMessage('form-message', error.message, 'error');
    }
  }

  if (deleteId && confirm('Delete this user?')) {
    try {
      const result = await apiRequest(`users.php?id=${deleteId}`, { method: 'DELETE' });
      showMessage('list-message', result.message, 'success');
      await loadUsers();
    } catch (error) {
      showMessage('list-message', error.message, 'error');
    }
  }
});

document.getElementById('cancel-edit').addEventListener('click', resetUserForm);
resetUserForm();
loadUsers();
