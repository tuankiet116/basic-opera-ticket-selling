import { EventData } from "../../types/event";
import axiosInstance from "../axios"

export const getListEvent = async () => {
    return await axiosInstance.get("/admin/event/list");
}

export const createEventAPI = async (data: EventData) => {
    return await axiosInstance.post("/admin/event/create", data, {
        headers: {
            'content-type': 'multipart/form-data'
        }
    });
}