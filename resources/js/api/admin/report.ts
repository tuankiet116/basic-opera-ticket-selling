import axiosInstance from "../axios"

type DataReport = {
    events: Array<number>,
}

export const createReportAPI = async (data: DataReport) => {
    return await axiosInstance.post("/admin/report/aggregate", data);
}