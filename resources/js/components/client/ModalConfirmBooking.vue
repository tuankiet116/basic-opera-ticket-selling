<template>
    <modal :modalTitle="$t('booking_page.modal_title_cf')" isCenter>
        <template #body>
            <template v-if="groupBookings.some(booking => Object.keys(booking.seats).length)">
                <div v-for="booking in groupBookings" :key="booking.hall" class="content">
                    <template v-if="Object.keys(booking.seats).length">
                        <h6>{{ $t("booking_page.hall") }}: {{ booking.hall }} </h6>
                        <div class="card mb-2 shadow-sm" v-for="(dataSeats, index) in booking.seats" :key="index">
                            <div class="row my-2 mx-0 align-items-center">
                                <div class="col-1 p-0 m-0 mx-1 border rounded-circle container-ticket-color"
                                    :style="{ 'background-color': dataSeats[0].color }">
                                </div>
                                <p class="col-auto m-0 p-0 fw-medium text-responsive">
                                    {{ $t("booking_page.ticket_class") }}: {{ dataSeats[0].class }}
                                </p>
                            </div>
                            <hr class="m-0 mb-1" />
                            <div class="card-body p-1">
                                <div v-for="seat in dataSeats" :key="seat.name + booking.hall">
                                    <div class="row col-12 text-responsive m-0">
                                        <p class="col fw-medium mb-0">{{ seat.name }}</p>
                                        <p class="col fw-medium p-0 m-0">
                                            <span
                                                :class="{ 'text-decoration-line-through text-danger me-2': seat.price > seat.discountPrice }">
                                                {{ numberWithCommas(seat.price) }} VND
                                            </span>
                                            <span v-if="seat.price > seat.discountPrice">{{
                                                numberWithCommas(seat.discountPrice) }} VND</span>
                                        </p>
                                        <div class="col-2 d-flex align-items-center justify-content-end">
                                            <button class="btn btn-link"
                                                @click="$emit('unSelectSeat', seat.name, booking.hall)">
                                                <svg class="bi bi-trash d-none d-md-block" width="20px" height="20px"
                                                    xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                                    viewBox="0 0 16 16">
                                                    <path
                                                        d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z" />
                                                    <path
                                                        d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z" />
                                                </svg>
                                                <svg class="bi bi-trash d-block d-md-none" width="12px" height="12px"
                                                    xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                                    viewBox="0 0 16 16">
                                                    <path
                                                        d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z" />
                                                    <path
                                                        d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
                <div class="row col-12 px-2">
                    <div class="col-md col-5 p-0">
                        <p class="text-end fw-bold text-responsive m-0">{{ $t("booking_page.total") }}: </p>
                    </div>
                    <div class="col text-responsive mb-1">
                        <p class="fw-bold p-0 m-0 text-start"
                            :class="{ 'text-decoration-line-through text-danger': totalPrice > totalPriceDiscount }">
                            {{ numberWithCommas(totalPrice) }} VND
                        </p>
                        <p v-if="totalPrice > totalPriceDiscount" class="fw-bold p-0 m-0 text-start">
                            {{ numberWithCommas(totalPriceDiscount) }} VND
                        </p>
                    </div>
                    <div class="col-2"></div>
                </div>
                <div class="row col-12 px-2">
                    <div class="col-5 p-0 d-flex align-items-center justify-content-end">
                        <p class="fw-medium text-end text-responsive m-0">{{ $t("discount.input_discount_code") }}:</p>
                    </div>
                    <div class="col-4">
                        <input type="text" class="form-control" v-model="discountCode" />
                    </div>
                    <div class="col-3 p-0">
                        <button :disabled="isLoading"
                            class="btn btn-primary text-responsive h-100 text-white d-flex justify-content-center align-items-center w-100"
                            @click="applyDiscount">
                            <div v-if="isLoading" class="spinner-border text-light me-2 spinner" role="status"></div>
                            <span>{{ $t("discount.apply") }}</span>
                        </button>
                    </div>
                </div>
            </template>
            <template v-else>
                <div class="card shadow-sm">
                    <div class="card-body">
                        <p class="text-center fw-medium text-responsive m-0 p-0">
                            {{ $t("booking_page.no_seat_selected") }}
                        </p>
                    </div>
                </div>
            </template>
        </template>
        <template #footer>
            <div class="footer">
                <button type="button" class="btn btn-secondary mb-sm-0 mb-2 me-sm-2 me-0 text-responsive"
                    data-bs-dismiss="modal">
                    {{ $t('booking_page.btn_close_modal') }}
                </button>
                <button type="button" class="btn btn-primary text-responsive" data-bs-dismiss="modal"
                    @click="$emit('confirm')">
                    {{ $t('booking_page.btn_cf_modal') }}
                </button>
            </div>
        </template>
    </modal>
