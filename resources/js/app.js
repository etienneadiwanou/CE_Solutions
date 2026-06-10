//
import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router'

// Installe Pinia et le Router
const app = createApp(App)
app.use(createPinia())
app.use(router)
app.mount('#app')