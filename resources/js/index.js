import 'bootstrap';
import { createApp } from 'vue';
import { createMemoryHistory, createRouter } from 'vue-router'
import { routes } from './routes';
import App from './App.vue';
import lazyload from './directives/lazyload';

const router = createRouter({
    history: createMemoryHistory(),
    routes,
})

let app = createApp(App).use(router);
app.directive("lazyload", lazyload);
app.mount("#app");