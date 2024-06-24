import Dashboard from '../pages/admin/Dashboard.vue';
import LoginAdmin from '../pages/admin/LoginAdmin.vue';
import Events from '../pages/admin/events/Events.vue';
import UpsertEvent from '../pages/admin/events/UpsertEvent.vue';
import SeatEvent from '../pages/admin/events/SeatEvent.vue';
import Clients from '../pages/admin/clients/Clients.vue';
import UpsertClient from '../pages/admin/clients/UpsertClient.vue';
import IndexBooking from '../pages/admin/bookings/Index.vue';
import Booking from '../pages/admin/bookings/Booking.vue';
import Files from '../pages/admin/files/Files.vue';

export const routes = [
    {
        path: '/admin', component: Dashboard, name: "admin", children: [
            { path: "", component: Events, name: 'admin-list-events' },
            { path: "create-event", component: UpsertEvent, name: 'admin-create-event' },
            { path: "files", component: Files, name: 'admin-list-file' },
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
]