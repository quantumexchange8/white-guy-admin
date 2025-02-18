import axios from 'axios';
window.axios = axios;

// Get CSRF token from meta tag
const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// Set CSRF token to Axios headers if it exists
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
}

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
