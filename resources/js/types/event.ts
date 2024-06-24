import { TicketClass } from "./ticketClass"

export type EventData = {
    name: string,
    date: string,
    description: string,
    image?: any | File,
    banking_code: string,
    ticketClasses: Array<TicketClass>
}