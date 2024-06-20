<template>
    <BookSuccess v-if="isSucess" />
    <div v-else-if="!notfound" class="container my-5">
        <div class="p-4 border shadow">
            <form id="form-client">
                <h3 class="text-center">{{ $t("form_contact.title") }}</h3>
                <p class="fw-bold">{{ $t("booking_page.pls_complete_in") }}{{ timer }}</p>
                <div class="mb-3">
                    <label for="email" class="form-label">{{ $t("form_contact.receive_address") }}</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="receive-ticket"
                            id="radio-receive-ticket-opera" v-model="isReceiveTicketInOpera" value="true">
                        <label class="form-check-label" for="radio-receive-ticket-opera">
                            {{ $t("form_contact.at_opera_house") }}
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="receive-ticket"
                            id="radio-receive-ticket-address" v-model="isReceiveTicketInOpera" value="false" checked>
                        <label class="form-check-label" for="radio-receive-ticket-address">
                            {{ $t("form_contact.at_your_address") }}
                        </label>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="name" class="form-label">{{ $t("form_contact.name") }}:</label>
                    <input type="text" class="form-control" id="name" v-model="client.name">
                    <small v-if="errors.name" class="text-danger">{{ errors.name[0] }}</small>
                </div>
                <div class="mb-3">
                    <label for="id_number" class="form-label">{{ $t("form_contact.id_number") }}:</label>
                    <input type="text" class="form-control" id="id_number" v-model="client.id_number">
                    <small v-if="errors.id_number" class="text-danger">{{ errors.id_number[0] }}</small>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">{{ $t("form_contact.email") }}:</label>
                    <input type="email" class="form-control" id="email" v-model="client.email">
                    <small v-if="errors.email" class="text-danger">{{ errors.email[0] }}</small>
                </div>
                <div class="mb-3">
                    <label for="phone-number" class="form-label">{{ $t("form_contact.phone_number") }}:</label>
                    <input type="text" class="form-control" id="phone-number" v-model="client.phone_number">
                    <small v-if="errors.phone_number" class="text-danger">{{ errors.phone_number[0] }}</small>
                </div>
                <div class="mb-3" v-if="isReceiveTicketInOpera == 'false'">
                    <label class="form-check-label" for="address">{{ $t("form_contact.address") }}:</label>
                    <input type="text" class="form-control" id="address" v-model="client.address">
                    <small v-if="errors.address" class="text-danger">{{ errors.address[0] }}</small>
                </div>
                <button type="click" @click.prevent="goBack" class="btn btn-light me-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                        class="bi bi-arrow-left" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 
                            .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8" />
                    </svg>
                    {{ $t("form_contact.back") }}
                </button>
                <button type="click" @click.prevent="submitForm" class="btn btn-primary text-white">
                    {{ $t("form_contact.confirm") }}
                </button>
            </form>
        </div>
    </div>
    <not-found v-else></not-found>
</template>
<script setup>
import { ref, onMounted, onUnmounted } from "vue";
import { useStoreBooking, useStoreTemporaryBooking } from "../pinia.ts";
import { useReCaptcha } from "vue-recaptcha-v3";
import { bookingAPI } from "../api/event.ts";
import NotFound from "./errors/NotFound.vue";
import { HttpStatusCode } from "axios";
import { useToast } from "vue-toastification";
import { useRouter } from "vue-router";
import BookSuccess from "./success/BookSuccess.vue";
import { useI18n } from "vue-i18n";
import { calcTimeRemaining } from "../helpers/number.ts";

const { executeRecaptcha, recaptchaLoaded } = useReCaptcha();
const toast = useToast();
const router = useRouter();
const { t } = useI18n();
const storeBooking = useStoreBooking();
const storeTemporaryBooking = useStoreTemporaryBooking();
let errors = ref({});
let isSucess = ref(false);
let isReceiveTicketInOpera = ref('false');
let notfound = ref(false);
let bookings = ref([]);
let timer = ref("");
let client = ref({
    name: "",
    email: "",
    phone_number: "",
    address: "",
    id_number: "",
});

onMounted(() => {
    let bookingStore = storeBooking.seatBooking;
    if (!bookingStore.length || bookingStore[0].seats.length == 0 && bookingStore[1].seats.length == 0) {
        notfound.value = true;
    } else {
        bookings.value = bookingStore;
    }

    let endTime = new Date().getTime() + storeTemporaryBooking.timeRemaining;
    let x = setInterval(function () {
        let now = new Date().getTime();
        let distance = endTime - now;
        let minutes = String(Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60))).padStart(2, '0');
        let seconds = String(Math.floor((distance % (1000 * 60)) / 1000)).padStart(2, '0');
        if (distance < 0) {
            clearInterval(x);
            minutes = seconds = "00";
            unselectAllSeats();
        }
        timer.value = minutes + ":" + seconds;
    }, 1000);
});

onUnmounted(() => {
    storeBooking.setBooking([], null);
})

const submitForm = async () => {
    await recaptchaLoaded();
    const token = await executeRecaptcha('submit');
    let data = {
        ...client.value,
        event_id: storeBooking.eventBooking,
        is_receive_in_opera: isReceiveTicketInOpera.value == 'true' ? true : false,
        bookings: bookings.value,
        token: storeTemporaryBooking.token,
        "g-recaptcha-response": token
    }
    let response = await bookingAPI(data);
    let dataRes = response.data;
    switch (response.status) {
        case HttpStatusCode.Ok:
            isSucess.value = true;
            break;
        case HttpStatusCode.InternalServerError:
            toast.error(response.data.message);
            break;
        case HttpStatusCode.UnprocessableEntity:
            if (dataRes.errors.event_id) {
                toast.error(t("form_contact.event_closed"));
            }
            errors.value = dataRes.errors;
    }
}

const goBack = () => {
    storeTemporaryBooking.setTimeRemaining(calcTimeRemaining(timer.value));
    router.back();
}
</script>