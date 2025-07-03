import { createApp } from 'vue'
import './style.css'
import App from './App.vue'
import router from './router'; // <-- Importa el router
import { createPinia } from 'pinia'; // Importa Pinia



const app = createApp(App);

app.use(createPinia());
app.use(router); // <-- Usa el router


app.mount('#app');
