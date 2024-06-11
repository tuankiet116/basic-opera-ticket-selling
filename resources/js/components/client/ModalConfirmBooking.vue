<template>
    <modal :modalTitle="$t('booking_page.modal_title_cf')" isCenter>
        <template #body>
            <template v-if="bookings.some(booking => booking.seats.length)">
                <div v-for="booking in bookings" :key="booking.hall" class="content">
                    <template v-if="booking.seats.length">
                        <h5>{{ $t("booking_page.hall") }} {{ booking.hall }} </h5>
                        <div class="card mb-2 shadow-sm" v-for="seat in booking.seats" :key="`${seat}_${booking.hall}`">
                            <div class="card-body py-1">
                                <div class="row col-12">
                                    <div class="col">
                                        <div class="row">
                                            <div
                                                class="col-md-1 col-2 p-0 m-0 d-flex align-items-center justify-content-center">
                                                <div class="border rounded-circle"
                                                    :style="{ 'background-color': seat.class.ticket_class.color, width: '20px', height: '20px' }">
                                                </div>
                                            </div>
                                            <div class="col-auto ps-1">
                                                <p class="fw-bold mb-0">{{ seat.name }}</p>
                                                <span>{{ seat.class.ticket_class.name }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col d-flex align-items-center justify-content-center">
                                        <p class="fw-bold p-0 m-0">{{ numberWithCommas(seat.class.ticket_class.price) }}
                                            VND
                                        </p>
                                    </div>
                                    <div class="col-1 d-flex align-items-center justify-content-center">
                                        <button class="btn btn-link"
                                            @click="$emit('unSelectSeat', seat.name, booking.hall)">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
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
                        <div class="row col-12 px-2">
                            <div class="col">
                                <p class="text-end fw-bold">{{ $t("booking_page.total") }}: </p>
                            </div>
                            <div class="col">
                                <p class="fw-bold p-0 m-0 text-center">{{ numberWithCommas(total()) }} VND</p>
                            </div>
                            <div class="col-1"></div>
                        </div>
                    </template>
                </div>
            </template>
            <template v-else>
                <div class="card shadow-sm">
                    <div class="card-body">
                        <p class="text-center fw-bold">{{ $t("booking_page.no_seat_selected") }}</p>
                    </div>
                </div>
            </template>
        </template>
        <template #footer>
            <div class="footer">
                <button type="button" class="btn btn-secondary mb-sm-0 mb-2 me-sm-2 me-0" data-bs-dismiss="modal">
                    {{ $t('booking_page.btn_close_modal') }}
                </button>
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal" @click="$emit('confirm')">
                    {{ $t('booking_page.btn_cf_modal') }}
                </button>
            </div>
        </template>
    </modal>
</template>
<script setup>
import Modal from "../Modal.vue";
import { numberWithCommas } from "../../helpers/number";

const props = defineProps({
    bookings: {
        type: Array,
        required: true
    }
});

defineEmits(["unSelectSeat", "confirm"]);

const total = () => {
    return props.bookings.reduce((previous, currentBooking) => {
        return previous + currentBooking.seats.reduce((previousPrice, currentSeat) => {
            return previousPrice + currentSeat.class.ticket_class.price;
        }, 0);
    }, 0)
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
}
</style>