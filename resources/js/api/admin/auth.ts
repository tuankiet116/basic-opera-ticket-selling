import { axiosInstance } from './../axios';

export const loginAPI = async (credentials: { email: string, password: string, remember_me: boolean }) => {
    return await axiosInstance.post("/admin/login", credentials);
}

export const isLoggedInAPI = async () => {
    return await axiosInstance.get("/admin/is-logged-in");
}

export const logoutAPI = async () => {
    return await axiosInstance.post("/admin/logout");
}