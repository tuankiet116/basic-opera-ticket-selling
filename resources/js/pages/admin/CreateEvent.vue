<template>
    <div class="container-fluid">
        <EventInfo :data="dataBasic" :errors="errors" @changeDate="setDate" @changeTitle="setTitle" @changeFile="setFile"
            @changeDesc="setDesc" />
        <TicketClass :ticketClasses="dataTicketClasses" :errors="errors"/>
        <div class="row justify-content-center mt-5">
            <button class="btn btn-primary col-2 text-white" @click="createEvent">Tạo sự kiện</button>
        </div>
    </div>
</template>

<script setup lang="ts">
import EventInfo from "../../components/admin/EventInfo.vue";
import TicketClass from "../../components/admin/TicketClass.vue";
import { reactive, ref } from "vue";
import { EventData } from "../../types/event";
import { createEventAPI } from "../../api/admin/events";
import moment from "moment";
import { HttpStatusCode } from "axios";

const dataBasic = reactive({
    title: "",
    desc: "",
    file: File,
    date: ""
});

let errors = ref({});
const dataTicketClasses = reactive([
    {
        id: null,
        name: "",
        price: 0,
        color: ""
    }
]);


const setDate = (date: string) => { dataBasic.date = date; }
const setTitle = (title: string) => { dataBasic.title = title }
const setFile = (file: typeof File) => { dataBasic.file = file }
const setDesc = (desc: string) => { dataBasic.desc = desc }

const createEvent = async () => {
    let data: EventData = {
        name: dataBasic.title,
        date: moment(dataBasic.date).format("Y-MM-DD"),
        description: dataBasic.desc,
        image: dataBasic.file,
        ticketClasses: dataTicketClasses
    }

    let response = await createEventAPI(data);
    switch (response.status) {
        case HttpStatusCode.Ok: 
            break;
        case HttpStatusCode.UnprocessableEntity:
            errors.value = response.data.errors;
            break;
    }
}
</script>