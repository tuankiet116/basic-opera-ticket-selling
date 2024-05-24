<template>
    <Modal modalTitle="Xuất báo cáo">
        <template #body>
            <div class="row" style="width: 700px;">
                <div class="col-4 d-flex align-items-center">
                    <label>Chọn ngày xuất báo cáo:</label>
                </div>
                <div class="col-8">
                    <flat-pickr id="event-date" v-model="reportInformation.date" class="form-control"
                        :config="config" />
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
import { reactive, ref } from "vue";
import Modal from "@/components/Modal.vue";
import flatPickr from "vue-flatpickr-component";
import 'flatpickr/dist/flatpickr.css';
import { Vietnamese } from 'flatpickr/dist/l10n/vn';
import { exportReportAPI } from "../../api/admin/report";

let reportInformation = reactive({
    date: ""
});

const config = ref({
    altInput: true,
    dateFormat: 'Y-m-d',
    locale: Vietnamese, // locale for this instance only          
});

const handleExportReport = async () => {
    await exportReportAPI();
}
</script>