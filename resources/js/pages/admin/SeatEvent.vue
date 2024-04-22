<template>
    <div class="container text-center pt-5">
        <h1 class="fs-3 pt-5">Cài đặt hạng vé chỗ ngồi cho {{ event.name }}</h1>
        <div class="d-flex justify-content-center">
            <button class="btn" :class="hall.id == hallSelected ? 'btn-primary' : 'btn-light'" v-for="hall in halls"
                :key="hall.id" @click="selectHall(hall.id)">
                {{ hall.name }}
            </button>
        </div>
    </div>
    <Hall1 class="mb-5 pb-5" v-if="hallSelected == 'fl_1-2'" :selected="seatSelectedHall1"
        :seat-ticket-classes="seatTicketClasses" @selectSeat="selectSeat" />
    <Hall2 class="mb-5 pb-5" v-else-if="hallSelected == 'fl_3-4'" :selected="seatSelectedHall2"
        :seat-ticket-classes="seatTicketClasses" @selectSeat="selectSeat" />
    <div class="box position-fixed border border-1 bg-white box-setting p-3 px-4 z-1 shadow-sm rounded"
        style="width: 40%;">
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
                <button class="btn btn-primary col-2" @click="setSeatTicketClass">Đặt hạng vé</button>
            </div>
            <div class="row" v-if="mode == MODE_PRE_BOOKING">
                <small class="text-danger" v-if="errors.ticket_class_id">{{ errors.ticket_class_id[0] }}</small>
                <div class="col-10 mx-0">
                    <VueSelectInfinityLoad @fetchData="getClientsSpecial" :list="clientsSpecial"></VueSelectInfinityLoad>
                </div>
                <button class="btn btn-primary col-2" @click="setSeatTicketClass">Pre-Booking</button>
            </div>
        </div>
    </div>
</template>
<script setup lang="ts">
import Hall1 from "../../components/seats/Hall1.vue";
import Hall2 from "../../components/seats/Hall2.vue";
import VueSelectInfinityLoad from "../../components/VueSelectInfinityLoad.vue";
import { ref, onMounted, toRef } from "vue";
import { getEventAPI } from "../../api/admin/events";
import { getTicketClassAPI, setTicketClassAPI } from "../../api/admin/seats";
import { useRoute } from "vue-router";
import { HttpStatusCode } from "axios";
import { useToast } from "vue-toastification";
import { getSpecialClientsAPI } from "../../api/admin/clients";

const route = useRoute();
const toast = useToast();
const MODE_TICKET_CLASS_SETTING = 'ticket-class-setting';
const MODE_PRE_BOOKING = 'pre-booking';
let event = ref({});
let hallSelected = ref("fl_1-2");
let seatSelectedHall1 = ref([]);
let seatSelectedHall2 = ref([]);
let ticketClassId = ref(null);
let seatTicketClasses = ref([]);
let clientsSpecial = ref([]);
let errors = ref({});
let mode = ref(MODE_TICKET_CLASS_SETTING)
let halls = [
    {
        name: "Khán phòng 1",
        id: "fl_1-2"
    },
    {
        name: "Khán phòng 2",
        id: "fl_3-4"
    }
];

onMounted(async () => {
    await getEvent();
    await getSeatTicketClass();
})

const selectHall = (hallId: string) => {
    hallSelected.value = hallId;
}

const selectSeat = (seatName: never) => {
    let seatSelected = toRef(seatSelectedHall1);
    if (hallSelected.value == "fl_3-4") {
        seatSelected = toRef(seatSelectedHall2);
    }
    let currentSeat = seatSelected.value.findIndex(seat => seat == seatName);
    if (currentSeat > -1) {
        seatSelected.value.splice(currentSeat, 1);
    } else {
        seatSelected.value.push(seatName);
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

const getClientsSpecial = async (search: string) => {
    let response = await getSpecialClientsAPI(search);
    clientsSpecial.value = response.data;
}

const getSeatTicketClass = async () => {
    let response = await getTicketClassAPI(route.params.eventId);
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
</script>
<style lang="scss">
.box-setting {
    background-color: white;
    bottom: 20px;
    left: 40%;

    select:focus {
        outline: none;
        box-shadow: none;
    }
}
</style>