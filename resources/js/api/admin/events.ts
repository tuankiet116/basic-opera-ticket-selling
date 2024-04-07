import axiosInstance from "../axios"

export const getListEvent = async () => {
    return await axiosInstance.get("/admin/event/list");
}