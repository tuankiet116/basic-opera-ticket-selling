<template>
    <div class="container d-flex mt-5">
        <button class="btn btn-primary text-white ms-auto" data-bs-toggle="modal" data-bs-target="#modal">
            <span>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                    class="bi bi-file-earmark-excel" viewBox="0 0 16 16">
                    <path
                        d="M5.884 6.68a.5.5 0 1 0-.768.64L7.349 10l-2.233 2.68a.5.5 0 0 0 .768.64L8 10.781l2.116 2.54a.5.5 0 0 0 .768-.641L8.651 10l2.233-2.68a.5.5 0 0 0-.768-.64L8 9.219l-2.116-2.54z" />
                    <path
                        d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2M9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5z" />
                </svg>
                Xuất báo cáo
            </span>
        </button>
    </div>
    <div class="container p-2 mt-3">
        <table class="table table-group-divider">
            <thead>
                <tr>
                    <th width="2%">#</th>
                    <th width="12%">Tên file</th>
                    <th width="15%">Trạng thái</th>
                    <th width="15%">Ngày</th>
                    <th>Lý do lỗi</th>
                    <th width="10%"></th>
                </tr>
            </thead>
            <tbody>
                <tr v-if="!files.data?.length">
                    <td colspan="5" class="text-center">
                        Không có dữ liệu hợp lệ
                    </td>
                </tr>
                <tr v-else v-for="(file, index) in files.data" :key="file.id">
                    <th scope="row">{{ index + 1 + (files.current_page - 1) * files.per_page }}</th>
                    <td>
                        <a :class="{ 'color-download': file.is_exported }"
                            class="d-flex justify-content-center align-items-center"
                            :href="file.is_exported ? `/admin/files/download/${file.file_name}` : `#`">
                            <svg v-if="file.is_exported" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                fill="currentColor" class="bi bi-download" viewBox="0 0 16 16">
                                <path
                                    d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5" />
                                <path
                                    d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708z" />
                            </svg>
                            <span class="ms-2">{{ file.file_name }}</span>
                        </a>
                    </td>
                    <td v-if="file.is_failed">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-exclamation-circle-fill" viewBox="0 0 16 16">
                            <path
                                d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M8 4a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 4m.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2" />
                        </svg>
                        Thất bại
                    </td>
                    <td v-else class="color-success">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-check-circle" viewBox="0 0 16 16">
                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                            <path
                                d="m10.97 4.97-.02.022-3.473 4.425-2.093-2.094a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05" />
                        </svg>
                        Thành công
                    </td>
                    <td>{{ moment(file.created_at).format("DD-MM-YYYY H:mm:ss") }}</td>
                    <td>{{ file.reason }}</td>
                    <td>
                        <button @click="deleteFile(file.id)" type="button"
                            class="btn btn-danger mx-1 btn-sm">Xóa</button>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="row">
            <div class="d-flex">
                <div class="ms-auto">
                    <button class="btn btn-light m-1" :class="{ 'active': files.current_page == page }"
                        v-for="page in files.last_page" :key="page" @click="changePage(page)">
                        {{ page }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    <modal-report ref="modalReport" />
</template>
<script setup>
import { ref, onMounted, onUnmounted } from "vue";
import { deleteFileAPI, getListFileAPI } from "../../../api/admin/report";
import { useToast } from "vue-toastification";
import ModalReport from "../../../components/admin/ModalReport.vue";
import { HttpStatusCode } from "axios";
import moment from "moment";

const toast = useToast();
const files = ref({
    data: [],
    current_page: 1,
    per_page: 1,
    last_page: null,
})

onUnmounted(() => {
    window.Echo.leave(`admin.notifications`);
})

onMounted(async () => {
    await fetchListFiles();
    window.Echo.private(`admin.notifications`)
        .listen("AdminSystemNotification", (e) => {
            if (e.status) {
                fetchListFiles()
            }
        })
})

const fetchListFiles = async () => {
    let response = await getListFileAPI(files.value.current_page);
    files.value.data = response.data.data;
    files.value.current_page = response.data.current_page;
    files.value.per_page = response.data.per_page;
    files.value.last_page = response.data.last_page;
}

const deleteFile = async (fileId) => {
    let response = await deleteFileAPI(fileId);
    switch (response.status) {
        case HttpStatusCode.Ok:
            toast.success("Xóa file thành công.");
            changePage(files.value.current_page);
            break;
        default:
            toast.error("Xóa file thất bại!");
    }
}

const changePage = (page) => {
    files.value.current_page = page;
    fetchListFiles();
}
</script>
<style scoped>
.color-download {
    color: green;
}
</style>