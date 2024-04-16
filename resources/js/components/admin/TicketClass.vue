<template>
    <div class="row container mx-auto mt-5">
        <div class="p-4 border shadow">
            <h4>Phân hạng vé</h4>
            <div class="row" v-for="(data, index) in props.ticketClasses" :key="index">
                <div class="col-5">
                    <label for="name" class="form-label">Tên hạng vé: </label>
                    <input type="text" class="form-control" id="name" v-model="data.name" :disabled="props.isLoading">
                    <small v-if="props.errors[`ticketClasses.${index}.name`]" class="text-danger">
                        {{ props.errors[`ticketClasses.${index}.name`][0] }}
                    </small>
                </div>
                <div class="col-5">
                    <label for="price" class="form-label">Giá vé: </label>
                    <div class="input-group">
                        <input v-model="data.priceFormatted" type="text" class="form-control" id="price"
                            @keydown="checkNumber" :disabled="props.isLoading">
                        <span class="input-group-text">vnd</span>
                    </div>
                    <small v-if="props.errors[`ticketClasses.${index}.price`]" class="text-danger">
                        {{ props.errors[`ticketClasses.${index}.price`][0] }}
                    </small>
                </div>
                <div class="col-2">
                    <label for="select-color" class="form-label">Chọn màu sắc: </label>
                    <input type="color" class="form-control form-control-color" id="select-color" v-model="data.color"
                        :disabled="props.isLoading">
                    <small v-if="props.errors[`ticketClasses.${index}.color`]" class="text-danger">
                        {{ props.errors[`ticketClasses.${index}.color`][0] }}
                    </small>
                </div>
            </div>
            <div class="row mt-2 justify-content-center">
                <button class="btn btn-light border col-12" @click="addTicketClass">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                        class="bi bi-plus-circle" viewBox="0 0 16 16">
                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                        <path
                            d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4" />
                    </svg>
                    Thêm mới
                </button>
            </div>
        </div>
    </div>
</template>
<script setup>
import { ref, computed, onMounted } from "vue";
import { numberWithCommas } from "../../helpers/number";

const props = defineProps({
    ticketClasses: {
        type: Array,
        default: [
            {
                id: {
                    type: String | Number | null,
                    default: null
                },
                name: {
                    type: String,
                },
                price: {
                    type: Number,
                    default: 0
                },
                color: {
                    type: String,
                    default: "#000000"
                }
            }
        ]
    },
    errors: {
        type: Object,
        default: {}
    },
    isLoading: {
        type: Boolean,
        default: false
    }
});

const checkNumber = (e) => {
    if (e.key.match(/\D+/g) && e.key != "Backspace" && e.key != "ArrowLeft" && e.key != "ArrowRight") {
        e.preventDefault();
    }
}

const computedNumberFormatted = (data) => computed({
    get: () => {
        return numberWithCommas(data.price);
    },
    set: (value) => {
        value = value.replaceAll(/[^0-9]+/g, "");
        data.price = Number(value)
    }
});

const addTicketClass = () => {
    props.ticketClasses.push({
        id: null,
        name: "",
        price: 0,
        color: "#000000"
    })

    props.ticketClasses[props.ticketClasses.length - 1].priceFormatted = computedNumberFormatted(props.ticketClasses[props.ticketClasses.length - 1]);
}

onMounted(() => {
    props.ticketClasses.forEach(data => {
        data.priceFormatted = computedNumberFormatted(data);
    })
});

</script>
