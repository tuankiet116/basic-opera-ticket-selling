<template>
    <div class="container p-5">
        <table class="table table-group-divider">
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th width="10%">Tên file</th>
                    <th width="10%">Trạng thái</th>
                    <th>Lý do lỗi</th>
                    <th width="10%">Hành động</th>
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
                        <a :href="file.is_exported ? `/admin/files/download/${file.file_name}` : `#`">
                            {{ file.file_name }}
                        </a>
                    </td>
                    <td v-if="file.is_failed">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-exclamation-circle-fill" viewBox="0 0 16 16">
                            <path
                                d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M8 4a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 4m.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2" />
                        </svg>
                        Xuất file lỗi
                    </td>
                    <td v-else>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-check-circle" viewBox="0 0 16 16">
                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                            <path
                                d="m10.97 4.97-.02.022-3.473 4.425-2.093-2.094a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05" />
                        </svg>
                        Xuất file thành công
                    </td>
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
</template>
<script setup>
import { ref, onMounted } from "vue";
import { deleteFileAPI, getListFileAPI } from "../../../api/admin/report";
import { useToast } from "vue-toastification";

const toast = useToast();
const files = ref({
    data: [],
    current_page: 1,
    per_page: 1,
    last_page: null,
})

onMounted(async () => {
    await fetchListFiles();
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
    toast.success("Xóa file thành công.");
}

const changePage = (page) => {
    files.value.current_page = page;
    fetchListFiles();
}
</script>