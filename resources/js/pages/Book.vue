<template>
    <div class="container text-center pt-5">
        <h1 class="fs-3 pt-5">{{ event.name?.toUpperCase() }}</h1>
        <div class="d-flex justify-content-center">
            <button class="btn mx-1" :class="hall.id == hallSelected ? 'btn-primary text-white' : 'btn-light'"
                v-for="hall in halls" @click="selectHall(hall.id)" :key="hall.id">
                {{ $t("booking_page.hall") }} {{ hall.name }}
            </button>
        </div>
    </div>
    <Hall1 v-if="hallSelected == 1" @selectSeat="selectSeatTemporary" :selected="seatSelectedHall1"
        :seat-ticket-classes="seatTicketClasses" :bookings="bookings" ref="refHall1" />
    <Hall2 v-else-if="hallSelected == 2" @selectSeat="selectSeatTemporary" :selected="seatSelectedHall2"
        :seat-ticket-classes="seatTicketClasses" :bookings="bookings" />
    <div class="box position-sticky border border-1 bg-white box-setting py-md-3 px-md-4 p-2 z-1 shadow-sm rounded 
        col-sm-8 col-md-7 col-lg-5 col-xl-4 col-12 start-0 end-0 m-auto">
        <div class="row position-relative">
            <button
                class="position-absolute btn btn-light top-0 col-1 end-0 d-flex justify-content-center align-items-center p-1 w-fit"
                @click="isZoomOutBox = !isZoomOutBox">
                <svg v-if="!isZoomOutBox" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                    class="bi bi-dash-square" viewBox="0 0 16 16">
                    <path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 
                        2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z" />
                    <path d="M4 8a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7A.5.5 0 0 1 4 8" />
                </svg>
                <svg v-else xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                    class="bi bi-plus-square" viewBox="0 0 16 16">
                    <path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1zM2 0a2 2 0 0 0-2 2v12a2 
                        2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z" />
                    <path
                        d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4" />
                </svg>
            </button>
            <h5 class="fw-medium d-none d-md-block text-responsive">{{ $t("booking_page.booking_info") }}</h5>
            <div class="col-12 mt-2">
                <p class="text-center text-danger fst-italic mb-1 fw-bold warning-text">
                    {{ $t("warning_text") }}
                </p>
                <div v-if="!isZoomOutBox" class="d-flex justify-content-center flex-wrap">
                    <div class="d-flex text-responsive" v-for="ticketClass in ticketClasses" :key="ticketClass.id">
                        <div>{{ ticketClass.name }}</div>
                        <div class="mx-1 rounded color-ticket" :style="'background-color: ' + ticketClass.color"></div>
                        <span>:
                            {{ numberWithCommas(ticketClass.price) }} vnđ</span>
                        <span class="mx-1">|</span>
                    </div>
                </div>
                <p class="fw-bold">{{ $t("booking_page.pls_complete_in") }}{{ timer }}</p>
                <div v-if="!isZoomOutBox" class="col-12 text-responsive">
                    <p style="word-break: break-all">
                        <span class="fw-medium">{{ $t("booking_page.hall") }} 1: </span>
                        <template v-if="seatSelectedHall1.length">
                            <span v-for="(seat, index) in seatSelectedHall1" :key="seat">
                                {{
                                    seat +
                                    (index == seatSelectedHall1.length - 1
                                        ? ""
                                        : ",")
                                }}
                            </span>
                        </template>
                        <span v-else>{{ $t("booking_page.booking_none") }}</span>
                    </p>
                    <p style="word-break: break-all">
                        <span class="fw-medium">{{ $t("booking_page.hall") }} 2: </span>
                        <template v-if="seatSelectedHall2.length">
                            <span v-for="(seat, index) in seatSelectedHall2" :key="index">
                                {{
                                    seat +
                                    (index == seatSelectedHall2.length - 1
                                        ? ""
                                        : ",")
                                }}
                            </span>
                        </template>
                        <span v-else>{{ $t("booking_page.booking_none") }}</span>
                    </p>
                </div>
                <div class="row text-responsive">
                    <div class="col-md-8 col-7 row align-items-center">
                        <div>
                            <span class="fw-medium">
                                {{ $t("booking_page.total") }}:
                            </span>
                            <span class="me-2"
                                :class="{ 'text-decoration-line-through text-danger': total > totalDiscount }">
                                {{ numberWithCommas(total) }} vnđ
                            </span>
                            <span v-if="total > totalDiscount">{{ numberWithCommas(totalDiscount) }} vnđ</span>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary text-white col-md-4 col-5 text-responsive"
                        data-bs-toggle="modal" data-bs-target="#modal" @click="createBookingsCart">
                        {{ $t("booking_page.confirm_payment") }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    <modal-confirm-booking :bookings="currentBookingsCart" :discount="discount" @unSelectSeat="handleUnselectSeatCart"
        @applyDiscount="handleApplyDiscount" @confirm="confirm" />
</template>

<script setup>
import { useRoute, useRouter } from "vue-router";
import Hall1 from "../components/seats/Hall1.vue";
import Hall2 from "../components/seats/Hall2.vue";
import { ref, toRef, onMounted, onUnmounted } from "vue";
import {
    generateTemporaryToken,
    getBookingsAPI,
    getBookingTemporary,
    getEventAPI,
    getTicketClassesByEventAPI,
    temporaryBookingAPI,
} from "../api/event";
import { calcTimeRemaining, numberWithCommas } from "../helpers/number";
import { useStoreBooking, useStoreTemporaryBooking } from "../pinia";
import { useToast } from "vue-toastification";
import { HttpStatusCode } from "axios";
import { useI18n } from "vue-i18n";
import { useReCaptcha } from "vue-recaptcha-v3";
import ModalConfirmBooking from "../components/client/ModalConfirmBooking.vue";

const route = useRoute();
const router = useRouter();
const toast = useToast();
const { t } = useI18n();
let halls = [
    {
        name: "1",
        id: 1,
    },
    {
        name: "2",
        id: 2,
    },
];
let hallSelected = ref(halls[0].id);
let seatSelectedHall1 = ref([]);
let seatSelectedHall2 = ref([]);
let seatTicketClasses = ref([]);
let ticketClasses = ref([]);
let bookings = ref([]);
let event = ref({});
let total = ref(0);
let totalDiscount = ref(0);
let discount = ref(null);
let timer = ref("");
let currentBookingsCart = ref([]);
let eventId = route.params.eventId;
const { executeRecaptcha, recaptchaLoaded } = useReCaptcha();
let isZoomOutBox = ref(false);
const refHall1 = ref(null);
const storeBookings = useStoreBooking();
const storeTemporaryBookings = useStoreTemporaryBooking();

onMounted(async () => {
    Promise.allSettled([getTicketClass(), getEvent(), getBookingStatus()]).then(async () => {
        if (!storeTemporaryBookings.token) {
            let resToken = await generateTemporaryToken();
            storeTemporaryBookings.setToken(resToken.data.token);
        }
        let resTemporary = await getBookingTemporary(storeTemporaryBookings.token, eventId);
        if (resTemporary.status == HttpStatusCode.Ok) {
            discount.value = resTemporary.data.discount;
            resTemporary.data.bookings.forEach((value) => {
                let bookingIndex = bookings.value.findIndex(booking => booking.seat == value.seat.name && booking.hall == value.seat.hall);
                if (bookingIndex > -1) bookings.value.splice(bookingIndex, 1);
                reselectSeat(value.seat.name, value.seat.hall);
            });
        }
        window.Echo.channel(`client-booking-event-${event.value.id}`)
            .listen("ClientBookingTicket", (e) => {
                let seats = e.seats.flatMap((seat) => {
                    let selectedSeats = seatSelectedHall1.value;
                    if (seat.hall == 2) selectedSeats = seatSelectedHall2.value;
                    let isSeatCurrentBooking = selectedSeats.find(s => s == seat.name);
                    if (isSeatCurrentBooking) return [];
                    return creatBookingItem(seat.name, seat.hall);
                });
                bookings.value.push(...seats);
            })
            .listen("ClientRemoveBookingTicket", (e) => {
                let seats = e.seats;
                for (let i = 0; i < seats.length; i++) {
                    let seat = seats[i];
                    let indexBooking = bookings.value.findIndex(book => book.seat == seat.name && book.hall == seat.hall);
                    if (indexBooking > -1) bookings.value.splice(indexBooking, 1);
                }
            });
    })

    setTimeout(async function () {
        refHall1.value.minimap?.reset();
    }, 30);

    let endTime = new Date().getTime() + storeTemporaryBookings.timeRemaining;
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

onUnmounted(async () => {
    window.Echo.leave(`client-booking-event-${event.value.id}`);
    if (route.name == "client-form") {
        storeTemporaryBookings.setTimeRemaining(calcTimeRemaining(timer.value));
        return;
    };
    storeBookings.setBooking([], null);
    unselectAllSeats();
});

const reselectSeat = (seatName, hall) => {
    if (bookings.value.find((booking => booking.seat == seatName && booking.hall == hall))) return null;
    let seatSelected = toRef(seatSelectedHall1);
    if ((hall && hall == 2) || hallSelected.value == 2) {
        seatSelected = toRef(seatSelectedHall2);
    }
    let currentSeatIdx = seatSelected.value.findIndex((seat) => seat == seatName);
    let isSeatTicketValid = seatTicketClasses.value.find((ticketclass) => ticketclass.seat.name == seatName && ticketclass.seat.hall == hallSelected.value);

    let ticketClassId = isSeatTicketValid?.ticket_class.id;
    let seatId = isSeatTicketValid?.seat.id;
    let price = Number(isSeatTicketValid?.ticket_class.price ?? 0);
    let discountPrice = price;

    if (discount.value && (ticketClassId == discount.value.ticket_class || !discount.value.ticket_class) && discount.value.applied.includes(seatId)) {
        discountPrice = discountPrice - (discount.value.discount_type == "percentage-discount" ?
            discount.value.percentage_discount * discountPrice / 100 : discount.value.price_discount);
    }

    let factor = 1;
    if (currentSeatIdx > -1) {
        factor = -1;
        seatSelected.value.splice(currentSeatIdx, 1);
    } else {
        seatSelected.value.push(seatName);
    }
    total.value += (price * factor);
    totalDiscount.value = Math.round(totalDiscount.value + discountPrice * factor);
    return !(currentSeatIdx > -1);
}

const selectHall = (hallId) => {
    hallSelected.value = hallId;
};

const selectSeatTemporary = (seatName, hall = null) => {
    let isSelect = reselectSeat(seatName, hall);
    if (isSelect !== null) {
        temporaryBooking(seatName, hallSelected.value, isSelect);
    }
};

const unselectAllSeats = async () => {
    const token = await executeRecaptcha('submit');
    let data = {
        "g-recaptcha-response": token,
        bookings: [
            {
                hall: 1,
                seats: seatSelectedHall1.value,
            },
            {
                hall: 2,
                seats: seatSelectedHall2.value,
            }
        ],
        event_id: Number(eventId),
        token: storeTemporaryBookings.token,
        is_booking: false
    };
    await temporaryBookingAPI(data);
    seatSelectedHall1.value = [];
    seatSelectedHall2.value = [];
    currentBookingsCart.value = [];
    totalDiscount.value = 0;
    total.value = 0;
}

const temporaryBooking = async (seat, hall, isBooking) => {
    await recaptchaLoaded();
    const token = await executeRecaptcha('submit');
    let data = {
        "g-recaptcha-response": token,
        bookings: [{ seats: [seat], hall: hall }],
        event_id: Number(eventId),
        token: storeTemporaryBookings.token,
        is_booking: isBooking
    };
    let response = await temporaryBookingAPI(data);
    switch (response.status) {
        case HttpStatusCode.Ok:
            storeTemporaryBookings.setToken(response.data.token);
            break;
        default:
            reselectSeat(seat, hall);
    }
}

const getTicketClass = async () => {
    let response = await getTicketClassesByEventAPI(Number(eventId));
    seatTicketClasses.value = response.data.seatTicketClasses;
    ticketClasses.value = response.data.ticketClasses;
};

const getEvent = async () => {
    let response = await getEventAPI(Number(eventId));
    if (response.status != HttpStatusCode.Ok) router.push("/error/notfound");
    event.value = response.data;
};

const getBookingStatus = async () => {
    let response = await getBookingsAPI(Number(eventId));
    if (response.status != HttpStatusCode.Ok) router.push("/error/notfound");
    bookings.value = response.data.map((booking) =>
        creatBookingItem(booking.seat.name, booking.seat.hall)
    );
};

const creatBookingItem = (seatName, seatHall) => {
    return {
        seat: seatName,
        hall: seatHall
    };
};

const handleApplyDiscount = (discountData, totalPriceDiscount) => {
    discount.value = discountData;
    totalDiscount.value = totalPriceDiscount;
}

const createBookingsCart = () => {
    let seatsHall1 = makeBookingWithClass(seatSelectedHall1.value, 1);
    let seatsHall2 = makeBookingWithClass(seatSelectedHall2.value, 2);
    currentBookingsCart.value = [
        {
            hall: 1,
            seats: seatsHall1,
        },
        {
            hall: 2,
            seats: seatsHall2,
        },
    ]
}

const makeBookingWithClass = (seatSelected, hall) => {
    return seatSelected.map(seat => {
        let ticketClass = seatTicketClasses.value.find(
            (ticketclass) =>
                ticketclass.seat.name == seat &&
                ticketclass.seat.hall == hall
        )
        return {
            id: ticketClass.seat.id,
            name: seat,
            class: ticketClass
        }
    })
}

const handleUnselectSeatCart = (seat, hall) => {
    selectSeatTemporary(seat, hall);
    createBookingsCart();
}

const confirm = () => {
    let selected = [
        {
            hall: 1,
            seats: seatSelectedHall1.value,
        },
        {
            hall: 2,
            seats: seatSelectedHall2.value,
        },
    ];
    if (!selected.length) {
        toast.error(t("booking_page.pls_select_seat"));
        return;
    }
    storeBookings.setBooking(selected, eventId);
    if (!seatSelectedHall1.value.length && !seatSelectedHall2.value.length) {
        toast.error(t("booking_page.pls_select_seat"));
        return;
    } else {
        router.push({
            name: "client-form",
        });
    }
};
</script>
<style scoped>
.text-responsive {
    @media screen and (max-width: 1081px) {
        font-size: 12px !important;
    }
}

.color-ticket {
    height: 20px;
    width: 20px;

    @media screen and (max-width: 1081px) {
        width: 14px !important;
        height: 14px !important;
    }
}

.warning-text {
    font-size: 15px !important;
}

@media screen and (max-width: 576px) {
    .warning-text {
        font-size: 10px !important;
    }
}

@media screen and (max-width: 748px) {
    .warning-text {
        font-size: 12px !important;
    }
}
</style>
