<template>
    <div class="container p-5">
        <div class="mb-5">
            <h5>Danh sách mã giảm giá "{{ event.name }}"</h5>
        </div>
        <button class="btn btn-primary text-white mb-2" data-bs-toggle="modal" data-bs-target="#modal-discount"
            @click="setModeCreate">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-add"
                viewBox="0 0 16 16">
                <path
                    d="M12.5 16a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7m.5-5v1h1a.5.5 0 0 1 0 1h-1v1a.5.5 0 0 1-1 0v-1h-1a.5.5 0 0 1 0-1h1v-1a.5.5 0 0 1 1 0m-2-6a3 3 0 1 1-6 0 3 3 0 0 1 6 0M8 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4" />
                <path
                    d="M8.256 14a4.5 4.5 0 0 1-.229-1.004H3c.001-.246.154-.986.832-1.664C4.484 10.68 5.711 10 8 10q.39 0 .74.025c.226-.341.496-.65.804-.918Q8.844 9.002 8 9c-5 0-6 3-6 4s1 1 1 1z" />
            </svg>
            Thêm mã giảm giá
        </button>
        <table class="table table-group-divider">
            <thead>
                <tr>
                    <th class="text-center">Mã discount</th>
                    <th class="text-center">Số tiền giảm</th>
                    <th class="text-center">Số phần trăm giảm</th>
                    <th class="text-center">Hạng vé</th>
                    <th class="text-center">Thời gian</th>
                    <th class="text-center">Số lượng</th>
                    <th class="text-center">Ghi chú</th>
                    <th class="text-center"></th>
                </tr>
            </thead>
            <tbody>
                <template v-if="discounts.data.length">
                    <tr v-for="discount in discounts.data" :key="discount.id">
                        <td class="text-center">{{ discount.discount_code }}</td>
                        <td class="text-center">
                            {{ discount.discount_type == "price-discount" ?
                                `${numberWithCommas(discount.price_discount)} vnđ` : "" }}
                        </td>
                        <td class="text-center">
                            {{ discount.discount_type == "price-discount" ?
                                "" : `${numberWithCommas(discount.percentage_discount)} %` }}
                        </td>
                        <td class="text-center">
                            {{ discount.ticket_class ? discount.ticket_class.name : "Mọi hạng vé" }}
                        </td>
                        <td class="text-center">
                            {{ discount.start_date ?
                                `${convertDate(discount.start_date)}-${convertDate(discount.end_date)}`
                                : "Áp dụng mọi thời gian" }}
                        </td>
                        <td class="text-center">
                            {{ numberWithCommas(discount.quantity_used ?? 0) + "/" + numberWithCommas(discount.quantity) }}
                        </td>
                        <td class="text-center">
                            {{ discount.note ?? "Không có ghi chú" }}
                        </td>
                        <td class="d-flex justify-content-start">
                            <button class="btn btn-primary btn-sm text-white" data-bs-toggle="modal"
                                data-bs-target="#modal-discount" @click="showModalEdit(discount)">Sửa</button>
                            <button v-if="discount.deleteable" class="btn btn-danger btn-sm ms-1"
                                @click="deleteDiscount(discount.id, discount.discount_code)">Xóa</button>
                        </td>
                    </tr>
                </template>
                <template v-else>
                    <tr>
                        <td class="text-center fw-medium" colspan="8">Không có mã giảm giá</td>
                    </tr>
                </template>
            </tbody>
        </table>
        <div class="row">
            <div class="d-flex">
                <div class="ms-auto">
                    <button class="btn btn-light m-1" :class="{ 'active': discounts.current_page == page }"
                        v-for="page in discounts.last_page" :key="page" @click="changePage(page)">
                        {{ page }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    <modal-upsert-discount :isCreate="isCreate" :ticketClasses="ticketClasses" :discountData="selectedDiscount"
        @afterCreate="handleAfterCreate" @afterUpdate="handleAfterUpdate" />
</template>
<script setup>
import { ref, onMounted } from "vue";
import ModalUpsertDiscount from "../../../components/admin/ModalUpsertDiscount.vue";
import { listTicketClassAPI } from "../../../api/admin/ticketClass";
import { useRoute } from "vue-router";
import { HttpStatusCode } from "axios";
import { deleteDiscountAPI, getListDiscountAPI } from "../../../api/admin/discounts";
import { numberWithCommas } from "../../../helpers/number";
import { convertDate } from "../../../helpers/date";
import { getEventAPI } from "../../../api/admin/events";
import { useToast } from "vue-toastification";

const route = useRoute();
const toast = useToast();
const eventId = route.params.eventId;
let discounts = ref({
    data: [],
    current_page: 1,
    last_page: 1,
    per_page: 0,
});
let isCreate = ref(false);
let ticketClasses = ref([]);
let selectedDiscount = ref({});
let event = ref({});

const setModeCreate = () => {
    selectedDiscount.value = {};
    isCreate.value = true;
}

onMounted(async () => {
    await fetchListTicketClasses();
    await fetchListDiscounts();
    await getEvent();
})

const fetchListTicketClasses = async () => {
    let response = await listTicketClassAPI(eventId);
    let data = response.status == HttpStatusCode.Ok ? response.data : [];
    ticketClasses.value = data;
}

const fetchListDiscounts = async () => {
    let response = await getListDiscountAPI(eventId, discounts.value.current_page);
    discounts.value.data = response.data?.data ?? [];
    discounts.value.last_page = response.data?.last_page;
    discounts.value.per_page = response.data?.per_page;
}

const changePage = async (page) => {
    discounts.value.current_page = page;
    await fetchListDiscounts();
}

const showModalEdit = (discount) => {
    isCreate.value = false;
    selectedDiscount.value = discount;
}

const handleAfterCreate = (dataCreate) => {
    discounts.value.data = [dataCreate, ...discounts.value.data];
}

const handleAfterUpdate = (dataUpdate) => {
    let updatedIndex = discounts.value.data.findIndex(discount => discount.id == dataUpdate.id);
    discounts.value.data[updatedIndex] = dataUpdate;
}

const getEvent = async () => {
    let response = await getEventAPI(Number(route.params.eventId));
    switch (response.status) {
        case HttpStatusCode.Ok:
            event.value = response.data;
            break;
        default:
            console.log(response.data);
    }
}

const deleteDiscount = async (discountId, discountCode) => {
    let isConfirm = confirm(`Xóa bỏ mã giảm giá ${discountCode}?`);
    if (!isConfirm) return;
    let response = await deleteDiscountAPI(discountId);
    if (response.status == HttpStatusCode.Ok) {
        let deleteIndex = discounts.value.data.findIndex(discount => discount.id == discountId);
        discounts.value.data.splice(deleteIndex, 1);
    } else {
        toast.error(response.data.message);
    }
}
</script>