import { axiosInstance } from './../axios';

export const loginAPI = async (credentials: { email: string, password: string, remember_me: boolean }) => {
    return await axiosInstance.post("/login", credentials);
}