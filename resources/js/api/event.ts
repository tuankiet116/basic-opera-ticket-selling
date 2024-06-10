import { BookingData, TemporaryBookingData } from "../types/clients";
import axiosInstance from "./axios";

export const getListEventAPI = async (page = 1) => {
    return await axiosInstance.get(`/event/list?page=${page}`);
}

export const getTicketClassesByEventAPI = async (eventId: number) => {
    return await axiosInstance.get(`/ticket-classes/${eventId}`);
}

export const getEventAPI = async (eventId: number) => {
    return await axiosInstance.get(`/event/info/${eventId}`);
}

export const getBookingsAPI = async (eventId: number) => {
    return await axiosInstance.get(`/event/bookings/${eventId}`);
}

export const bookingAPI = async (data: BookingData) => {
    return await axiosInstance.post(`/booking`, data);
}

export const temporaryBookingAPI = async (data: TemporaryBookingData) => {
    return await axiosInstance.post(`/temporary-booking`, data);
}