<template>
    <Modal id="modal-discount" :modalTitle="props.isCreate ? 'Tạo mã giảm giá' : 'Cập nhật mã giảm giá'" isCenter>
        <template #body>
            <div class="row">
                <div class="col-6">
                    <label for="discount_code" class="form-label fw-medium">Mã giảm giá: </label>
                    <input type="text" :disabled="!props.isCreate" class="form-control" id="discount_code"
                        v-model="data.discount_code">
                    <small v-if="errors.discount_code" class="text-danger">{{ errors.discount_code[0] }}</small>
                </div>
                <div class="col-6">
                    <label for="quantity" class="form-label fw-medium">Số lượng áp dụng: </label>
                    <input type="number" class="form-control" id="quantity" v-model="data.quantity">
                    <small v-if="errors.quantity" class="text-danger">{{ errors.quantity[0] }}</small>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-6">
                    <label for="price_discount" class="form-label fw-medium">Loại giảm giá: </label>
                    <div class="form-check">
                        <input :disabled="!props.isCreate" class="form-check-input" type="radio" name="radio-discount"
                            id="radio-discount-percentage" value="percentage-discount" v-model="discountType">
                        <label class="form-check-label" for="radio-discount-percentage">
                            Giảm theo phần trăm
                        </label>
                    </div>
                    <div class="form-check">
                        <input :disabled="!props.isCreate" class="form-check-input" type="radio" name="radio-discount"
                            id="radio-discount-price" value="price-discount" v-model="discountType">
                        <label class="form-check-label" for="radio-discount-price">
                            Giảm theo giá tiền
                        </label>
                    </div>
                </div>
                <div class="col-6" v-if="discountType == 'price-discount'">
                    <label for="price_discount" class="form-label fw-medium">Số tiền giảm: </label>
                    <div class="input-group mb-3">
                        <input :disabled="!props.isCreate" type="text" class="form-control" id="price_discount"
                            v-model="data.price_discount">
                        <span class="input-group-text" id="addon-currency">VND</span>
                    </div>
                    <small v-if="errors.price_discount" class="text-danger">{{ errors.price_discount[0] }}</small>
                </div>
                <div class="col-6" v-if="discountType == 'percentage-discount'">
                    <label for="percentage_discount" class="form-label fw-medium">Phần trăm giảm: </label>
                    <div class="input-group mb-3">
                        <input :disabled="!props.isCreate" type="number" class="form-control" id="percentage_discount"
                            v-model="data.percentage_discount">
                        <span class="input-group-text" id="addon-percentage">%</span>
                    </div>
                    <small v-if="errors.percentage_discount" class="text-danger">
                        {{ errors.percentage_discount[0] }}
                    </small>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-6">
                    <label for="price_discount" class="form-label fw-medium">Thời gian áp dụng: </label>
                    <VueDatePicker id="event-date" v-model="dateSelected" locale="vi" :format="format" selectText="Chọn"
                        cancelText="Thoát" range multi-calendars auto-apply />
                    <small v-if="errors.start_date" class="text-danger">{{ errors.start_date[0] }}</small>
                    <small v-if="errors.end_date" class="text-danger">{{ errors.end_date[0] }}</small>
                </div>
                <div class="col-6">
                    <label for="ticket_class_id" class="form-label fw-medium">Hạng vé áp dụng: </label>
                    <Multiselect :disabled="!props.isCreate" v-model="ticketClassSelected" :options="optionsTicketClasses" track-by="id"
                        @searchChange="searchTicketClassChange" deselect-label="Bỏ chọn" label="name"
                        select-label="Chọn hạng vé áp dụng" selected-label="Đã chọn">
                        <template #option="props">
                            {{ props.option.name + (props.option.price ? `(${numberWithCommas(props.option.price)}) vnd`
                                : "") }}
                        </template>
                    </Multiselect>
                    <small v-if="errors.ticket_class_id" class="text-danger">
                        {{ errors.ticket_class_id[0] }}
                    </small>
                </div>
                <div class="col-12">
                    <label for="note" class="form-label fw-medium">Ghi chú: </label>
                    <input class="form-control" id="note" v-model="data.note" />
                </div>
            </div>
        </template>
        <template #footer>
            <button type="button" class="btn btn-secondary" @click="closeModal">Đóng</button>
            <button type="button" class="btn btn-primary text-white" @click="handleUpsertDiscountCode">
                {{ props.isCreate ? 'Tạo mới' : 'Cập nhật' }}
            </button>
        </template>
    </Modal>
