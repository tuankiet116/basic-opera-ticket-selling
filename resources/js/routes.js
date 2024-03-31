import Index from './pages/Index.vue';
import Book from './pages/Book.vue';

export const routes = [
    { path: '/', component: Index, name: "index" },
    { path: '/book/:eventId', component: Book, name: 'book-ticket' },
]