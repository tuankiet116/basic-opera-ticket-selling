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
import { VueReCaptcha } from 'vue-recaptcha-v3';
import "./echo.js";
import { createI18n } from 'vue-i18n';
import * as VI from "./lang/vi.json";
import * as EN from "./lang/en.json";

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

const i18n = createI18n({
    locale: 'vi',
    fallbackLocale: 'en',
    messages: {
        en: EN,
        vi: VI
    },
    legacy: false,
    globalInjection: true
});

let app = createApp(App)
    .use(i18n)
    .use(router).use(pinia).use(Toast, {
        timeout: 3000,
        position: "top-right",
        closeOnClick: true,
        pauseOnHover: true,
        draggable: true,
        pauseOnFocusLoss: true,
    }).use(VueReCaptcha, {
        siteKey: '6Lf5Z8wpAAAAAPR2ikZrfMVoxTw6YgGX6QDt3szj',
        loaderOptions: {
            useRecaptchaNet: true
        }
    });
app.directive("lazyload", lazyload);
app.directive("popover", popover);
app.directive("tooltip", tooltip);
app.mount("#app");