</template>
<script setup>
import Modal from '../Modal.vue';
import { numberWithCommas } from '../../helpers/number';
import { ref, defineProps, computed, toRaw, watch } from 'vue';
import VueDatePicker from "@vuepic/vue-datepicker";
import Multiselect from 'vue-multiselect';
import { createDiscountAPI, updateDiscountAPI } from '../../api/admin/discounts';
import { useRoute } from 'vue-router';
import { HttpStatusCode } from 'axios';
import { useToast } from 'vue-toastification';
import { Modal as BootstrapModal } from "bootstrap";
import moment from 'moment';

const format = "dd-MM-yyyy";
const emits = defineEmits(["afterCreate", "afterUpdate"]);
const route = useRoute();
const toast = useToast();
const props = defineProps({
    isCreate: {
        type: Boolean,
        require: true
    },
    discountData: {
        type: Object,
        default: {
            discount_code: "",
            ticket_class_id: null,
            note: "",
            quantity: "",
            price_discount: "",
            percentage_discount: "",
            start_date: null,
            end_date: null
        }
    },
    ticketClasses: {
        type: Array,
        default: []
    }
});

let data = ref({});

let searchStringTicketClass = ref("");
let dateSelected = ref([]);
let discountType = ref('percentage-discount');
let ticketClassSelected = ref({
    id: null,
    name: "Mọi hạng vé"
});
let errors = ref({});
let optionsTicketClasses = computed(() => {
    let options = structuredClone(toRaw(props.ticketClasses));
    options.push({
        id: null,
        name: "Mọi hạng vé"
    });
    return options.filter(ticketClass => ticketClass.name.toLowerCase().indexOf(searchStringTicketClass.value) > -1);
})

function searchTicketClassChange(search) {
    searchStringTicketClass.value = search;
}

function closeModal() {
    const modal = BootstrapModal.getInstance(document.getElementById('modal-discount'));
    modal.hide();
}

async function handleUpsertDiscountCode() {
    let response = {
        status: HttpStatusCode.InternalServerError
    }
    let dataUpsert = {
        ...data.value,
        event_id: route.params.eventId,
        discount_type: discountType.value,
        start_date: moment(dateSelected.value[0]).format("Y-MM-DD"),
        end_date: moment(dateSelected.value[1]).format("Y-MM-DD"),
    };
    if (props.isCreate) {
        response = await createDiscountAPI(dataUpsert);
    } else {
        response = await updateDiscountAPI(dataUpsert, data.value.id);
    }

    switch (response.status) {
        case HttpStatusCode.UnprocessableEntity:
            errors.value = response.data.errors;
            break;
        case HttpStatusCode.Ok:
            closeModal();
            response.data.ticket_class = ticketClassSelected;
            if (props.isCreate) emits("afterCreate", response.data);
            else {
                data.value.start_date = dateSelected.value[0];
                data.value.end_date = dateSelected.value[1];
                emits("afterUpdate", data.value)
            };
            break;
        default:
            toast.error(response.data.message);
    }
}

watch(ticketClassSelected, (ticketClassSelected) => data.value.ticket_class_id = ticketClassSelected?.id ?? null);
watch(() => props.discountData, (newValue) => {
    if (Object.keys(newValue).length) data.value = newValue;
    else data.value = {
        discount_code: "",
        ticket_class_id: null,
        note: "",
        quantity: "",
        price_discount: "",
        percentage_discount: "",
        start_date: null,
        end_date: null
    };
    if (newValue.start_date && newValue.end_date) dateSelected.value = [newValue.start_date, newValue.end_date]
    else dateSelected.value = [];
});
</script>