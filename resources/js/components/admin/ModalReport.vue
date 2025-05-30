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
                    <small v-if="errors.type" class="text-danger">{{ errors.type[0] }}</small>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-3 d-flex align-items-center">
                    <label>Chọn sự kiện:</label>
                </div>
                <div class="col-9">
                    <multiselect v-model="eventSelected" :options="events" multiple track-by="id"
                        @searchChange="searchChange" deselect-label="Bỏ chọn sự kiện" label="name"
                        placeholder="Chọn ít nhất 1 sự kiện" select-label="Chọn sự kiện" selected-label="Đã chọn">
                    </multiselect>
                    <small v-if="errors.events" class="text-danger">{{ errors.events[0] }}</small>
                </div>
            </div>
            <div class="row" v-if="reportType == 'report-daily'">
                <div class="col-3 d-flex align-items-center">
                    <label>Chọn ngày:</label>
                </div>
                <div class="col-9">
                    <VueDatePicker id="event-date" v-model="dateSelected" locale="vi" :format="format" selectText="Chọn"
                        cancelText="Thoát" range multi-calendars auto-apply />
                    <small v-if="errors.start_date" class="text-danger">{{ errors.start_date[0] }}</small>
                    <small v-if="errors.end_date" class="text-danger">{{ errors.end_date[0] }}</small>
                </div>
            </div>
        </template>
        <template #footer>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            <button type="button" class="btn btn-primary" @click="handleCreateReport">
                Xuất báo cáo
            </button>
        </template>
    </Modal>
</template>
<script setup>
import { ref, onMounted } from "vue";
import Modal from "@/components/Modal.vue";
import Multiselect from 'vue-multiselect';
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';
import { getListEvent } from "../../api/admin/events";
import { createReportAPI } from "../../api/admin/report";
import { HttpStatusCode } from "axios";
import { useToast } from "vue-toastification";
import moment from "moment";
import ToastLoading from "../ToastLoading.vue";
import { useStoreExportStatus } from "../../pinia";

const format = "dd-MM-yyyy";
const toast = useToast();
const storeExporting = useStoreExportStatus();

let events = ref([]);
let eventSelected = ref([]);
let dateSelected = ref([new Date(), new Date()]);
let reportType = ref('report-daily');
let errors = ref({});

onMounted(async () => {
    await searchChange("");
});

const handleCreateReport = async () => {
    let response = await createReportAPI({
        type: reportType.value,
        events: eventSelected.value.map(e => e.id),
        start_date: dateSelected.value && dateSelected.value[0] ? moment(dateSelected.value[0]).format("Y-MM-DD") : null,
        end_date: dateSelected.value && dateSelected.value[1] ? moment(dateSelected.value[1]).format("Y-MM-DD") : null,
    });
    errors.value = {};
    switch (response.status) {
        case HttpStatusCode.Ok:
            const toastId = toast.info({
                component: ToastLoading,
                props: {
                    message: "Đang xuất file báo cáo"
                }
            }, {
                timeout: false,
                icon: false
            });
            storeExporting.setToastId(toastId);
            break;
        case HttpStatusCode.UnprocessableEntity:
            errors.value = response.data.errors;
            break;
        default:
            toast.error('Xảy ra lỗi, không thể xuất báo cáo. Vui lòng check logs.');
    }
}

const searchChange = async (search) => {
    let response = await getListEvent(1, search);
    events.value = response.data.data;
}
</script>
<style src="vue-multiselect/dist/vue-multiselect.css"></style>
<style scoped>
:deep(.multiselect) {
    display: block;
}
</style>