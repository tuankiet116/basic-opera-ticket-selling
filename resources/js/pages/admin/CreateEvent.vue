<template>
    <div class="container-fluid">
        <EventInfo :title="dataBasic.title" :date="dataBasic.date" :description="dataBasic.desc" :errors="errors"
            @changeDate="setDate" @changeTitle="setTitle" @changeFile="setFile" @changeDesc="setDesc"
            :isLoading="isLoading" />
        <TicketClass :ticketClasses="dataTicketClasses" :errors="errors" :isLoading="isLoading" />
        <div class="row justify-content-center mt-5">
            <button class="btn btn-primary col-2 text-white" :disabled="isLoading" @click="createEvent">
                Tạo sự kiện
                <span v-if="isLoading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            </button>
        </div>
    </div>
</template>

<script setup lang="ts">
import EventInfo from "../../components/admin/EventInfo.vue";
import TicketClass from "../../components/admin/TicketClass.vue";
import { reactive, ref, onMounted } from "vue";
import { EventData } from "../../types/event";
import { createEventAPI, getEventAPI } from "../../api/admin/events";
import moment from "moment";
import { HttpStatusCode } from "axios";
import { useToast } from "vue-toastification";
import { useRoute, useRouter } from "vue-router";

let errors = ref({});
let isLoading = ref(false);
const dataBasic = reactive({
    id: null,
    title: "",
    desc: "",
    file: File,
    date: ""
});

const dataTicketClasses = ref([
    {
        id: null,
        name: "",
        price: 0,
        color: ""
    }
]);

const props = defineProps({
    isEdit: {
        type: Boolean,
        default: false
    }
})

const setDate = (date: string) => { dataBasic.date = date; }
const setTitle = (title: string) => { dataBasic.title = title }
const setFile = (file: typeof File) => { dataBasic.file = file }
const setDesc = (desc: string) => { dataBasic.desc = desc }
const toast = useToast();
const router = useRouter();
const route = useRoute();

onMounted(async () => {
    if (props.isEdit == true) {
        await getEvent();
    }
})

const getEvent = async () => {
    let response = await getEventAPI(Number(route.params.eventId));
    dataBasic.id = response.data.id;
    dataBasic.title = response.data.name;
    dataBasic.desc = response.data.description;
    dataBasic.date = response.data.date;
    dataTicketClasses.value = response.data.ticket_classes;
}

const updateEvent = async () => {
    
}

const createEvent = async () => {
    let data: EventData = {
        name: dataBasic.title,
        date: moment(dataBasic.date).format("Y-MM-DD"),
        description: dataBasic.desc,
        image: dataBasic.file,
        ticketClasses: dataTicketClasses.value
    }
    isLoading.value = true;
    let response = await createEventAPI(data);
    switch (response.status) {
        case HttpStatusCode.Ok:
            toast.success("Tạo sự kiện thành công");
            router.push({ name: "admin-list-events" });
            break;
        case HttpStatusCode.UnprocessableEntity:
            errors.value = response.data.errors;
            break;
        default:
            toast.error("Tạo sự kiện thất bại");
            break;
    }
    isLoading.value = false;
}
</script>