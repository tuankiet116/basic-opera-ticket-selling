<template>
    <BookSuccess v-if="isSucess" />
    <div v-else-if="!notfound" class="container my-5">
        <div class="p-4 border shadow">
            <form id="form-client">
                <h3 class="text-center">Nhập thông tin liên hệ</h3>
                <div class="mb-3">
                    <label for="email" class="form-label">Bạn muốn nhận vé ở đâu?</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="receive-ticket"
                            id="radio-receive-ticket-opera" v-model="isReceiveTicketInOpera" value="true">
                        <label class="form-check-label" for="radio-receive-ticket-opera">
                            Tại nhà hát
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="receive-ticket"
                            id="radio-receive-ticket-address" v-model="isReceiveTicketInOpera" value="false" checked>
                        <label class="form-check-label" for="radio-receive-ticket-address">
                            Gửi về địa chỉ của bạn
                        </label>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="name" class="form-label">Bạn tên là gì:</label>
                    <input type="text" class="form-control" id="name" v-model="client.name">
                    <small v-if="errors.name" class="text-danger">{{ errors.name[0] }}</small>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Địa chỉ Email của bạn:</label>
                    <input type="email" class="form-control" id="email" v-model="client.email">
                    <small v-if="errors.email" class="text-danger">{{ errors.email[0] }}</small>
                </div>
                <div class="mb-3">
                    <label for="phone-number" class="form-label">Số điện thoại của bạn:</label>
                    <input type="text" class="form-control" id="phone-number" v-model="client.phone_number">
                    <small v-if="errors.phone_number" class="text-danger">{{ errors.phone_number[0] }}</small>
                </div>
                <div class="mb-3" v-if="isReceiveTicketInOpera == 'false'">
                    <label class="form-check-label" for="address">Địa chỉ của bạn:</label>
                    <input type="text" class="form-control" id="address" v-model="client.address">
                    <small v-if="errors.address" class="text-danger">{{ errors.address[0] }}</small>
                </div>
                <button type="click" @click.prevent="submitForm" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>
    <not-found v-else></not-found>
</template>
<script setup>
    import { ref, onMounted } from "vue";
    import { useStoreBooking } from "../pinia.ts";
    import { useReCaptcha } from "vue-recaptcha-v3";
    import { bookingAPI } from "../api/event.ts";
    import NotFound from "./errors/NotFound.vue";
    import { HttpStatusCode } from "axios";
    import { useToast } from "vue-toastification";
    import { useRouter } from "vue-router";
    import BookSuccess from "./success/BookSuccess.vue";

    const { executeRecaptcha, recaptchaLoaded } = useReCaptcha();
    const toast = useToast();
    const router = useRouter();
    let errors = ref({});
    let isSucess = ref(false);
    let isReceiveTicketInOpera = ref('false');
    let notfound = ref(false);
    let bookings = ref([]);
    let client = ref({
        name: "",
        email: "",
        phone_number: "",
        address: ""
    });

    onMounted(() => {
        let bookingStore = useStoreBooking().seatBooking;
        if (!bookingStore.length || bookingStore[0].seats.length == 0 && bookingStore[1].seats.length == 0) {
            notfound.value = true;
        } else {
            bookings.value = bookingStore;
        }
    });

    const submitForm = async () => {
        await recaptchaLoaded();
        const token = await executeRecaptcha('submit');
        let data = {
            ...client.value,
            event_id: useStoreBooking().eventBooking,
            is_receive_in_opera: isReceiveTicketInOpera.value == 'true' ? true : false,
            bookings: bookings.value,
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
                    toast.error("Sự kiện chưa được mở bán!");
                }
                errors.value = dataRes.errors;
        }
    }
</script>