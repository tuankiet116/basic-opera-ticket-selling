import 'bootstrap';
import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router'
import { routes } from './routes';
import App from './App.vue';
import lazyload from './directives/lazyload';
import { isLoggedInAPI } from './api/admin/auth';
import { HTTP_SUCCESS, UNAUTHORIZED } from './config/const';
import { createPinia } from 'pinia';
import { useAuthenticateStore } from './pinia';
import Toast from 'vue-toastification';
import "vue-toastification/dist/index.css";
import { popover, tooltip } from "./directives/bootstrap";

const pinia = createPinia();
const authenticatedStore = useAuthenticateStore(pinia);
const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach(async (to, from, next) => {
    const isAuthenticated = authenticatedStore.isAdminLoggedIn;
    if (to.matched.find(value => value.name == "admin" && !isAuthenticated)) {
        let result = await isLoggedInAPI();
        if (result.status == HTTP_SUCCESS) {
            authenticatedStore.setAdminLoggedIn();
            next();
        } else {
            next({ name: "admin-login" });
        }
    } else if (to.name == "admin-login") {
        let result = await isLoggedInAPI();
        if (result.status == HTTP_SUCCESS) {
            authenticatedStore.setAdminLoggedIn();
            next({ name: "admin-list-events" });
        } else {
            next();
        }
    } else {
        next();
    }
});

let app = createApp(App)
    .use(router).use(pinia).use(Toast, {
        timeout: 3000,
        position: "top-right",
        closeOnClick: true,
        pauseOnHover: true,
        draggable: true,
        pauseOnFocusLoss: true,
    });
app.directive("lazyload", lazyload);
app.directive("popover", popover);
app.directive("tooltip", tooltip);
app.mount("#app");