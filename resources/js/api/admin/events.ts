import { EventData } from "../../types/event";
import axiosInstance from "../axios"

export const getListEvent = async (page = 1, searchString: string = "") => {
    return await axiosInstance.get(`/admin/event/list?page=${page}&search=${searchString}`);
}

export const createEventAPI = async (data: EventData) => {
    return await axiosInstance.post("/admin/event/create", data, {
        headers: {
            'content-type': 'multipart/form-data'
        }
    });
}

export const updateEventAPI = async (data: EventData, eventId) => {
    return await axiosInstance.post(`/admin/event/update/${eventId}?_method=PUT`, data, {
        headers: {
            'content-type': 'multipart/form-data'
        }
    });
}

export const getEventAPI = async (eventId: number) => {
    return await axiosInstance.get(`/admin/event/edit/${eventId}`);
}