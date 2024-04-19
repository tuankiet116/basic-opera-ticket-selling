import { SeatsTicketClassData } from "../../types/seats";
import axiosInstance from "../axios"

export const setTicketClassAPI = async (data: SeatsTicketClassData) => {
    return await axiosInstance.post("/admin/seats/set-ticket-class", data);
}

export const getTicketClassAPI = async (eventId) => {
    return await axiosInstance.get(`/admin/seats/get-ticket-class/${eventId}`);
}