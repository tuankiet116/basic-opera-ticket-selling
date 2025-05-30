<template>
    <div class="minimap position-fixed p-2"></div>
    <div class="row justify-content-center mx-2 mb-md-5 mb-2">
        <div id="rows" class="container-md overflow-x-auto container-fluid p-0 m-0" ref="rows">
            <div class="zoom wrapper" ref="wrapper">
                <div id="seats-container" ref="seatsContainer"
                    class="row p-0 justify-content-center mt-5 seats-container mx-auto" style="width: 1250px;">
                    <div class="stage col-10 m-auto col-md-6 border border-dark py-5 rounded mb-2">
                        <p class="text-center fs-4 h-100 m-0">{{ $t("seat.stage") }}</p>
                    </div>
                    <div v-for="(row, index) in rows1" :key="index" class="row p-0 justify-content-center flex-nowrap"
                        :style="`height: ${caculateRowHeight(row)}px`">
                        <template v-for="seat in row" :key="seat.id">
                            <div v-if="seat.id" @click="emits('selectSeat', seat.id, Hall)"
                                :class="setClassName(seat)"
                                class="border border-dark seat d-flex justify-content-center align-items-center z-1 rounded"
                                :style="setStyleSeat(seat)" v-bind="makeToolTipData(seat, props.bookings, 1)"
                                data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip"
                                v-tooltip>
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
                            <span>{{ $t("seat.door") }} 3</span>
                        </div>
                        <div class="border border-dark d-flex justify-content-center align-items-center col-1"
                            style="height: 60px;">
                            <span>{{ $t("seat.door") }} 4</span>
                        </div>
                    </div>
                    <div v-for="(row, index) in rows2" class="row justify-content-center flex-nowrap" :key="index"
                        :style="`height: ${caculateRowHeight(row)}px`">
                        <template v-for="seat in row" :key="seat.id">
                            <div v-if="seat.id" @click="emits('selectSeat', seat.id, Hall)"
                                class="border border-dark seat d-flex justify-content-center align-items-center z-1 rounded"
                                :class="setClassName(seat)" :style="setStyleSeat(seat)"
                                v-bind="makeToolTipData(seat, props.bookings, 1)" data-bs-toggle="tooltip"
                                data-bs-placement="top" data-bs-custom-class="custom-tooltip" v-tooltip>
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
                            <span>{{ $t("seat.door") }} 1</span>
                        </div>
                        <div class="border border-dark d-flex justify-content-center align-items-center col-1"
                            style="height: 60px;">
                            <span>{{ $t("seat.door") }} 2</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { renderSeats } from '../../helpers/seats';
import { seats1, seats2 } from "../../config/hall1";
import { onMounted, ref, computed } from 'vue';
import { setStyleSeatByTicketClass, makeToolTipData, setSeatClassName } from '../../composable/hallComposable';
import Minimap from 'js-minimap';

const rows = ref(null);
const wrapper = ref(null);
const seatsContainer = ref(null);
const rows1 = ref(Array());
const rows2 = ref(Array());
const emits = defineEmits(["selectSeat"]);
const minimap = ref(null);
const Hall = 1;

const props = defineProps({
    selected: {
        type: Array,
        default: []
    },
    seatTicketClasses: {
        type: Array,
        default: []
    },
    bookings: {
        type: Array,
        default: []
    },
    mode: {
        default: ""
    }
});

const setStyleSeat = (seat) => {
    return setStyleSeatByTicketClass(seat, props.seatTicketClasses, 1)
}

const setClassName = computed(() => {
    return (seat) => setSeatClassName(seat, props.selected, props.seatTicketClasses, props.bookings, props.mode, 1)
})

onMounted(() => {
    rows1.value = renderSeats(seats1);
    rows2.value = renderSeats(seats2);
    setTimeout(async function () {
        let scrollposition = seatsContainer.value.offsetWidth / 3.2 - wrapper.value.offsetWidth / 2;
        rows.value.scroll(scrollposition, 0);
        loadMinimap();
    }, 2)
});

const loadMinimap = () => {
    const container = document.querySelector('.seats-container') // any container you want to generate a minimap for
    const target = document.querySelector('.minimap') // the container of the minimap
    setTimeout(() => {
        minimap.value = new Minimap({
            container,
            target,
            width: 100,
            observe: false, // default true
            throttle: 30, // default 30
        })
    }, 200);
}

const caculateRowHeight = (row) => {
    let max = Math.max(...row.flatMap(s => {
        if (s.isWall) return [];
        return (s.marginTop + s.height) / 1.2
    }))
    return max;
}

defineExpose({
    minimap
});
</script>
<style>
.minimap-preview {
    background-color: white;
    border: solid 1px black;
}

.minimap {
    z-index: 10;
    top: 70px;
    display: none;

    @media screen and (max-width: 748px) {
        top: 50px !important;
        display: block;
    }

    @media screen and (max-width: 556px) {
        top: 70px !important;
        display: block;
    }
}
</style>
<style scoped lang="scss">
.seat {
    margin-left: 1px;
    margin-right: 1px;
    font-size: 11px;
    cursor: pointer;

    span {
        cursor: pointer;
    }
}

.selected {
    background-color: #8888 !important;
    color: white;
}

.seat>span {
    font-size: 12px;
}

.zoom {
    transform: scale(1);
    -webkit-transform: scale(1);

    @media screen and (max-width: 748px) {
        transform: scale(.5) !important;
        -webkit-transform: scale(.5) !important;
        transform-origin: top center;
        -webkit-transform-origin: top center;
        display: block;
        margin-bottom: -200px;
        margin-left: -125px;
    }
}

#rows {
    @media screen and (max-width: 748px) {
        height: 800px;
        overflow-y: hidden;
    }
}
</style>