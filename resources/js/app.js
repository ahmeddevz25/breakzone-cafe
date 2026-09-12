import './bootstrap';
import { createApp } from 'vue';
import DashboardStats from './components/DashboardStats.vue';

const app = createApp({});

app.component('dashboard-stats', DashboardStats);

app.mount('#app');
