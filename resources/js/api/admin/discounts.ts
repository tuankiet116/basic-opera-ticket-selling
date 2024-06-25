import { DiscountData } from "../../types/discount";
import axiosInstance from "../axios"

export const createDiscountAPI = (data: DiscountData) => {
    return axiosInstance.post("/admin/discount/create", data);
}

export const updateDiscountAPI = (data: DiscountData, discountId: number) => {
    return axiosInstance.put(`/admin/discount/update/${discountId}`, data);
}

export const getListDiscountAPI = (eventId: number, page: number) => {
    return axiosInstance.get(`/admin/discount/${eventId}?page=${page}`);
}

export const deleteDiscountAPI = (discountId: number) => {
    return axiosInstance.delete(`/admin/discount/delete/${discountId}`);
}