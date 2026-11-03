// Shared helper: all frontend pages use this function to talk to the PHP API.
const API_BASE = 'api';

async function apiRequest(endpoint, options = {}) {
  const response = await fetch(`${API_BASE}/${endpoint}`, options);
  const result = await response.json();

  if (!response.ok || result.success === false) {
    throw new Error(result.message || `Request failed (${response.status})`);
  }

  return result;
}

function showMessage(elementId, message, type = '') {
  const element = document.getElementById(elementId);
  element.textContent = message;
  element.className = `message ${type}`;
}

function escapeHTML(value) {
  return String(value ?? '').replace(/[&<>"']/g, character => ({
    '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
  })[character]);
}
