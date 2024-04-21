<template>
    <div class="container-fluid">
        <div class="container p-5">
            <div class="row g-3 align-items-center justify-content-center">
                <div class="col-auto">
                    <label for="searchEvent" class="col-form-label">Tìm kiếm khách hàng</label>
                </div>
                <div class="col-auto">
                    <input type="search" id="searchEvent" class="form-control" v-model="search">
                </div>
                <div class="col-auto">
                    <button class="btn btn-primary text-white" @click="getClients">
                        <span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-search" viewBox="0 0 16 16">
                                <path
                                    d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0" />
                            </svg>
                            Tìm kiếm
                        </span>
                    </button>
                </div>
            </div>
        </div>
        <div class="container-fluid mt-5">
            <ul class="nav nav-tabs justify-content-center">
                <li class="nav-item" @click="changeTab(TAB_CLIENTS)">
                    <button class="nav-link" :class="{ 'active': tab == TAB_CLIENTS }">
                        Danh sách khách hàng Online
                    </button>
                </li>
                <li class="nav-item" @click="changeTab(TAB_SPECIAL_CLIENTS)">
                    <button class="nav-link" :class="{ 'active': tab == TAB_SPECIAL_CLIENTS }">
                        Danh sách khách hàng đặc biệt
                    </button>
                </li>
            </ul>
        </div>
        <div v-if="tab == TAB_SPECIAL_CLIENTS" class="container mt-5">
            <router-link class="btn btn-primary" :to="{ name: 'admin-create-client' }">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                    class="bi bi-person-add" viewBox="0 0 16 16">
                    <path
                        d="M12.5 16a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7m.5-5v1h1a.5.5 0 0 1 0 1h-1v1a.5.5 0 0 1-1 0v-1h-1a.5.5 0 0 1 0-1h1v-1a.5.5 0 0 1 1 0m-2-6a3 3 0 1 1-6 0 3 3 0 0 1 6 0M8 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4" />
                    <path
                        d="M8.256 14a4.5 4.5 0 0 1-.229-1.004H3c.001-.246.154-.986.832-1.664C4.484 10.68 5.711 10 8 10q.39 0 .74.025c.226-.341.496-.65.804-.918Q8.844 9.002 8 9c-5 0-6 3-6 4s1 1 1 1z" />
                </svg>
                Thêm khách hàng đặc biệt
            </router-link>
        </div>
        <div class="container p-5">
            <table class="table table-group-divider">
                <thead>
                    <tr>
                        <th width="10%">#</th>
                        <th width="10%">Tên khách hàng</th>
                        <th width="10%">Email</th>
                        <th width="10%">Số điện thoại</th>
                        <th width="20%">Địa chỉ</th>
                        <th width="10%">Sự kiện đặt vé</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="!clients.data?.length">
                        <td colspan="5" class="text-center">
                            No Data Available
                        </td>
                    </tr>
                    <tr v-else v-for="(client, index) in clients.data" :key="client.id">
                        <th scope="row">{{ index + 1 + (clients.current_page - 1) * clients.per_page }}</th>
                        <td>{{ client.name }}</td>
                        <td>{{ client.email }}</td>
                        <td>{{ client.phone_number }}</td>
                        <td>{{ client.address }}</td>
                        <td>{{ client.event_name }}</td>
                    </tr>
                </tbody>
            </table>
            <div class="row">
                <div class="d-flex">
                    <button class="btn btn-light ms-auto" v-for="page in clients.last_page" :key="page"
                        @click="changePage(page)">
                        {{ page }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
import { onMounted, ref } from "vue";
import { getListClientsAPI, getSpecialClientsAPI } from "../../api/admin/clients.ts";

const TAB_SPECIAL_CLIENTS = "special-clients";
const TAB_CLIENTS = "clients";
const props = defineProps({
    tabModeSpecial: {
        type: Boolean,
        default: false,
    }
});
let listClients = ref({});
let listClientsSpecial = ref({});
let clients = ref({});
let search = ref("");
let pageNumber = ref(1);
let tab = ref(TAB_CLIENTS);

onMounted(async () => {
    tab.value = props.tabModeSpecial ? TAB_SPECIAL_CLIENTS : TAB_CLIENTS;
    await getClients();
});

const getClients = async () => {
    await getClientsSpecial();
    await getListClients();
    if (tab.value == TAB_SPECIAL_CLIENTS) {
        clients.value = listClientsSpecial.value;
    } else {
        clients.value = listClients.value;
    }
}

const changePage = async (page) => {
    pageNumber.value = page;
    if (tab.value == TAB_SPECIAL_CLIENTS) {
        await getClientsSpecial();
        clients.value = listClientsSpecial.value;
    } else {
        await getListClients();
        clients.value = listClients.value;
    }
}

const getListClients = async () => {
    let response = await getListClientsAPI(search.value, pageNumber.value);
    listClients.value = response.data;
}

const getClientsSpecial = async () => {
    let response = await getSpecialClientsAPI(search.value, pageNumber.value);
    listClientsSpecial.value = response.data;
}

const changeTab = (tabName) => {
    tab.value = tabName;
    if (tabName == TAB_SPECIAL_CLIENTS) {
        clients.value = listClientsSpecial.value;
    } else {
        clients.value = listClients.value;
    }
}
</script>