</template>
<script setup>
import Modal from "../Modal.vue";
import { numberWithCommas } from "../../helpers/number";
import { ref, watch, computed } from "vue";
import { applyDiscountAPI } from "../../api/discount";
import { useStoreTemporaryBooking } from "../../pinia";
import { useReCaptcha } from "vue-recaptcha-v3";
import { HttpStatusCode } from "axios";
import { useToast } from "vue-toastification";

const toast = useToast();
const storeTemporaryBooking = useStoreTemporaryBooking();
const { executeRecaptcha, recaptchaLoaded } = useReCaptcha();
const props = defineProps({
    bookings: {
        type: Array,
        required: true
    },
    discount: {
        required: true
    }
});

let groupBookings = ref([]);
let discountCode = ref("");
let discount = ref(null);
let isLoading = ref(false);

const emit = defineEmits(["unSelectSeat", "confirm", "applyDiscount"]);

const totalPrice = computed(() => {
    return groupBookings.value.reduce((previous, currentBooking) => {
        return previous + Object.values(currentBooking.seats).reduce((previousPriceTicketClass, currentSeatsTicketClass) => {
            return previousPriceTicketClass + currentSeatsTicketClass.reduce((previousPrice, currentSeat) => {
                return previousPrice + currentSeat.price;
            }, 0);
        }, 0);
    }, 0);
})

const totalPriceDiscount = computed(() => {
    return Math.round(groupBookings.value.reduce((previous, currentBooking) => {
        return previous + Object.values(currentBooking.seats).reduce((previousPriceTicketClass, currentSeatsTicketClass) => {
            return previousPriceTicketClass + currentSeatsTicketClass.reduce((previousPrice, currentSeat) => {
                return previousPrice + Number(currentSeat.discountPrice);
            }, 0);
        }, 0);
    }, 0));
})

const applyDiscount = async () => {
    if (!discountCode.value) return;
    isLoading.value = true;
    await recaptchaLoaded();
    const token = await executeRecaptcha('submit');
    let data = {
        discount_code: discountCode.value,
        token: storeTemporaryBooking.token,
        "g-recaptcha-response": token
    };
    let response = await applyDiscountAPI(data);
    switch (response.status) {
        case HttpStatusCode.Ok:
            if (response.data.discount_code) {
                discount.value = response.data;
            }
            break;
        default:
            discount.value = null;
            toast.error(response.data.message);
    }
    groupBookings.value = calculateBookings(props.bookings);
    emit("applyDiscount", discount.value, totalPriceDiscount.value);
    isLoading.value = false;
}

watch(() => props.bookings, (bookings, oldBookings) => {
    groupBookings.value = calculateBookings(bookings);
    if (groupBookings.value.every(booking => !Object.keys(booking.seats).length)) {
        discount.value = null;
        discountCode.value = null;
        emit("applyDiscount", null, 0);
    }
})

watch(() => props.discount, (newDiscount) => {
    if (newDiscount) {
        discountCode.value = newDiscount.discount_code;
    }
    discount.value = newDiscount;
});

const calculateBookings = (bookings) => {
    return bookings.map(booking => {
        let dataSeats = {};
        booking.seats.forEach((seat => {
            let ticketClassId = seat.class.ticket_class.id;
            let discountPrice = seat.class.ticket_class.price;
            if (discount.value && (ticketClassId == discount.value.ticket_class || !discount.value.ticket_class) && discount.value.applied.includes(seat.id)) {
                discountPrice = discountPrice - (discount.value.discount_type == "percentage-discount" ?
                    discount.value.percentage_discount * discountPrice / 100 : discount.value.price_discount);
            }
            let seatData = {
                name: seat.name,
                price: seat.class.ticket_class.price,
                discountPrice: discountPrice,
                class: seat.class.ticket_class.name,
                color: seat.class.ticket_class.color
            }
            if (dataSeats[ticketClassId]) dataSeats[ticketClassId].push(seatData)
            else dataSeats[ticketClassId] = [seatData];
        }))
        return {
            hall: booking.hall,
            seats: dataSeats
        }
    })
}
</script>
<style scoped lang="scss">
@media only screen and (max-width: 600px) {
    .footer {
        width: 100% !important;

        button {
            width: 100% !important;
        }
    }

    .container-ticket-color {
        width: 10px;
        height: 10px;
    }

    .text-responsive {
        font-size: 12px !important;
    }

    .spinner {
        width: 10px !important;
        height: 10px !important;
    }
}

.container-ticket-color {
    width: 15px;
    height: 15px;
}

.text-responsive {
    font-size: 15px;
}

.spinner {
    width: 20px;
    height: 20px;
}
</style>