<template>
    <div v-show="event.is_openning" class="alert alert-danger position-sticky z-2 m-auto left-0 w-50" style="top: 1rem;"
        role="alert">
        Không thể sửa đổi thông tin khi sự kiện đang trong trạng thái mở bán vé!
    </div>
    <div class="container text-center pt-5 position-relative">
        <h1 class="fs-3 pt-5">Cài đặt hạng vé chỗ ngồi cho {{ event.name }}</h1>
        <div class="d-flex justify-content-center">
            <button class="btn" :class="hall.id == hallSelected ? 'btn-primary' : 'btn-light'" v-for="hall in halls"
                :key="hall.id" @click="selectHall(hall.id)">
                {{ hall.name }}
            </button>
        </div>
    </div>
    <KeepAlive>
        <Hall1 class="mb-5 pb-5" v-if="hallSelected == 1" :selected="seatSelectedHall1"
            :seat-ticket-classes="seatTicketClasses" :mode="mode" :bookings="bookings" @selectSeat="selectSeat" />
        <Hall2 class="mb-5 pb-5" v-else-if="hallSelected == 2" :selected="seatSelectedHall2"
            :seat-ticket-classes="seatTicketClasses" :mode="mode" :bookings="bookings" @selectSeat="selectSeat" />
    </KeepAlive>
    <div
        class="box position-sticky border border-1 bg-white box-setting p-3 px-4 z-1 shadow-sm rounded col-12 col-md-6 start-0 end-0 m-auto">
        <div class="row">
            <div class="col-12">
                <ul class="nav nav-tabs mb-2">
                    <li class="nav-item">
                        <button class="nav-link" :class="{ 'active': mode == MODE_TICKET_CLASS_SETTING }"
                            @click="mode = MODE_TICKET_CLASS_SETTING">Chọn hạng vé</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" :class="{ 'active': mode == MODE_PRE_BOOKING }"
                            @click="mode = MODE_PRE_BOOKING">Pre-Booking</button>
                    </li>
                </ul>
            </div>
            <div class="row" v-if="mode == MODE_TICKET_CLASS_SETTING">
                <small class="text-danger" v-if="errors.ticket_class_id">{{ errors.ticket_class_id[0] }}</small>
                <div class="col-10 mx-0">
                    <select class="form-select" v-model="ticketClassId">
                        <option v-for="ticketclass in event.ticket_classes" :key="ticketclass.id"
                            :value="ticketclass.id">
                            {{ ticketclass.name }}
                        </option>
                    </select>
                </div>
                <button class="btn btn-primary col-2" @click="setSeatTicketClass" :disabled="event.is_openning">Đặt hạng
                    vé</button>
            </div>
            <div class="row justify-content-center" v-if="mode == MODE_PRE_BOOKING">
                <div class="mb-2 row mx-0 p-0">
                    <div class="col-10">
                        <div class="d-flex">
                            <span>Ghế đã đặt chỗ bởi khách hàng Online: </span>
                            <div class="box-color-note admin-seat-booked"/>
                        </div>
                        <div class="d-flex">
                            <span>Ghế được đặt trước: </span>
                            <div class="box-color-note admin-seat-booked-special"/>
                        </div>
                    </div>
                    <button type="button" class="btn btn-light col btn-sm" data-bs-toggle="modal"
                        data-bs-target="#modal">
                        Tình trạng vé
                    </button>
                </div>
                <div class="col-10 mx-0">
                    <Multiselect placeholder="Chọn khách hàng" v-model="clientPreBooking" label="name" valueProp="id"
                        openDirection="top" searchable :clear-on-search="true" :options="clientsSpecial">
                        <template #option="data">
                            {{ data.option.name }} {{ data.option.event_name ? `(${data.option.event_name})` : "" }}
                        </template>
                        <template #nooptions>Không có dữ liệu</template>
                    </Multiselect>
                </div>
                <button class="btn btn-primary col-2 btn-sm text-white" @click="preBooking()">Pre-Booking</button>
                <button class="btn btn-danger col-12 btn-sm mt-2" @click="preBooking(true)">
                    Hủy Pre-Booking các ghế đang chọn
                </button>
            </div>
        </div>
    </div>
    <Modal modalId="modal" :modalTitle="'Tình trạng đặt trước vé khán phòng' + hallSelected">
        <template #body>
            <table class="table table-bordered border-primary" style="width: 100%;">
                <thead>
                    <tr>
                        <th scope="col" class="text-center">Ghế</th>
                        <th scope="col" class="text-center">Tên khách hàng</th>
                        <th scope="col" class="text-center">Số điện thoại</th>
                    </tr>
                </thead>
                <tbody>
                    <template v-for="booking in getBookingSorted">
                        <tr v-if="booking?.client?.isSpecial" :key="booking?.id">
                            <td class="text-center">{{ booking.seat }}</td>
                            <td class="text-center">{{ booking.client.name }}</td>
                            <td class="text-center">{{ booking.client.phone_number }}</td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </template>
        <template #footer>
            <button class="btn btn-danger" data-bs-dismiss="modal">Đóng</button>
        </template>
    </Modal>
