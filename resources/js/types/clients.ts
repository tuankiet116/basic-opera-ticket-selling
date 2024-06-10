export type ClientData = {
    name: string,
    phone_number: string,
    address: string,
    email: string
};

export type BookingData = {
    event_id: number,
    name: string,
    email: string,
    phone_number: string,
    is_receive_in_opera: boolean,
    address: string,
    bookings: Array<{
        hall: number,
        seats: Array<string>
    }>,
    "g-recaptcha-response": string
}

export type TemporaryBookingData = {
    event_id: number,
    seat: string,
    hall: number,
    "g-recaptcha-response": string
}