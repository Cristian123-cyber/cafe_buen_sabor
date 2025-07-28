import { createApp } from 'vue'
import './style.css'
import App from './App.vue'
import { createPinia } from 'pinia'; // Importa Pinia



const app = createApp(App);
const pinia = createPinia()
app.use(pinia); // <-- Usa Pinia


import router from './router'; // <-- Importa el router
app.use(router); // <-- Usa el router


app.mount('#app');
