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
                        <th width="5%">#</th>
                        <th width="10%">Tên sự kiện</th>
                        <th width="5%">Ngày diễn</th>
                        <th width="40%">Mô tả</th>
                        <th width="15%">Trạng thái</th>
                        <th width="25%">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="!events.data.length">
                        <td colspan="5" class="text-center">
                            Không có dữ liệu hợp lệ
                        </td>
                    </tr>
                    <tr v-else v-for="(event, index) in events.data" :key="index">
                        <th scope="row">{{ index + 1 + (events.current_page - 1) * events.per_page }}</th>
                        <td>
                            <router-link :to="{ name: 'book-ticket', params: { eventId: event.id } }">
                                <strong>{{ event.name }}</strong>
                            </router-link>
                        </td>
                        <td>{{ convertDate(event.date) }}</td>
                        <td class="text-wrap text-truncate ">
                            <p>{{ event.description }}</p>
                        </td>
                        <td>
                            <div class="form-check form-switch">
                                <label class="form-check-label" for="flexSwitchCheckDisabled">Mở bán vé</label>
                                <input class="form-check-input"
                                    @change="updateOpenningStatus(event.id, !event.is_openning)" type="checkbox"
                                    role="switch" id="flexSwitchCheckDisabled" :checked="event.is_openning">
                            </div>
                        </td>
                        <td>
                            <router-link :to="{ name: 'admin-edit-event', params: { eventId: event.id } }" type="button"
                                class="btn btn-light mx-1 my-1 btn-sm">Cài đặt thông tin</router-link>
                            <router-link :to="{ name: 'admin-edit-seats', params: { eventId: event.id } }" type="button"
                                class="btn btn-light mx-1 btn-sm">Cài đặt chỗ ngồi</router-link>
                            <button type="button" class="btn btn-danger mx-1 btn-sm">Xóa</button>
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
import { getListEvent, updateStatusAPI } from "../../../api/admin/events";
import { HttpStatusCode } from "axios";
import moment from "moment";
import { useToast } from "vue-toastification";

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
</script>