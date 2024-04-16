<template>
    <div class="row container mx-auto mt-5">
        <div class="p-4 border shadow">
            <h4>Thông tin cơ bản</h4>
            <div class="row">
                <div class="col-6">
                    <label for="event-name" class="form-label">Tên sự kiện: </label>
                    <input type="text" class="form-control" id="event-name" v-model="title" @change="changeTitle"
                        :disabled="props.isLoading">
                    <small v-if="props.errors.name" class="text-danger">{{ props.errors.name[0] }}</small>
                </div>
                <div class="col-6">
                    <label for="event-date" class="form-label">Ngày công chiếu: </label>
                    <flat-pickr id="event-date" v-model="date" class="form-control" :config="config"
                        @change="changeDate" />
                    <small v-if="props.errors.date" class="text-danger">{{ props.errors.date[0] }}</small>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-6">
                    <label for="event-description" class="form-label">Mô tả sự kiện</label>
                    <textarea class="form-control" type="textarea" id="event-description" v-model="description"
                        @change="changeDesc" :disabled="props.isLoading" />
                    <small v-if="props.errors.desc" class="text-danger">{{ props.errors.desc[0] }}</small>
                </div>
                <div class="col-6">
                    <label for="event-image" class="form-label">Ảnh sự kiện</label>
                    <input class="form-control" type="file" id="event-image" @change="selectImage" accept="image/*"
                        :disabled="props.isLoading">
                    <small v-if="props.errors.image" class="text-danger">{{ props.errors.image[0] }}</small>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, toRef } from "vue";
import flatPickr from "vue-flatpickr-component";
import 'flatpickr/dist/flatpickr.css';
import { Vietnamese } from 'flatpickr/dist/l10n/vn';

const emits = defineEmits(["changeTitle", "changeDate", "changeDesc", "changeFile"]);
const props = defineProps({
    title: {
        type: String,
        default: null
    },
    date: {
        type: String,
        default: null
    },
    description: {
        type: String,
        default: null
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

const title = ref(props.title);
const date = ref(props.date);
const description = ref(props.description);
const file = ref(null);
const config = ref({
    altInput: true,
    dateFormat: 'Y-m-d',
    locale: Vietnamese, // locale for this instance only          
});

const selectImage = ($event) => {
    const target = $event.target;
    if (target && target.files) {
        file.value = target.files[0];
        emits("changeFile", file);
    }
}
const changeTitle = () => emits("changeTitle", title);
const changeDate = () => emits("changeDate", date);
const changeDesc = () => emits("changeDesc", description);
</script>