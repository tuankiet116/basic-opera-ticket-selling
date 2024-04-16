import { TicketClass } from "./ticketClass"

export type EventData = {
    name: string,
    date: string,
    description: string,
    image: any | File,
    ticketClasses: Array<TicketClass>
}