<template>
    <div class="mt-5 row justify-content-center w-100 m-0">
        <div id="rows" class="container-md overflow-x-auto container-fluid h-100" ref="rows">
            <div id="seats-container" ref="seatsContainer"
                class="row p-0 justify-content-center mt-5 seats-container mx-auto" style="width: 1250px;">
                <div v-for="(row, index) in rows1" class="row mt-2 justify-content-center flex-nowrap"
                    :style="`height: ${caculateRowHeight(row)}px`" :key="index">
                    <template v-for="seat in row" :key="seat.id">
                        <div v-if="seat.id" @click="emits('selectSeat', seat.id)"
                            :class="{ 'selected': isSeatInSelected(seat.id) }"
                            class="border border-dark seat d-flex justify-content-center align-items-center z-1"
                            :style="`margin-top: ${seat.marginTop}px !important; width: ${seat.width}px; height: ${seat.height}px;`">
                            <span>{{ seat.name }}</span>
                        </div>
                        <div v-else-if="seat.isWall" class="seat mx-1 wall"
                            :style="`margin-top: ${seat.marginTop - 10}px !important; width: ${seat.width}px; height: ${seat.height}px;`">
                        </div>
                        <div v-else class="seat"
                            :style="`margin-top: ${seat.marginTop}px !important; width: ${seat.width}px; height: ${seat.height}px;`" />
                    </template>
                </div>
                <div class="row p-0 justify-content-center flex-nowrap my-2" style="width: 1050px;">
                    <div class="border border-dark d-flex justify-content-center align-items-center col-1"
                        style="height: 60px;">
                        <span>Cửa số 7</span>
                    </div>
                    <div v-for="(row, index) in rows2" class="row mt-2 justify-content-center flex-nowrap"
                        :style="`height: ${caculateRowHeight(row)}px`" :key="index">
                        <template v-for="seat in row" :key="seat.id">
                            <div v-if="seat.id" @click="emits('selectSeat', seat.id)"
                                :class="{ 'selected': isSeatInSelected(seat.id) }"
                                class="border border-dark seat d-flex justify-content-center align-items-center z-1"
                                :style="`margin-top: ${seat.marginTop}px !important; width: ${seat.width}px; height: ${seat.height}px;`">
                                <span>{{ seat.name }}</span>
                            </div>
                            <div v-else-if="seat.isWall" class="seat mx-1 wall"
                                :style="`margin-top: ${seat.marginTop - 10}px !important; width: ${seat.width}px; height: ${seat.height}px;`">
                            </div>
                            <div v-else class="seat"
                                :style="`margin-top: ${seat.marginTop}px !important; width: ${seat.width}px; height: ${seat.height}px;`" />
                        </template>
                    </div>
                    <div class="border border-dark d-flex justify-content-center align-items-center col-1"
                        style="height: 60px;">
                        <span>Cửa số 8</span>
                    </div>
                </div>
                <div v-for="(row, index) in rows3" class="row mt-2 justify-content-center flex-nowrap"
                    :style="`height: ${caculateRowHeight(row)}px`" :key="index">
                    <template v-for="seat in row" :key="seat.id">
                        <div v-if="seat.id" @click="emits('selectSeat', seat.id)"
                            :class="{ 'selected': isSeatInSelected(seat.id) }"
                            class="border border-dark seat d-flex justify-content-center align-items-center z-1"
                            :style="`margin-top: ${seat.marginTop}px !important; width: ${seat.width}px; height: ${seat.height}px;`">
                            <span>{{ seat.name }}</span>
                        </div>
                        <div v-else-if="seat.isWall" class="seat mx-1 wall"
                            :style="`margin-top: ${seat.marginTop - 10}px !important; width: ${seat.width}px; height: ${seat.height}px;`">
                        </div>
                        <div v-else class="seat"
                            :style="`margin-top: ${seat.marginTop}px !important; width: ${seat.width}px; height: ${seat.height}px;`" />
                    </template>
                </div>
                <div class="row p-0 justify-content-around flex-nowrap my-2" style="width: 1050px;">
                    <div class="border border-dark d-flex justify-content-center align-items-center col-1"
                        style="height: 60px;">
                        <span>Cửa số 5</span>
                    </div>
                    <div class="border border-dark d-flex justify-content-center align-items-center col-1"
                        style="height: 60px;">
                        <span>Cửa số 6</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { renderSeats } from '../../helpers/seats';
import { seats1, seats2, seats3 } from "../../config/hall2.ts";
import { onMounted, ref } from 'vue';

const rows = ref(null);
const seatsContainer = ref(null);
const rows1 = ref(Array());
const rows2 = ref(Array());
const rows3 = ref(Array());
const emits = defineEmits(["selectSeat"]);

const props = defineProps({
    selected: {
        type: Array,
        default: []
    }
});

onMounted(() => {
    rows1.value = renderSeats(seats1);
    rows2.value = renderSeats(seats2);
    rows3.value = renderSeats(seats3);

    window.addEventListener('load', function () {
        setTimeout(function () {
            let scrollposition = seatsContainer.value.offsetWidth / 2 - rows.value.offsetWidth / 2;
            rows.value.scroll(scrollposition, 0);
        }, 1)
    });
})

const isSeatInSelected = (seatId) => {
    return props.selected.find(v => v == seatId);
}

const caculateRowHeight = (row) => {
    let max = Math.max(...row.flatMap(s => {
        if (s.isWall) return [];
        return s.marginTop + s.height
    }))
    return max;
}
</script>

<style lang="scss">
.seat {
    margin-left: 1px;
    margin-right: 1px;
    cursor: pointer;

    span {
        cursor: pointer;
    }
}

.selected {
    background-color: #8888;
    color: white;
}
</style>