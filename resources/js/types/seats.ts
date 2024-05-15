export type SideConfig = {
    start?: number,
    end?: number
}

export type RowConfig = {
    row: string,
    startMargin?: number,
    max: number,
    leftSide?: SideConfig,
    rightSide?: SideConfig,
    centerSide?: SideConfig,
    empty?: Array<number>,
    walls?: [
        {
            height?: number
            start: number,
            size: number,
            marginTop?: number
        }
    ]
}

export type SeatFormatted = {
    id?: string | null,
    name?: string | null,
    marginTop?: number,
    isWall?: boolean,
    wallWidth?: number,
    width: number,
    height: number
}

export type SeatsTicketClassData = {
    event_id: number,
    ticket_class_id: number,
    seats: Array<{
        hall: number,
        names: Array<string>
    }>
}

export type PreBookingData = {
    event_id: number,
    client_id: number,
    seats: Array<{
        hall: number,
        names: Array<string>
    }>,
    isCancel: boolean
}

export type AdminBookingStatus = {
    seat: string,
    hall: number,
    disable: boolean,
    color: string
}