</template>
<script setup lang="ts">
import Hall1 from "../../../components/seats/Hall1.vue";
import Hall2 from "../../../components/seats/Hall2.vue";
import Modal from '../../../components/Modal.vue';
import { ref, onMounted, toRef, computed, Ref } from "vue";
import { getEventAPI } from "../../../api/admin/events";
import { getBookingAPI, getTicketClassAPI, preBookinngAPI, setTicketClassAPI } from "../../../api/admin/seats";
import { useRoute } from "vue-router";
import { HttpStatusCode } from "axios";
import { useToast } from "vue-toastification";
import { getSpecialClientsAPI } from "../../../api/admin/clients";
import Multiselect from "@vueform/multiselect"
import { PreBookingData } from "../../../types/seats";
import { MODE_PRE_BOOKING, MODE_TICKET_CLASS_SETTING } from "../../../config/const";

const route = useRoute();
const toast = useToast();
const COLOR_SEAT_BOOKED_NON_SPECIAL = "rgb(0,0,0)";
const COLOR_SEAT_BOOKED_SPECIAL = "#FFEDD8";

let event: Ref<any> = ref({});
let hallSelected = ref(1);
let seatSelectedHall1 = ref([]);
let seatSelectedHall2 = ref([]);
let ticketClassId = ref(null);
let seatTicketClasses = ref([]);
let clientsSpecial = ref({});
let clientPreBooking = ref(null);
let bookings = ref<Array<any>>();
let errors: Ref<any> = ref({});
let mode = ref(MODE_TICKET_CLASS_SETTING)
let halls: Array<any> = [
    {
        name: "Khán phòng 1",
        id: "1"
    },
    {
        name: "Khán phòng 2",
        id: "2"
    }
];

onMounted(async () => {
    await getEvent();
    await getSeatTicketClass();
    await getClientsSpecial();
    await getBookings();
})

const selectHall = (hallId: number) => {
    hallSelected.value = hallId;
}

const getBookingSorted = computed(() => {
    return bookings.value?.sort((a, b) => a.seat > b.seat ? 1 : -1);
})

const selectSeat = (seatName: any) => {
    let seatSelected = toRef(seatSelectedHall1);
    if (hallSelected.value == 2) {
        seatSelected = toRef(seatSelectedHall2);
    }
    let currentSeat = seatSelected.value.findIndex(seat => seat == seatName);
    let seatBooked = bookings.value?.find(book => book.hall == hallSelected.value && book.seat == seatName);
    if (currentSeat > -1) {
        seatSelected.value.splice(currentSeat, 1);
    } else if (seatBooked && seatBooked.disable == false || !seatBooked) {
        seatSelected.value.push(seatName);
    } else if (seatBooked.disable) {
        toast.error("Ghế đã được đặt chỗ. Không thể sửa đổi.");
    }
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

const getClientsSpecial = async () => {
    let response = await getSpecialClientsAPI("", 1, false);
    clientsSpecial.value = response.data;
}

const getSeatTicketClass = async () => {
    let response = await getTicketClassAPI(Number(route.params.eventId));
    seatTicketClasses.value = response.data;
}

const setSeatTicketClass = async () => {
    let data = {
        event_id: Number(route.params.eventId),
        ticket_class_id: Number(ticketClassId.value),
        seats: [
            {
                hall: 1,
                names: seatSelectedHall1.value
            },
            {
                hall: 2,
                names: seatSelectedHall2.value
            }
        ]
    };
    let response = await setTicketClassAPI(data);
    errors.value = {};
    switch (response.status) {
        case HttpStatusCode.Ok:
            seatTicketClasses.value = response.data;
            seatSelectedHall1.value = [];
            seatSelectedHall2.value = [];
            break;
        case HttpStatusCode.UnprocessableEntity:
            errors.value = response.data.errors;
            break;
        default:
            toast.error(response.data.message);
            break;
    }
}

const preBooking = async (isCancel = false) => {
    if (!seatSelectedHall2.value.length && !seatSelectedHall1.value.length) {
        toast.error("Không có ghế được chọn pre-booking");
        return;
    }
    if (!isCancel && !clientPreBooking.value) {
        toast.error("Không có khách hàng để pre-booking");
        return;
    }
    let data: PreBookingData = {
        event_id: Number(route.params.eventId),
        client_id: Number(clientPreBooking.value),
        seats: [
            {
                hall: 1,
                names: seatSelectedHall1.value
            },
            {
                hall: 2,
                names: seatSelectedHall2.value
            }
        ],
        isCancel: isCancel
    };
    let response = await preBookinngAPI(data);
    switch (response.status) {
        case HttpStatusCode.Ok:
            seatSelectedHall1.value = [];
            seatSelectedHall2.value = [];
            await getBookings();
            break;
        case HttpStatusCode.UnprocessableEntity:
            errors.value = response.data.errors;
            break;
        default:
            toast.error(response.data.message);
            break;
    }
}

const getBookings = async () => {
    let response = await getBookingAPI(Number(route.params.eventId));
    bookings.value = response.data.map(booking => {
        return {
            seat: booking.seat.name,
            hall: booking.seat.hall,
            color: booking.client?.isSpecial ? COLOR_SEAT_BOOKED_SPECIAL : COLOR_SEAT_BOOKED_NON_SPECIAL,
            client: booking.client,
            disable: booking.client?.isSpecial ? false : true,
            textcolor: booking.client?.isSpecial ? "black" : "white",
        }
    });
}
</script>
<style src="@vueform/multiselect/themes/default.css"></style>
<style lang="scss" scoped>
.box-setting {
    background-color: white;
    bottom: 20px;

    select:focus {
        outline: none;
        box-shadow: none;
    }
}

.multiselect.is-active {
    box-shadow: none !important;
}

.box-color-note {
    width: 20px;
    height: 20px;
}
</style>