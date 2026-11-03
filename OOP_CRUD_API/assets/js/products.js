const productForm = document.getElementById('product-form');
const productBody = document.getElementById('products-body');
const categorySelect = document.getElementById('category_id');

async function loadCategoriesIntoSelect(selectedId = '') {
  const result = await apiRequest('categories.php');
  categorySelect.innerHTML = '<option value="">Select category</option>';
  result.data.forEach(category => {
    const option = document.createElement('option');
    option.value = category.id;
    option.textContent = category.name;
    if (String(category.id) === String(selectedId)) option.selected = true;
    categorySelect.appendChild(option);
  });
}

async function loadProducts() {
  try {
    showMessage('list-message', 'Loading products…');
    const result = await apiRequest('products.php');
    productBody.innerHTML = '';

    if (result.data.length === 0) {
      productBody.innerHTML = '<tr><td colspan="8">No products yet.</td></tr>';
    }

    result.data.forEach(product => {
      const image = product.image
        ? `<img src="${escapeHTML(product.image)}" alt="Product image">`
        : 'No image';
      const row = document.createElement('tr');
      row.innerHTML = `
        <td>${product.id}</td><td>${image}</td>
        <td>${escapeHTML(product.name)}</td>
        <td>${escapeHTML(product.description || '')}</td>
        <td>${Number(product.price).toFixed(2)}</td><td>${product.quantity}</td>
        <td>${escapeHTML(product.category_name || '')}</td>
        <td>
          <button class="small" data-edit="${product.id}">Edit</button>
          <button class="small danger" data-delete="${product.id}">Delete</button>
        </td>`;
      productBody.appendChild(row);
    });
    showMessage('list-message', '');
  } catch (error) {
    showMessage('list-message', error.message, 'error');
  }
}

function resetProductForm() {
  productForm.reset();
  document.getElementById('product-id').value = '';
  document.getElementById('form-title').textContent = 'Add product';
  document.getElementById('image').required = false;
  showMessage('form-message', '');
}

productForm.addEventListener('submit', async event => {
  event.preventDefault();
  const formData = new FormData();
  const id = document.getElementById('product-id').value;
  formData.append('name', document.getElementById('name').value.trim());
  formData.append('description', document.getElementById('description').value.trim());
  formData.append('price', document.getElementById('price').value);
  formData.append('quantity', document.getElementById('quantity').value);
  formData.append('category_id', categorySelect.value);
  if (id) formData.append('id', id);

  const imageFile = document.getElementById('image').files[0];
  if (imageFile) formData.append('image', imageFile);

  try {
    const result = await apiRequest('products.php', { method: 'POST', body: formData });
    showMessage('form-message', result.message, 'success');
    resetProductForm();
    await loadProducts();
  } catch (error) {
    showMessage('form-message', error.message, 'error');
  }
});

productBody.addEventListener('click', async event => {
  const editId = event.target.dataset.edit;
  const deleteId = event.target.dataset.delete;

  if (editId) {
    try {
      const result = await apiRequest(`products.php?id=${editId}`);
      const product = result.data;
      document.getElementById('product-id').value = product.id;
      document.getElementById('name').value = product.name;
      document.getElementById('description').value = product.description || '';
      document.getElementById('price').value = product.price;
      document.getElementById('quantity').value = product.quantity;
      await loadCategoriesIntoSelect(product.category_id);
      document.getElementById('form-title').textContent = `Edit product #${product.id}`;
      document.getElementById('image').required = false;
      showMessage('form-message', 'Choose a new image only if you want to replace the current one.');
      window.scrollTo({ top: 0, behavior: 'smooth' });
    } catch (error) {
      showMessage('form-message', error.message, 'error');
    }
  }

  if (deleteId && confirm('Delete this product?')) {
    try {
      const result = await apiRequest(`products.php?id=${deleteId}`, { method: 'DELETE' });
      showMessage('list-message', result.message, 'success');
      await loadProducts();
    } catch (error) {
      showMessage('list-message', error.message, 'error');
    }
  }
});

document.getElementById('cancel-edit').addEventListener('click', resetProductForm);

(async function init() {
  try {
    await loadCategoriesIntoSelect();
    await loadProducts();
  } catch (error) {
    showMessage('list-message', error.message, 'error');
  }
})();
