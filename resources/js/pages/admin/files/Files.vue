<template>
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
                    <th width="20%" v-if="tab == TAB_SPECIAL_CLIENTS">
                        Hành động
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr v-if="!clients.data?.length">
                    <td :colspan="tab == TAB_SPECIAL_CLIENTS ? 7 : 6" class="text-center">
                        Không có dữ liệu hợp lệ
                    </td>
                </tr>
                <tr v-else v-for="(client, index) in clients.data" :key="client.id">
                    <th scope="row">{{ index + 1 + (clients.current_page - 1) * clients.per_page }}</th>
                    <td>{{ client.name }}</td>
                    <td>{{ client.email }}</td>
                    <td>{{ client.phone_number }}</td>
                    <td>{{ client.address }}</td>
                    <td>{{ client.event_name }}</td>
                    <td v-if="tab == TAB_SPECIAL_CLIENTS">
                        <router-link :to="{ name: 'admin-edit-client', params: { clientId: client.id } }" type="button"
                            class="btn btn-light mx-1 my-1 btn-sm">Chỉnh sửa</router-link>
                        <button @click="deleteSpecialClient(client)" type="button"
                            class="btn btn-danger mx-1 btn-sm">Xóa</button>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="row">
            <div class="d-flex">
                <div class="ms-auto">
                    <button class="btn btn-light m-1" :class="{ 'active': clients.current_page == page }"
                        v-for="page in clients.last_page" :key="page" @click="changePage(page)">
                        {{ page }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
import { ref } from "vue";

</script>