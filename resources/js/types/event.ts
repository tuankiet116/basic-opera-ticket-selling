import { TicketClass } from "./ticketClass"

export type EventData = {
    name: string,
    date: string,
    desc: string,
    image: File,
    ticketClasses: Array<TicketClass>
}