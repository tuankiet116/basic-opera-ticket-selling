<template>
    <div class="container text-center pt-5">
        <h1 class="fs-3 pt-5">Đặt vé sự kiện {{ "Fake" }}</h1>
        <div class="d-flex justify-content-center">
            <button class="btn" :class="hall.id == hallSelected ? 'btn-primary' : 'btn-light'" v-for="hall in halls"
                :key="hall.id" @click="selectHall(hall.id)">
                {{ hall.name }}
            </button>
        </div>
    </div>
    <Hall1 v-if="hallSelected == 'fl_1-2'" @selectSeat="selectSeat"/>
    <Hall2 v-else-if="hallSelected == 'fl_3-4'" @selectSeat="selectSeat"/>
</template>
<script setup>
import Hall1 from "../../components/seats/Hall2.vue";
import Hall2 from "../../components/seats/Hall1.vue";
import { ref } from "vue";

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
let hallSelected = ref("fl_1-2");
let seatSelected = ref([]);

const selectHall = (hallId) => {
    hallSelected.value = hallId;
}

const selectSeat = (seatName) => {
    let currentSeat = seatSelected.value.findIndex(seat => seat == seatName);
    if (currentSeat > -1) {
        seatSelected.value.splice(currentSeat, 1);
    } else {
        seatSelected.value.push(seatName);
    }
}
</script>