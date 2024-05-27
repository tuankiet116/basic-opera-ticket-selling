<template>
    <Modal modalTitle="Báo cáo tình trạng bán vé, doanh thu">
        <template #body>
            <div style="width: 700px;" class="row mb-3">
                <div class="col-3 d-flex align-items-center">
                    <label>Loại báo cáo</label>
                </div>
                <div class="col-9">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="report-type" id="report-daily"
                            value="report-daily" v-model="reportType" checked>
                        <label class="form-check-label" for="report-daily">
                            Báo cáo doanh thu hàng ngày
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="report-type" id="report-event"
                            value="report-event" v-model="reportType">
                        <label class="form-check-label" for="report-event">
                            Báo cáo doanh thu toàn bộ sự kiện
                        </label>
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-3 d-flex align-items-center">
                    <label>Chọn sự kiện:</label>
                </div>
                <div class="col-9">
                    <multiselect v-model="eventSelected" :options="events" multiple track-by="id" @searchChange="searchChange"
                        deselect-label="Bỏ chọn sự kiện" label="name" placeholder="Chọn ít nhất 1 sự kiện"
                        select-label="Chọn sự kiện" selected-label="Đã chọn"></multiselect>
                </div>
            </div>
            <div class="row" v-if="reportType == 'report-daily'">
                <div class="col-3 d-flex align-items-center">
                    <label>Chọn ngày:</label>
                </div>
                <div class="col-9">
                    <VueDatePicker id="event-date" v-model="reportInformation.date" locale="vi" :format="format"
                        selectText="Chọn" cancelText="Thoát" range />
                </div>
            </div>
        </template>
        <template #footer>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            <button type="button" class="btn btn-primary" @click="handleExportReport" data-bs-dismiss="modal">
                Xuất báo cáo
            </button>
        </template>
    </Modal>
</template>
<script setup>
import { reactive, ref, onMounted } from "vue";
import Modal from "@/components/Modal.vue";
import Multiselect from 'vue-multiselect';
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';
import { getListEvent } from "../../api/admin/events";
import { createReportAPI } from "../../api/admin/report";
import { HttpStatusCode } from "axios";
import { useToast } from "vue-toastification";

const format = "dd-MM-yyyy";
const toast = useToast();

let reportInformation = reactive({
    date: [new Date(), new Date()],
});

let events = ref([]);
let eventSelected = ref([]);
let reportType = ref('report-daily');

const handleCreateReport = async () => {
    let response = await createReportAPI({});
    switch (response.status) {
        case HttpStatusCode.Ok:
            break;
        default:
            toast.error('Xảy ra lỗi, không thể xuất báo cáo. Vui lòng check logs.');
    }
}

const handleExportReport = async () => {
    let link = document.createElement("a");
    link.href = "/admin/export/aggregate";
    link.target = "_blank";
    link.click();
    link.remove();
}

const searchChange = async (search) => {
    let response = await getListEvent(1, search);
    events.value = response.data.data;
}

defineExpose({ events });
</script>
<style src="vue-multiselect/dist/vue-multiselect.css"></style>
<style scoped>
:deep(.multiselect) {
    display: block;
}
</style>