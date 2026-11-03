const categoryForm = document.getElementById('category-form');
const categoryBody = document.getElementById('categories-body');

async function loadCategories() {
  try {
    showMessage('list-message', 'Loading categories…');
    const result = await apiRequest('categories.php');
    categoryBody.innerHTML = '';

    if (result.data.length === 0) categoryBody.innerHTML = '<tr><td colspan="4">No categories yet.</td></tr>';

    result.data.forEach(category => {
      const row = document.createElement('tr');
      row.innerHTML = `<td>${category.id}</td><td>${escapeHTML(category.name)}</td>
        <td>${escapeHTML(category.description || '')}</td>
        <td><button class="small" data-edit="${category.id}">Edit</button>
        <button class="small danger" data-delete="${category.id}">Delete</button></td>`;
      categoryBody.appendChild(row);
    });
    showMessage('list-message', '');
  } catch (error) {
    showMessage('list-message', error.message, 'error');
  }
}

function resetCategoryForm() {
  categoryForm.reset();
  document.getElementById('category-id').value = '';
  document.getElementById('form-title').textContent = 'Add category';
  showMessage('form-message', '');
}

categoryForm.addEventListener('submit', async event => {
  event.preventDefault();
  const id = document.getElementById('category-id').value;
  const payload = {
    name: document.getElementById('name').value.trim(),
    description: document.getElementById('description').value.trim()
  };
  if (id) payload.id = Number(id);

  try {
    const result = await apiRequest('categories.php', {
      method: id ? 'PUT' : 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    });
    showMessage('form-message', result.message, 'success');
    resetCategoryForm();
    await loadCategories();
  } catch (error) {
    showMessage('form-message', error.message, 'error');
  }
});

categoryBody.addEventListener('click', async event => {
  const editId = event.target.dataset.edit;
  const deleteId = event.target.dataset.delete;

  if (editId) {
    try {
      const result = await apiRequest(`categories.php?id=${editId}`);
      document.getElementById('category-id').value = result.data.id;
      document.getElementById('name').value = result.data.name;
      document.getElementById('description').value = result.data.description || '';
      document.getElementById('form-title').textContent = `Edit category #${result.data.id}`;
      window.scrollTo({ top: 0, behavior: 'smooth' });
    } catch (error) {
      showMessage('form-message', error.message, 'error');
    }
  }

  if (deleteId && confirm('Delete this category?')) {
    try {
      const result = await apiRequest(`categories.php?id=${deleteId}`, { method: 'DELETE' });
      showMessage('list-message', result.message, 'success');
      await loadCategories();
    } catch (error) {
      showMessage('list-message', error.message, 'error');
    }
  }
});

document.getElementById('cancel-edit').addEventListener('click', resetCategoryForm);
loadCategories();
