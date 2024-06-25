import axiosInstance from "../axios"

export const listTicketClassAPI = async (eventId: number) => {
    return await axiosInstance.get(`/admin/ticket-class/${eventId}/list`);
}