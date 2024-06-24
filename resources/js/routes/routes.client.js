import Index from '../pages/Index.vue';
import Book from '../pages/Book.vue';
import ListEvent from '../pages/ListEvent.vue';
import ClientForm from '../pages/ClientForm.vue';
import { getEventAPI } from '../api/event';
import NotFound from '../pages/errors/NotFound.vue';

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
    { path: '/error/notfound', component: NotFound, name: "not-found" },
    { path: "/:pathMatch(.*)*", component: NotFound }
]