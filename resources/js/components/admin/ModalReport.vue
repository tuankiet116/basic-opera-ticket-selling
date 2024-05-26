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
                    <multiselect v-model="eventSelected" :options="events" track-by="id"
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

const format = "dd-MM-yyyy"

let reportInformation = reactive({
    date: [new Date(), new Date()],
});

let events = ref([]);
let eventSelected = ref([]);
let reportType = ref('report-daily');

const handleExportReport = async () => {
    let link = document.createElement("a");
    link.href = "/admin/export/aggregate";
    link.target = "_blank";
    link.click();
    link.remove();
}

defineExpose({ events });
</script>
<style src="vue-multiselect/dist/vue-multiselect.css"></style>
<style scoped>
.multiselect::v-deep {
    display: block;
}
</style>