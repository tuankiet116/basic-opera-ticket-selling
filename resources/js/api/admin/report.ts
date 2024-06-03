import axiosInstance from "../axios"

type DataReport = {
    events: Array<number>,
}

export const createReportAPI = async (data: DataReport) => {
    return await axiosInstance.post("/admin/report/aggregate", data);
}

export const getListFileAPI = async (page: number) => {
    return await axiosInstance.get(`/admin/files/list?page=${page}`);
}

export const deleteFileAPI = async (fileId: number) => {
    return await axiosInstance.delete(`/admin/files/delete/${fileId}`);
}