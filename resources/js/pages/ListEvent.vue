<template>
    <div class="container-sm">
        <div class="row justify-content-center align-items-center">
            <div class="col-md-10">
                <h3 class="py-5 fs-3">Danh sách sự kiện</h3>
                <div class="mb-5" v-for="event in events.data">
                    <router-link :to="{ name: 'book-ticket', params: { eventId: event.id } }">
                        <div class="row shadow bg-white border-hg-bottom">
                            <div class="col-lg-4 col-md-5">
                                <figure class="image__wrapper" v-lazyload>
                                    <img class="image__item w-100"
                                        :data-url="`/storage/${event.image_url}`"
                                        alt="random image">
                                </figure>
                            </div>
                            <div class="col-lg-8 col-md-7">
                                <h4 class="text-primary fs-4 fs-sm-2">{{ event.name }}</h4>
                                <button class="btn btn-primary fw-bold rounded-pill fs-5 py-0">Đặt vé</button>
                                <p class="desc fs-md-5  text-truncate-custom">
                                    {{ event.description }}
                                </p>
                            </div>
                        </div>
                    </router-link>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
import { ref, onMounted } from "vue";
import { getListEventAPI } from "../api/event";

let events = ref([]);

onMounted(async () => {
    await getListEvent();
});

const getListEvent = async () => {
    let response = await getListEventAPI();
    events.value = response.data;
}
</script>