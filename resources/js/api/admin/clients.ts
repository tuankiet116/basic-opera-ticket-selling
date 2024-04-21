import { ClientData } from "../../types/clients";
import axiosInstance from "../axios"

export const getListClientsAPI = async (search = "", page = 1) => {
    return await axiosInstance.get(`/admin/client/list?page=${page}&search=${search}`);
}

export const getSpecialClientsAPI = async (search = "", page = 1) => {
    return await axiosInstance.get(`/admin/client/special?page=${page}&search=${search}`);
}

export const getClientAPI = async (clientId: number) => {
    return await axiosInstance.get(`/admin/client/edit/${clientId}`);
}

export const updateClientAPI = async (clientId: number, data: ClientData) => {
    return await axiosInstance.put(`/admin/client/update/${clientId}`, data);
}

export const createClientAPI = async (data: ClientData) => {
    return await axiosInstance.post(`/admin/client/create`, data);
}