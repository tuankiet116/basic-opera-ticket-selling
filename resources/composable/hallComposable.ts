import { Tooltip } from "bootstrap";
import { SeatFormatted } from "../js/types/seats";

export const setStyleSeatByTicketClass = (seat: SeatFormatted, seatTicketClasses: any, bookings: Array<any>, hallId: number) => {
    let style = `margin-top: ${seat.marginTop}px !important; width: ${seat.width}px; height: ${seat.height}px;`;
    let config = seatTicketClasses.find((ticketClass: any) => {
        return ticketClass.seat.name == seat.name && ticketClass.seat.hall == hallId;
    });
    let booking = bookingStatus(seat, bookings, hallId);
    if (booking) {
        style += `background-color: ${booking.color}`;
    } else if (config) {
        style += `background-color: ${config.ticket_class.color}`;
    }
    return style;
}

export const bookingStatus = (seat: SeatFormatted, bookings: Array<any>, hallId: number) => {
    return bookings.find(booking => booking.seat == seat.name && booking.hall == hallId);
}

export const isSeatInSelected = (seatId: string, selectedSeats: Array<string>) => {
    return selectedSeats.find(v => v == seatId);
}

export const makeToolTipData = (seat: SeatFormatted, bookings: Array<any>, hallId: number) => {
    let booking = bookingStatus(seat, bookings, hallId);
    if (booking && booking.client) return {
        "title": booking.client.name
    };
    return {};
}