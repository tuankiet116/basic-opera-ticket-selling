import axiosInstance from "../axios"

export const getListBookingsAPI = async (eventId: number, pageNumber: number, searchString = "") => {
    return await axiosInstance.get(`/admin/bookings/list/${eventId}?page=${pageNumber}&search=${searchString}`);
}

export const acceptBookingAPI = async (eventId: number, clientId: number) => {
    return await axiosInstance.put(`/admin/bookings/accept`, {
        event_id: eventId,
        client_id: clientId
    });
}