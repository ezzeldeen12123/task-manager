/* eslint-disable import/order */
import '@/@iconify/icons-bundle'
import App from '@/App.vue'
import vuetify from '@/plugins/vuetify'
import { loadFonts } from '@/plugins/webfontloader'
import router from '@/router'
import { isUserLoggedIn } from '@/router/utils'
import '@core-scss/template/index.scss'
import '@layouts/styles/index.scss'
import '@styles/styles.scss'
import { createPinia } from 'pinia'
import { createApp } from 'vue'

loadFonts()


// Create vue app
const app = createApp(App)

// Use plugins
app.use(vuetify)
app.use(createPinia())
app.use(router)

// Set up a navigation guard
router.beforeEach((to, from, next) => {
    const isLoggedIn = isUserLoggedIn();
    if((to.path == '/login' || to.path == '/register') && isLoggedIn) {
        next('/dashboard');
    }

    if (to.meta.requiresAuth && !isLoggedIn) {
        next('/login');
    }
        
    // Example: Redirect to admin dashboard if not an admin
    if (to.meta.requiresAdmin) {
        next();
    }

    // If all conditions are met, allow the navigation
    next();
})

// Mount vue app
app.mount('#app')
