<template>
    <div class="container text-center pt-5">
        <h1 class="fs-3 pt-5">{{ $t("booking_page.booking_for") }} {{ event.name }}</h1>
        <div class="d-flex justify-content-center">
            <button class="btn mx-1" :class="hall.id == hallSelected ? 'btn-primary text-white' : 'btn-light'"
                v-for="hall in halls" @click="selectHall(hall.id)" :key="hall.id">
                {{ hall.name }}
            </button>
        </div>
    </div>
    <Hall1 v-if="hallSelected == 1" @selectSeat="selectSeat" :selected="seatSelectedHall1"
        :seat-ticket-classes="seatTicketClasses" :bookings="bookings" />
    <Hall2 v-else-if="hallSelected == 2" @selectSeat="selectSeat" :selected="seatSelectedHall2"
        :seat-ticket-classes="seatTicketClasses" :bookings="bookings" />
    <div
        class="box position-sticky border border-1 bg-white box-setting p-3 px-4 z-1 shadow-sm rounded col-sm-8 col-md-6 col-lg-4 col-12 start-0 end-0 m-auto">
        <div class="row position-relative">
            <button
                class="position-absolute btn btn-light top-0 col-1 end-0 d-flex justify-content-center align-items-center p-1 w-fit"
                @click="isZoomOutBox = !isZoomOutBox">
                <svg v-if="!isZoomOutBox" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                    class="bi bi-dash-square" viewBox="0 0 16 16">
                    <path
                        d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z" />
                    <path d="M4 8a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7A.5.5 0 0 1 4 8" />
                </svg>
                <svg v-else xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                    class="bi bi-plus-square" viewBox="0 0 16 16">
                    <path
                        d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z" />
                    <path
                        d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4" />
                </svg>
            </button>
            <h5 class="fw-medium">{{ $t("booking_page.booking_info") }}</h5>
            <div class="col-12 mt-2 ">
                <div v-if="!isZoomOutBox" class="d-flex justify-content-center flex-wrap">
                    <div class="d-flex" v-for="ticketClass in ticketClasses" :key="ticketClass.id">
                        <div>{{ ticketClass.name }}</div>
                        <div class="mx-1" :style="'background-color: ' + ticketClass.color"
                            style="width: 20px; height: 20px"></div>
                        <span>:
                            {{ numberWithCommas(ticketClass.price) }} vnđ</span>
                        <span class="mx-1">|</span>
                    </div>
                    <div class="d-flex">
                        <div>{{ $t("booking_page.booked") }}</div>
                        <div class="mx-1" :style="'background-color: ' + BOOKED_COLOR"
                            style="width: 20px; height: 20px"></div>
                    </div>
                </div>
                <div v-if="!isZoomOutBox" class="col-12">
                    <p style="word-break: break-all">
                        <span class="fw-medium">{{ $t("booking_page.hall") }} 1: </span>
                        <span v-if="seatSelectedHall1.length" v-for="(seat, index) in seatSelectedHall1" :key="seat">
                            {{
                                seat +
                                (index == seatSelectedHall1.length - 1
                                    ? ""
                                    : ",")
                            }}
                        </span>
                        <span v-else>{{ $t("booking_page.booking_none") }}</span>
                    </p>
                    <p style="word-break: break-all">
                        <span class="fw-medium">{{ $t("booking_page.hall") }} 2: </span>
                        <span v-if="seatSelectedHall2.length" v-for="(seat, index) in seatSelectedHall2">
                            {{
                                seat +
                                (index == seatSelectedHall2.length - 1
                                    ? ""
                                    : ",")
                            }}
                        </span>
                        <span v-else>{{ $t("booking_page.booking_none") }}</span>
                    </p>
                </div>
                <div class="row">
                    <div class="col-8 row align-items-center">
                        <div>
                            <span class="fw-medium fs-5">
                                {{ $t("booking_page.total") }}:
                            </span>
                            <span class="fs-5">{{ numberWithCommas(total) }} vnđ</span>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary text-white col-4" @click="confirm">
                        {{ $t("booking_page.confirm_payment") }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useRoute, useRouter } from "vue-router";
import Hall1 from "../components/seats/Hall1.vue";
import Hall2 from "../components/seats/Hall2.vue";
import { ref, toRef, onMounted, onUnmounted } from "vue";
import {
    getBookingsAPI,
    getEventAPI,
    getTicketClassesByEventAPI,
} from "../api/event";
import { numberWithCommas } from "../helpers/number";
import { useStoreBooking } from "../pinia";
import { useToast } from "vue-toastification";
import { HttpStatusCode } from "axios";

const BOOKED_COLOR = "black";
const route = useRoute();
const router = useRouter();
const toast = useToast();
let halls = [
    {
        name: "Khán phòng 1",
        id: 1,
    },
    {
        name: "Khán phòng 2",
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
let eventId = route.params.eventId;
let isZoomOutBox = ref(false);
const storeBookings = useStoreBooking();

onMounted(async () => {
    await getTicketClass();
    await getEvent();
    await getBookingStatus();
    const seatsBookings = storeBookings.seatBooking;
    for (let i = 0; i < seatsBookings.length; i++) {
        let bookings = seatsBookings[i];
        for (let j = 0; j < bookings.seats.length; j++) {
            selectSeat(bookings.seats[j], bookings.hall);
        }
    }

    window.Echo.channel(`client-booking-event-${event.value.id}`)
        .listen("ClientBookingTicket", (e) => {
            let seats = e.seats.map((seat) => {
                selectSeat(seat.name, seat.hall, true);
                return creatBookingItem(seat.name, seat.hall);
            });
            bookings.value.push(...seats);
        })
        .listen("ClientRemoveBookingTicket", (e) => {
            let seats = e.seats;
            for (let i = 0; i < seats.length; i++) {
                let seat = seats[i];
                let index = bookings.value.findIndex(
                    (book) => book.seat == seat.name && book.hall == seat.hall
                );
                if (index > -1) bookings.value.splice(index, 1);
            }
        });
});

onUnmounted(() => {
    window.Echo.leave(`client-booking-event-${event.value.id}`);
});

const selectHall = (hallId) => {
    hallSelected.value = hallId;
};

const selectSeat = (seatName, hall = null, isBooked = false) => {
    let seatSelected = toRef(seatSelectedHall1);
    if ((hall && hall == 2) || hallSelected.value == 2) {
        seatSelected = toRef(seatSelectedHall2);
    }
    let currentSeat = seatSelected.value.findIndex((seat) => seat == seatName);
    let seatBooked = bookings.value?.find(
        (book) => book.hall == hallSelected.value && book.seat == seatName
    );
    let isSeatValid = seatTicketClasses.value.find(
        (ticketclass) =>
            ticketclass.seat.name == seatName &&
            ticketclass.seat.hall == hallSelected.value
    );

    if (currentSeat > -1) {
        seatSelected.value.splice(currentSeat, 1);
        total.value -= isSeatValid.ticket_class.price;
    } else if (!seatBooked && isSeatValid && !isBooked) {
        total.value += isSeatValid.ticket_class.price;
        seatSelected.value.push(seatName);
    }
};

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
        hall: seatHall,
        color: BOOKED_COLOR,
        textcolor: "white",
    };
};

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
        toast.error("Vui lòng chọn ghế");
        return;
    }
    storeBookings.setBooking(selected, eventId);
    if (!seatSelectedHall1.value.length && !seatSelectedHall2.value.length) {
        toast.error("Vui lòng chọn ghế");
        return;
    } else {
        router.push({
            name: "client-form",
        });
    }
};
</script>
