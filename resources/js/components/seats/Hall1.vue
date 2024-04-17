<template>
    <div class="mt-5 row justify-content-center mx-2">
        <div class="stage col-10 col-md-6 border border-dark py-5">
            <p class="text-center fs-4 h-100 m-0">Khu vực sân khấu</p>
        </div>
        <div id="rows" class="container-md overflow-x-auto container-fluid p-0 m-0" ref="rows">
            <div id="seats-container" ref="seatsContainer"
                class="row p-0 justify-content-center mt-5 seats-container mx-auto" style="width: 1250px;">
                <div v-for="(row, index) in rows1" :key="index" class="row p-0 justify-content-center flex-nowrap"
                    :style="`height: ${caculateRowHeight(row)}px`">
                    <template v-for="seat in row" :key="seat.id">
                        <div v-if="seat.id" @click="emits('selectSeat', seat.id)"
                            class="border border-dark seat d-flex justify-content-center align-items-center"
                            :style="`margin-top: ${seat.marginTop}px !important; width: ${seat.width}px; height: ${seat.height}px;`">
                            <span>{{ seat.name }}</span>
                        </div>
                        <div v-else class="seat"
                            :style="`margin-top: ${seat.marginTop}px !important; width: ${seat.width}px; height: ${seat.height}px;`">
                        </div>
                    </template>
                </div>
                <div class="row p-0 justify-content-between flex-nowrap my-2" style="width: 100%;">
                    <div class="border border-dark d-flex justify-content-center align-items-center col-1"
                        style="height: 60px;">
                        <span>Cửa số 3</span>
                    </div>
                    <div class="border border-dark d-flex justify-content-center align-items-center col-1"
                        style="height: 60px;">
                        <span>Cửa số 4</span>
                    </div>
                </div>
                <div v-for="(row, index) in rows2" class="row justify-content-center flex-nowrap" :key="index"
                    :style="`height: ${caculateRowHeight(row)}px`">
                    <template v-for="seat in row" :key="seat.id">
                        <div v-if="seat.id" @click="emits('selectSeat', seat.id)"
                            class="border border-dark seat d-flex justify-content-center align-items-center"
                            :style="`margin-top: ${seat.marginTop}px !important; width: ${seat.width}px; height: ${seat.height}px;`">
                            <span>{{ seat.name }}</span>
                        </div>
                        <div v-else-if="seat.isWall" class="wall mx-1"
                            :style="`margin-top: ${seat.marginTop - 10}px !important; width: ${seat.width}px; height: ${seat.height}px;`">
                        </div>
                        <div v-else class="seat"
                            :style="`margin-top: ${seat.marginTop}px !important; width: ${seat.width}px; height: ${seat.height}px;`">
                        </div>
                    </template>
                </div>
                <div class="row p-0 justify-content-around flex-nowrap my-2" style="width: 100%;">
                    <div class="border border-dark d-flex justify-content-center align-items-center col-1"
                        style="height: 60px;">
                        <span>Cửa số 1</span>
                    </div>
                    <div class="border border-dark d-flex justify-content-center align-items-center col-1"
                        style="height: 60px;">
                        <span>Cửa số 2</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { renderSeats } from '../../helpers/seats';
import { seats1, seats2 } from "../../config/hall1";
import { onMounted, ref } from 'vue';

const rows = ref(null);
const seatsContainer = ref(null);
const rows1 = ref(Array());
const rows2 = ref(Array());
const emits = defineEmits("selectSeat");

onMounted(() => {
    rows1.value = renderSeats(seats1);
    rows2.value = renderSeats(seats2);
    window.addEventListener('load', function () {
        setTimeout(function () {
            let scrollposition = seatsContainer.value.offsetWidth / 2 - rows.value.offsetWidth / 2;
            rows.value.scroll(scrollposition, 0);
        }, 1)
    });
});

const caculateRowHeight = (row) => {
    let max = Math.max(...row.flatMap(s => {
        if (s.isWall) return [];
        return (s.marginTop + s.height) / 1.2
    }))
    return max;
}
</script>

<style lang="scss">
.seat {
    margin-left: 1px;
    margin-right: 1px;
    font-size: 11px;
    cursor: pointer;

    span {
        cursor: pointer;
    }
}
</style>