import { Tooltip } from "bootstrap";
import { SeatFormatted } from "../types/seats";
import { MODE_TICKET_CLASS_SETTING } from "../config/const";

export const setStyleSeatByTicketClass = (seat: SeatFormatted, seatTicketClasses: any, bookings: Array<any>, hallId: number, mode: string) => {
    let style = `margin-top: ${seat.marginTop}px !important; width: ${seat.width}px; height: ${seat.height}px;`;
    let config = seatTicketClasses.find((ticketClass: any) => {
        return ticketClass.seat.name == seat.name && ticketClass.seat.hall == hallId;
    });
    let booking = bookingStatus(seat, bookings, hallId);
    if (booking && mode != MODE_TICKET_CLASS_SETTING) {
        if (booking.textcolor) style += `color: ${booking.textcolor};`;
        style += `background-color: ${booking.color};`;
    } else if (config) {
        style += `background-color: ${config.ticket_class.color};`;
    }
    return style;
}

const setClassName = (seat: SeatFormatted, seatTicketClasses: any, bookings: Array<any>, hallId: number, mode: string) => {
    let className = isSeatInSelected(seat.id, props.selected) ? 'selected' : '';
    let booking = bookingStatus(seat, props.bookings, 1);
    console.log(booking)
    if (booking && booking.disable) {
        
        className +='booking';
    }
    return className;
}

export const bookingStatus = (seat: SeatFormatted, bookings: Array<any>, hallId: number) => {
    return bookings.find(booking => booking.seat == seat.name && booking.hall == hallId);
}

export const isSeatInSelected = (seatId?: string|number, selectedSeats?: Array<string>) => {
    return selectedSeats?.find(v => v == seatId);
}

export const makeToolTipData = (seat: SeatFormatted, bookings: Array<any>, hallId: number) => {
    let booking = bookingStatus(seat, bookings, hallId);
    if (booking && booking.client) return {
        "title": booking.client.name
    };
    return {};
}