import axios, { AxiosResponse, HttpStatusCode } from "axios";
import Cookies from "js-cookie";

export const axiosInstance = axios.create({
    baseURL: '/api',
    withCredentials: true,
    withXSRFToken: true
});

// Request interceptor. Runs before your request reaches the server
const onRequest = (config) => {
    // If http method is `post | put | delete` and XSRF-TOKEN cookie is 
    // not present, call '/sanctum/csrf-cookie' to set CSRF token, then 
    // proceed with the initial response
    if ((
        config.method == 'post' ||
        config.method == 'put' ||
        config.method == 'delete'
        /* other methods you want to add here */
    ) && !Cookies.get('XSRF-TOKEN')) {
        return setCSRFToken()
            .then(response => config);
    }
    return config;
}

// A function that calls '/api/csrf-cookie' to set the CSRF cookies. The 
// default is 'sanctum/csrf-cookie' but you can configure it to be anything.
const setCSRFToken = () => {
    return axiosInstance.get('/sanctum/csrf-cookie', {
        baseURL: "/"
    }); // resolves to '/api/csrf-cookie'.
}

const onResponseRejected = async (axiosResponse) => {
    return axiosResponse.response;
}

// attach your interceptor
axiosInstance.interceptors.request.use(onRequest, null);
axiosInstance.interceptors.response.use(null, onResponseRejected);

export default axiosInstance;
