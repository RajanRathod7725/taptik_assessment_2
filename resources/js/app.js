import './bootstrap';
import './scripts.js';
import 'bootstrap/dist/css/bootstrap.min.css'
import 'bootstrap/dist/js/bootstrap.bundle.min.js'

import { createApp } from 'vue/dist/vue.esm-bundler';

const app = createApp({});

import calendar from './pages/calendar.vue';
app.component('calendar', calendar);


app.mount('#app');
