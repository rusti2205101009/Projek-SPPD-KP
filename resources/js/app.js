import '@fortawesome/fontawesome-free/css/all.min.css';
import './bootstrap';
import '../css/app.css';

import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import axios from 'axios';
import Multiselect from 'vue-multiselect'
import 'vue-multiselect/dist/vue-multiselect.css'

// createApp(App).mount('#app')
// createApp(App).use(router).mount('#app')

axios.defaults.baseURL = import.meta.env.VITE_API_URL || 'http://localhost:8000';
axios.defaults.withCredentials = true;

function setAxiosToken(token) {
  if (token) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
  } else {
    delete axios.defaults.headers.common['Authorization'];
  }
}

const token = localStorage.getItem('token');
setAxiosToken(token);

const app = createApp(App);

app.use(router);

app.component('Multiselect', Multiselect);

app.mount('#app');

export { setAxiosToken };