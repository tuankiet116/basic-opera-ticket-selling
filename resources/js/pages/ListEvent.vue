<template>
    <div class="container-sm" v-if="!isNoEvents">
        <div class="row justify-content-center align-items-center">
            <div class="col-md-10">
                <h3 class="py-5 fs-3"> {{ $t("events.title") }} </h3>
                <div class="mb-5" v-for="event in events.data" :key="event.id">
                    <router-link :to="{ name: 'book-ticket', params: { eventId: event.id } }">
                        <div class="row shadow bg-white border-hg-bottom">
                            <div class="col-lg-4 col-md-5">
                                <figure class="image__wrapper" v-lazyload>
                                    <img class="image__item ratio ratio-16x9 object-fit-fill" style="max-height: 200px"
                                        :data-url="`/storage/${event.image_url}`" alt="random image">
                                </figure>
                            </div>
                            <div class="col-lg-8 col-md-7">
                                <h4 class="text-black fs-4 fs-sm-2">{{ event.name }}</h4>
                                <button class="btn btn-small btn-primary rounded-pill py-0 fw-medium text-white"> {{
                                    $t("events.booking") }} </button>
                                <p class="fs-md-5 fw-medium">
                                    {{ $t("events.publish_date") + ': ' + convertDate(event.date) }}
                                </p>
                                <p class="desc fs-md-5 text-truncate-custom">
                                    {{ event.description }}
                                </p>
                            </div>
                        </div>
                    </router-link>
                </div>
                <div class="row">
                    <div class="d-flex">
                        <div class="ms-auto">
                            <button class="btn btn-light m-1" :class="{ 'active': events.current_page == page }"
                                v-for="page in events.last_page" :key="page" @click="changePage(page)">
                                {{ page }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <ComingSoon v-else/>
</template>
<script setup>
import { ref, onMounted } from "vue";
import { getListEventAPI } from "../api/event";
import { convertDate } from "../helpers/date";
import ComingSoon from "./ComingSoon.vue";

let events = ref({
    next_page_url: null,
    current_page: 1,
    data: [],
    last_page: 1,
});

let isNoEvents = ref(false);

onMounted(async () => {
    await getListEvent();
    if (!events.value.data.length) isNoEvents.value = true;
});

const getListEvent = async (page = 1) => {
    let response = await getListEventAPI(page);
    events.value.data = response.data.data;
    events.value.next_page_url = response.data.next_page_url;
    events.value.current_page = response.data.current_page;
    events.value.last_page = response.data.last_page;
}

const changePage = async (page) => {
    await getListEvent(page);
}
</script>