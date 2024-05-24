import axiosInstance from "../axios"

export const exportReportAPI = async () => {
    return await axiosInstance.get("/api/export/users");
}