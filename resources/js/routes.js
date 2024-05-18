import Index from './pages/Index.vue';
import Book from './pages/Book.vue';
import ListEvent from './pages/ListEvent.vue';
import Dashboard from './pages/admin/Dashboard.vue';
import LoginAdmin from './pages/admin/LoginAdmin.vue';
import Events from './pages/admin/events/Events.vue';
import UpsertEvent from './pages/admin/events/UpsertEvent.vue';
import SeatEvent from './pages/admin/events/SeatEvent.vue';
import Clients from './pages/admin/clients/Clients.vue';
import UpsertClient from './pages/admin/clients/UpsertClient.vue';
import ClientForm from './pages/ClientForm.vue';
import { getEventAPI } from './api/event';
import NotFound from './pages/errors/NotFound.vue';
import IndexBooking from './pages/admin/bookings/Index.vue';
import Booking from './pages/admin/bookings/Booking.vue';

export const routes = [
    {
        path: '/', component: Index, children: [
            { path: '', component: ListEvent, name: 'index' },
            {
                path: '/book/:eventId', component: Book, name: 'book-ticket', beforeEnter: async (to, from, next) => {
                    if (!to.params.eventId.match("^\\d+$") || !getEventAPI(to.params.eventId)) {
                        next("/error/notfound");
                    }
                    else {
                        next();
                    }
                }
            },
            { path: '/form', component: ClientForm, name: 'client-form' },
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
            },
            {
                path: "bookings", children: [
                    { path: "", name: "admin-list-booking", component: IndexBooking },
                    { path: ":eventId", name: "admin-list-booking-event", component: Booking }
                ]
            }
        ]
    },
    { path: '/admin/login', component: LoginAdmin, name: "admin-login" },
    { path: '/error/notfound', component: NotFound, name: "not-found" },
    { path: "/:pathMatch(.*)*", component: NotFound }
]