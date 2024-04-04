import Index from './pages/Index.vue';
import Book from './pages/Book.vue';
import ListEvent from './pages/ListEvent.vue';
import Dashboard from './pages/admin/Dashboard.vue';
import LoginAdmin from './pages/admin/LoginAdmin.vue';
import { adminRoutes } from './adminRoutes';

export const routes = [
    {
        path: '/', component: Index, children: [
            { path: '/', component: ListEvent, name: 'index' },
            { path: '/book/:eventId', component: Book, name: 'book-ticket' },
        ]
    },
    { path: '/admin', component: Dashboard, children: adminRoutes },
    { path: '/admin/login', component: LoginAdmin, name: "admin-login" },
]