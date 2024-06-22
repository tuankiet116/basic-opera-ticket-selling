<template>
    <div class="minimap position-fixed p-2"></div>
    <div class="mt-5 row justify-content-center w-100 m-0">
        <div id="rows" class="container-md overflow-x-auto container-fluid" ref="rows">
            <div class="zoom wrapper" ref="wrapper">
                <div id="seats-container" ref="seatsContainer"
                    class="row p-0 justify-content-center mt-5 seats-container mx-auto" style="width: 1250px;">
                    <div v-for="(row, index) in rows1" class="row mt-2 justify-content-center flex-nowrap"
                        :style="`height: ${caculateRowHeight(row)}px`" :key="index">
                        <template v-for="seat in row" :key="seat.id">
                            <div v-if="seat.id" @click="emits('selectSeat', seat.id, Hall)"
                                :class="setClassName(seat)"
                                class="border border-dark seat d-flex justify-content-center align-items-center z-1 rounded"
                                :style="setStyleSeat(seat)" v-bind="makeToolTipData(seat, props.bookings, 2)"
                                data-bs-toggle="tooltip" data-bs-placement="top" v-tooltip>
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
                            style="height: 60px; width: 100px;">
                            <span>{{ $t("seat.door") }} 7</span>
                        </div>
                        <div v-for="(row, index) in rows2" class="row mt-2 justify-content-center flex-nowrap"
                            :style="`height: ${caculateRowHeight(row)}px`" :key="index">
                            <template v-for="seat in row" :key="seat.id">
                                <div v-if="seat.id" @click="emits('selectSeat', seat.id, Hall)"
                                    :class="setClassName(seat)"
                                    class="border border-dark seat d-flex justify-content-center align-items-center z-1 rounded"
                                    :style="setStyleSeat(seat)" v-bind="makeToolTipData(seat, props.bookings, 2)"
                                    data-bs-toggle="tooltip" data-bs-placement="top" v-tooltip>
                                    <span>{{ seat.name }}</span>
                                </div>
                                <div v-else-if="seat.isWall" class="seat mx-1 wall"
                                    :style="`margin-top: ${seat.marginTop - 10}px !important; width: ${seat.width}px; height: ${seat.height}px;`">
                                </div>
                                <div v-else class="seat"
                                    :style="`margin-top: ${seat.marginTop}px !important; width: ${seat.width}px; height: ${seat.height}px;`" />
                            </template>
                        </div>
                        <div class="border border-dark d-flex justify-content-center align-items-center"
                            style="height: 60px; width: 100px;">
                            <span>{{ $t("seat.door") }} 8</span>
                        </div>
                    </div>
                    <div v-for="(row, index) in rows3" class="row mt-2 justify-content-center flex-nowrap"
                        :style="`height: ${caculateRowHeight(row)}px`" :key="index">
                        <template v-for="seat in row" :key="seat.id">
                            <div v-if="seat.id" @click="emits('selectSeat', seat.id, Hall)"
                                :class="setClassName(seat)"
                                class="border border-dark seat d-flex justify-content-center align-items-center z-1 rounded"
                                :style="setStyleSeat(seat)" v-bind="makeToolTipData(seat, props.bookings, 2)"
                                data-bs-toggle="tooltip" data-bs-placement="top" v-tooltip>
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
                            style="height: 60px; width: 100px;">
                            <span>{{ $t("seat.door") }} 5</span>
                        </div>
                        <div class="border border-dark d-flex justify-content-center align-items-center col-1"
                            style="height: 60px; width: 100px;">
                            <span>{{ $t("seat.door") }} 6</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { renderSeats } from '../../helpers/seats';
import { seats1, seats2, seats3 } from "../../config/hall2.ts";
import { onMounted, ref, computed } from 'vue';
import { setStyleSeatByTicketClass, makeToolTipData, setSeatClassName } from '../../composable/hallComposable.ts';

const rows = ref(null);
const seatsContainer = ref(null);
const wrapper = ref(null);
const rows1 = ref(Array());
const rows2 = ref(Array());
const rows3 = ref(Array());
const emits = defineEmits(["selectSeat"]);
const Hall = 2;
import Minimap from 'js-minimap';

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

onMounted(() => {
    rows1.value = renderSeats(seats1);
    rows2.value = renderSeats(seats2);
    rows3.value = renderSeats(seats3);

    setTimeout(function () {
        let scrollposition = seatsContainer.value.offsetWidth / 3.2 - wrapper.value.offsetWidth / 2;
        rows.value.scroll(scrollposition, 0);
        const container = document.querySelector('.seats-container') // any container you want to generate a minimap for
        const target = document.querySelector('.minimap') // the container of the minimap
        const minimap = new Minimap({
            container,
            target,
            width: 100,
            observe: false, // default true
            throttle: 300, // default 30
        })
    }, 1)
})

const caculateRowHeight = (row) => {
    let max = Math.max(...row.flatMap(s => {
        if (s.isWall) return [];
        return s.marginTop + s.height
    }))
    return max;
}

const setStyleSeat = (seat) => {
    return setStyleSeatByTicketClass(seat, props.seatTicketClasses, 2)
}

const setClassName = computed(() => {
    return (seat) => setSeatClassName(seat, props.selected, props.seatTicketClasses, props.bookings, props.mode, 2)
})
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
    cursor: pointer;

    span {
        cursor: pointer;
    }
}

.selected {
    background-color: #8888 !important;
    color: white;
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
        margin-left: -120px;
    }
}

.seat>span {
    font-size: 12px;
}

#rows {
    @media screen and (max-width: 748px) {
        height: 620px !important;
        overflow-y: hidden;
    }
}
</style>