import axiosInstance from "./axios";

export const applyDiscountAPI = (data: {
    discount_code: string,
    token: string
}) => {
    return axiosInstance.post("/discount/apply", data);
}