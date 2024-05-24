<template>
    <div class="container">
        <h2>Danh sách đặt vé</h2>
        <div class="container-fluid mt-5">
            <ul class="nav nav-tabs justify-content-center">
                <li class="nav-item" @click="changeTab(TAB_EVENTS_OPENNING)">
                    <button class="nav-link" :class="{ active: tab == TAB_EVENTS_OPENNING }">
                        Danh sách sự kiện đang mở bán vé
                    </button>
                </li>
                <li class="nav-item" @click="changeTab(TAB_ALL_EVENTS)">
                    <button class="nav-link" :class="{ active: tab == TAB_ALL_EVENTS }">
                        Tất cả sự kiện
                    </button>
                </li>
            </ul>
        </div>
        <div class="row">
            <div class="col-6" v-if="tab == TAB_EVENTS_OPENNING" v-for="event in eventsOpenning">
                <div class="card mt-3 mx-auto" style="max-width: 600px">
                    <div class="row g-0">
                        <div class="col-md-5">
                            <img :src="'/storage/' + event.image_url" class="img-fluid rounded-start ratio ratio-16x9 object-fit-fill" alt="..." />
                        </div>
                        <div class="col-md-7">
                            <div class="card-body">
                                <RouterLink :to="{ name: 'admin-list-booking-event', params: { eventId: event.id } }">
                                    <h5 class="card-title">{{ event.name }}</h5>
                                </RouterLink>
                                <p class="card-text text-truncate-custom">
                                    {{ event.description }}
                                </p>
                                <p class="card-text">
                                    <small class="text-body-secondary">Ngày diễn: {{ convertDate(event.date) }}</small>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mt-3 p-3 mx-auto" style="max-width: 600px" v-if="!eventsOpenning.length">
                    Không có dữ liệu
                </div>
            </div>
            <div class="col-6" v-if="tab == TAB_ALL_EVENTS" v-for="event in events.data">
                <div class="card mt-3 mx-auto" style="max-width: 600px">
                    <div class="row g-0">
                        <div class="col-md-5">
                            <img :src="'/storage/' + event.image_url" class="img-fluid rounded-start ratio ratio-16x9 object-fit-fill" alt="..." />
                        </div>
                        <div class="col-md-7">
                            <div class="card-body">
                                <RouterLink :to="{ name: 'admin-list-booking-event', params: { eventId: event.id } }">
                                    <h5 class="card-title">{{ event.name }}</h5>
                                </RouterLink>
                                <p class="card-text text-truncate-custom">
                                    {{ event.description }}
                                </p>
                                <p class="card-text">
                                    <small class="text-body-secondary">Ngày diễn: {{ convertDate(event.date) }}</small>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mt-3 p-3 mx-auto" style="max-width: 600px" v-if="!eventsOpenning.length">
                    Không có dữ liệu
                </div>
            </div>
        </div>
        <InfiniteLoading v-if="events.next_page_url" @infinite="infiniteHandler" />
    </div>
</template>

<script setup>
    import InfiniteLoading from "v3-infinite-loading";
    import "v3-infinite-loading/lib/style.css";
    import { ref, onMounted, computed } from "vue";
    import { getListEvent } from "../../../api/admin/events";
    import { HttpStatusCode } from "axios";
    import moment from "moment";

    const TAB_EVENTS_OPENNING = "tab-events-openning";
    const TAB_ALL_EVENTS = "tab-all-events";

    let tab = ref(TAB_EVENTS_OPENNING);
    let events = ref({
        data: [],
        total: 0,
        current_page: 1,
        next_page_url: null,
    });
    let eventsOpenning = computed(() => {
        return events.value.data.filter((event) => event.is_openning);
    });

    const infiniteHandler = async () => {
        if (events.value.next_page_url) {
            events.value.current_page++;
            await getEvents();
        }
    };
    const changeTab = (name) => {
        tab.value = name;
    };

    onMounted(async () => {
        await getEvents();
    });

    const getEvents = async () => {
        let response = await getListEvent(events.value.current_page);
        switch (response.status) {
            case HttpStatusCode.Ok:
                events.value.data.push(...response.data.data);
                events.value.total = response.data.total;
                events.value.current_page = response.data.current_page;
                events.value.next_page_url = response.data.next_page_url;
                break;
            default:
                console.log(response.data);
        }
    };

    const convertDate = (date) => {
        return moment(date).format("DD/MM/yyyy")
    }
</script>
<style scoped>
.text-truncate-custom {
    font-size: 12px
}
</style>
