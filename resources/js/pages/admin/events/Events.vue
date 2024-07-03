<template>
    <div class="container-fluid">
        <div class="container p-5">
            <div class="row g-3 align-items-center justify-content-center">
                <div class="col-auto">
                    <label for="searchEvent" class="col-form-label">Tìm kiếm sự kiện</label>
                </div>
                <div class="col-auto">
                    <input type="search" id="searchEvent" class="form-control" v-model="searchString">
                </div>
                <div class="col-auto">
                    <button class="btn btn-primary text-white" @click="getEvents">
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
        <div class="container p-5">
            <table class="table table-group-divider">
                <thead>
                    <tr>
                        <th width="2%" class="text-center">#</th>
                        <th width="25%" class="text-center">Tên sự kiện</th>
                        <th width="5%" class="text-center">Ngày diễn</th>
                        <th width="15%" class="text-center">Mã chuyển khoản</th>
                        <th width="15%" class="text-center">Trạng thái</th>
                        <th width="20%" class="text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="!events.data.length">
                        <td colspan="5" class="text-center">
                            Không có dữ liệu hợp lệ
                        </td>
                    </tr>
                    <tr v-else v-for="(event, index) in events.data" :key="index">
                        <th class="text-center" scope="row">{{ index + 1 + (events.current_page - 1) * events.per_page
                            }}</th>
                        <td class="text-center">
                            <a :href="`/book/${event.id}`" class="fw-medium">
                                {{ event.name }}
                            </a>
                        </td>
                        <td class="text-center">{{ convertDate(event.date) }}</td>
                        <td class="text-center">{{ event.banking_code }}</td>
                        <td>
                            <div class="d-flex justify-content-center">
                                <div class="form-check form-switch">
                                    <label class="form-check-label" for="flexSwitchCheckDisabled">Mở bán vé</label>
                                    <input class="form-check-input"
                                        @change="updateOpenningStatus(event.id, !event.is_openning)" type="checkbox"
                                        role="switch" id="flexSwitchCheckDisabled" :checked="event.is_openning">
                                </div>
                            </div>
                        </td>
                        <td class="text-center">
                            <Popper arrow>
                                <button class="btn">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor"
                                        class="bi bi-feather" viewBox="0 0 16 16">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            fill="currentColor" class="bi bi-toggles2" viewBox="0 0 16 16">
                                            <path
                                                d="M9.465 10H12a2 2 0 1 1 0 4H9.465c.34-.588.535-1.271.535-2s-.195-1.412-.535-2" />
                                            <path
                                                d="M6 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6m0 1a4 4 0 1 1 0-8 4 4 0 0 1 0 8m.535-10a4 4 0 0 1-.409-1H4a1 1 0 0 1 0-2h2.126q.138-.534.41-1H4a2 2 0 1 0 0 4z" />
                                            <path d="M14 4a4 4 0 1 1-8 0 4 4 0 0 1 8 0" />
                                        </svg>
                                    </svg>
                                </button>
                                <template #content>
                                    <div class="card d-block p-1 shadow shadow-md">
                                        <router-link :to="{ name: 'admin-edit-event', params: { eventId: event.id } }"
                                            type="button" class="btn btn-light mx-1 btn-sm">
                                            Cài đặt thông tin
                                        </router-link>
                                        <router-link :to="{ name: 'admin-edit-seats', params: { eventId: event.id } }"
                                            type="button" class="btn btn-light mx-1 btn-sm">
                                            Cài đặt chỗ ngồi
                                        </router-link>
                                        <router-link :to="{ name: 'admin-discount', params: { eventId: event.id } }"
                                            type="button" class="btn btn-light mx-1 btn-sm">
                                            Mã giảm giá</router-link>
                                        <button type="button" @click="removeEvent(event)"
                                            class="btn btn-danger mx-1 btn-sm">Xóa</button>
                                    </div>
                                </template>
                            </Popper>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="row">
                <div class="d-flex">
                    <div class="ms-auto">
                        <button class="btn btn-light m-1" :class="{ 'active': events.current_page == page }"
                            v-for="page in events.last_page" :key="page" @click="changePage(page)">
                            {{ page }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { onMounted, reactive, ref } from "vue";
import { deleteEventAPI, getListEvent, updateStatusAPI } from "../../../api/admin/events";
import { HttpStatusCode } from "axios";
import moment from "moment";
import { useToast } from "vue-toastification";
import Popper from "vue3-popper";

let events = reactive({
    data: [],
    current_page: 1,
    last_page: 1,
    per_page: 0,
});
let searchString = ref("");
let pageNumber = ref(1);
const toast = useToast();

onMounted(async () => {
    await getEvents();
});

const changePage = async (page) => {
    pageNumber.value = page;
    await getEvents();
}

const getEvents = async () => {
    let response = await getListEvent(pageNumber.value, searchString.value);
    switch (response.status) {
        case HttpStatusCode.Ok:
            events.data = response.data.data;
            events.current_page = response.data.current_page;
            events.last_page = response.data.last_page;
            events.per_page = response.data.per_page;
            break;
        default:
            console.log(response.data);
    }
}

const convertDate = (date) => {
    return moment(date).format("DD/MM/yyyy")
}

const updateOpenningStatus = async (eventId, status) => {
    let response = await updateStatusAPI(eventId, {
        is_openning: status
    });
    if (response.status == HttpStatusCode.Ok) {
        let event = events.data.find(e => e.id == eventId);
        if (status) {
            toast.success(`Sự kiện '${event.name}' mở bán vé thành công`);
        } else {
            toast.success(`Sự kiện '${event.name}' đóng bán vé thành công`);
        }
    } else {
        toast.error(`Sự kiện '${event.name}' lỗi cập nhật trạng thái bán vé.`);
    }
}

const removeEvent = async (event) => {
    let isConfirm = confirm(`Sự kiện "${event.name}" bị xóa sẽ vô hiệu hóa. Bạn chắc chắn chứ?`);
    if (isConfirm) {
        let response = await deleteEventAPI(event.id);
        if (response.status == HttpStatusCode.Ok) {
            toast.success(`Sự kiện '${event.name}' đã bị xóa thành công`);
            events.data = events.data.filter((e) => {
                return e.id != event.id;
            })
        }
    }
}
</script>