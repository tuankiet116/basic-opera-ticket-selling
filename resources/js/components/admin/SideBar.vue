<template>
    <div class="d-flex flex-column flex-shrink-0 p-3 text-white bg-dark min-vh-100" style="width: 280px;">
        <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
            <img width="50" src="../../../images/image.png" />
            <span class="fs-4">&nbsp; HGO Admin</span>
        </a>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item">
                <router-link :to="{ name: 'admin-list-events' }" class="nav-link"
                    :class="{ 'active': isActiveRouter('admin-list-events') }" aria-current="page">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                        class="bi bi-calendar-event-fill" viewBox="0 0 16 16">
                        <path
                            d="M4 .5a.5.5 0 0 0-1 0V1H2a2 2 0 0 0-2 2v1h16V3a2 2 0 0 0-2-2h-1V.5a.5.5 0 0 0-1 0V1H4zM16 14V5H0v9a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2m-3.5-7h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5" />
                    </svg>
                    &nbsp;
                    Danh sách sự kiện
                </router-link>
            </li>
            <li class="nav-item mt-2">
                <router-link :to="{ name: 'admin-create-event' }" class="nav-link" aria-current="page"
                    :class="{ 'active': isActiveRouter('admin-create-event') }">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                        class="bi bi-calendar-plus" viewBox="0 0 16 16">
                        <path
                            d="M8 7a.5.5 0 0 1 .5.5V9H10a.5.5 0 0 1 0 1H8.5v1.5a.5.5 0 0 1-1 0V10H6a.5.5 0 0 1 0-1h1.5V7.5A.5.5 0 0 1 8 7" />
                        <path
                            d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z" />
                    </svg>
                    &nbsp;
                    Tạo sự kiện
                </router-link>
            </li>
        </ul>
        <hr>
        <div class="dropdown position-relative bottom-0">
            <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle"
                id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor"
                    class="bi bi-person-circle" viewBox="0 0 16 16">
                    <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0" />
                    <path fill-rule="evenodd"
                        d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1" />
                </svg>
                &nbsp;
                <strong>Admin</strong>
            </a>
            <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser1">
                <li><a class="dropdown-item" href="#">Settings</a></li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li><a class="dropdown-item" href="#" @click="logout">Sign out</a></li>
            </ul>
        </div>
    </div>
</template>
<script setup lang="ts">
import { useRouter } from "vue-router";
import { logoutAPI } from "../../api/admin/auth";
import { HTTP_SUCCESS } from "../../config/const";
import { useAuthenticateStore } from "../../pinia.ts";

const authenticatedStore = useAuthenticateStore();
const router = useRouter();
const isActiveRouter = (typeRouter) => {
    if (typeRouter == router.currentRoute.value.name) {
        return true;
    }
    return false;
}

const logout = async () => {
    let result = await logoutAPI();
    authenticatedStore.setAdminLoggedIn(false);
    if (result.status == HTTP_SUCCESS)
        router.push({ name: "admin-login" })
}
</script>