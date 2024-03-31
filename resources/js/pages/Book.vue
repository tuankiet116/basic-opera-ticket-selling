<script setup lang="ts">
import { useRoute } from 'vue-router';
import Hall1 from '../components/Seats/Hall1.vue';
import Hall2 from '../components/Seats/Hall2.vue';
import { ref } from "vue";

const route = useRoute();
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
let hallSelected = ref(halls[1].id);
let eventId = route.params.eventId;

const selectHall = (hallId: string): void => {
    hallSelected.value = hallId;
}
</script>
<template>
    <div class="container text-center pt-5">
        <h1 class="fs-3 pt-5">Đặt vé sự kiện {{ "Fake" }}</h1>
        <div class="d-flex justify-content-center">
            <button class="btn" :class="hall.id == hallSelected ? 'btn-primary' : 'btn-light'" v-for="hall in halls"
                @click="selectHall(hall.id)">
                {{ hall.name }}
            </button>
        </div>
    </div>
    <Hall1 v-if="hallSelected == 'fl_1-2'"/>
    <Hall2 v-else-if="hallSelected == 'fl_3-4'"/>
</template>