import { SeatFormatted } from "../types/seats";
import { MODE_PRE_BOOKING, MODE_TICKET_CLASS_SETTING } from "../config/const";

export const setStyleSeatByTicketClass = (seat: SeatFormatted, seatTicketClasses: any, hallId: number) => {
    let style = `margin-top: ${seat.marginTop}px !important; width: ${seat.width}px; height: ${seat.height}px;`;
    let config = seatTicketClasses.find((ticketClass: any) => {
        return ticketClass.seat.name == seat.name && ticketClass.seat.hall == hallId;
    });
    if (config)  style += `background-color: ${config.ticket_class.color};`;
    return style;
}

export const setSeatClassName = (seat: SeatFormatted, selectedSeats: Array<string>, seatTicketClasses: any, bookings: Array<any>, mode: string, hallId: number) => {
    let className = isSeatInSelected(seat.id, selectedSeats) ? 'selected' : '';
    let booking = bookingStatus(seat, bookings, hallId);
    let config = seatTicketClasses.find((ticketClass: any) => {
        return ticketClass.seat.name == seat.name && ticketClass.seat.hall == hallId;
    });
    if (mode == MODE_TICKET_CLASS_SETTING && booking && booking.disable) {
        className += " admin-seat-hover-booked";
    } else if (mode == MODE_PRE_BOOKING && booking) {
        className += booking.client?.isSpecial ? " admin-seat-booked-special" : " admin-seat-booked";
    } else if (booking && !mode) className += " seat-unselectable";
    else if ((!mode && config) || mode) className += " seat-selectable";
    else className += " seat-unselectable";
    return className;
}

export const bookingStatus = (seat: SeatFormatted, bookings: Array<any>, hallId: number) => {
    return bookings.find(booking => booking.seat == seat.name && booking.hall == hallId);
}

export const isSeatInSelected = (seatId?: string | number, selectedSeats?: Array<string>) => {
    return selectedSeats?.find(v => v == seatId);
}

export const makeToolTipData = (seat: SeatFormatted, bookings: Array<any>, hallId: number) => {
    let booking = bookingStatus(seat, bookings, hallId);
    if (booking && booking.client) return {
        "title": booking.client.name
    };
    return {};
}