import Index from './pages/Index.vue';
import Book from './pages/Book.vue';
import ListEvent from './pages/ListEvent.vue';
import Dashboard from './pages/admin/Dashboard.vue';
import LoginAdmin from './pages/admin/LoginAdmin.vue';
import Events from './pages/admin/Events.vue';
import CreateEvent from './pages/admin/CreateEvent.vue';

export const routes = [
    {
        path: '/', component: Index, children: [
            { path: '', component: ListEvent, name: 'index' },
            { path: '/book/:eventId', component: Book, name: 'book-ticket' },
        ]
    },
    {
        path: '/admin', component: Dashboard, name: "admin", children: [
            { path: "", component: Events, name: 'admin-list-events' },
            { path: "create-event", component: CreateEvent, name: 'admin-create-event' },
            {
                path: "event/:eventId", children: [
                    { path: "edit", name: 'admin-edit-event', component: CreateEvent, props: { isEdit: true } }
                ]
            }
        ]
    },
    { path: '/admin/login', component: LoginAdmin, name: "admin-login" },
]