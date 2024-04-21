import Index from './pages/Index.vue';
import Book from './pages/Book.vue';
import ListEvent from './pages/ListEvent.vue';
import Dashboard from './pages/admin/Dashboard.vue';
import LoginAdmin from './pages/admin/LoginAdmin.vue';
import Events from './pages/admin/Events.vue';
import UpsertEvent from './pages/admin/UpsertEvent.vue';
import SeatEvent from './pages/admin/SeatEvent.vue';
import Clients from './pages/admin/Clients.vue';
import UpsertClient from './pages/admin/UpsertClient.vue';

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
            { path: "create-event", component: UpsertEvent, name: 'admin-create-event' },
            {
                path: "event/:eventId", children: [
                    { path: "edit", name: 'admin-edit-event', component: UpsertEvent, props: { isEdit: true } },
                    { path: "seats", name: 'admin-edit-seats', component: SeatEvent },
                ]
            },
            {
                path: "clients", children: [
                    { path: "", name: 'admin-list-client', component: Clients, props: { tabModeSpecial: false } },
                    { path: "create", name: 'admin-create-client', component: UpsertClient },
                    { path: "edit/:clientId", name: 'admin-edit-client', component: UpsertClient, props: { isEdit: true } },
                ]
            }
        ]
    },
    { path: '/admin/login', component: LoginAdmin, name: "admin-login" },
]