import axiosInstance from "../axios"

export const exportReportAPI = async () => {
    return await axiosInstance.get("/admin/export/users");
}