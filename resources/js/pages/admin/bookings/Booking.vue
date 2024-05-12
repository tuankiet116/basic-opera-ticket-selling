<template>
    <div class="container mt-3">
        <h3>Danh sách đặt vé sự kiện {{ event.name }}</h3>
        <div class="container">
            <div class="container p-5">
                <div class="row g-3 align-items-center justify-content-center">
                    <div class="col-auto">
                        <label for="searchEvent" class="col-form-label">Tìm kiếm theo số điện thoại</label>
                    </div>
                    <div class="col-auto">
                        <input type="search" id="searchEvent" class="form-control" v-model="searchString">
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-primary text-white" @click="getBookings()">
                            <span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                    class="bi bi-search" viewBox="0 0 16 16">
                                    <path
                                        d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0" />
                                </svg>
                                Tìm kiếm
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-fluid mt-5">
            <ul class="nav nav-tabs justify-content-center">
                <li class="nav-item" @click="changeTab(TAB_ALL)">
                    <button class="nav-link" :class="{ active: tab == TAB_ALL }">
                        Tất cả
                    </button>
                </li>
                <li class="nav-item" @click="changeTab(TAB_PAID)">
                    <button class="nav-link" :class="{ active: tab == TAB_PAID }">
                        Đã thanh toán
                    </button>
                </li>
                <li class="nav-item" @click="changeTab(TAB_UNPAID)">
                    <button class="nav-link" :class="{ active: tab == TAB_UNPAID }">
                        Chưa thanh toán
                    </button>
                </li>
            </ul>
        </div>
        <div class="mt-5">
            <div class="card w-75 mb-2 mx-auto" v-for="booking in bookingsGroupedByTab">
                <div class="card-body position-relative">
                    <span class="badge rounded-pill text-bg-success" v-if="booking.bookings[0].isBooked">
                        Đã thanh toán
                    </span>
                    <span class="badge rounded-pill text-bg-danger" v-else>
                        Chưa thanh toán
                    </span>
                    <span class="badge rounded-pill text-bg-info" v-if="booking.is_receive_in_opera">
                        Nhận vé tại nhà hát
                    </span>
                    <p class="mb-0 fs-5">Khách hàng: <strong>{{ booking.name }}</strong></p>
                    <p class="mb-0 fs-5">Email: <strong>{{ booking.email }}</strong></p>
                    <p class="mb-0 fs-5">Số điện thoại: <strong>{{ booking.phone_number }}</strong></p>
                    <p class="mb-0 fs-5">Tổng tiền thanh toán: <strong>{{ numberWithCommas(booking.price) }}
                            vnd</strong></p>
                    <button class="btn btn-primary text-white position-absolute" data-bs-toggle="modal"
                        data-bs-target="#modal" style="bottom: 10%; right: 2%;" @click="bookingSelected = booking">
                        Xem chi tiết
                    </button>
                </div>
            </div>
            <div class="card w-75 mb-2 mx-auto" v-if="!bookingsGroupedByTab.length">
                <div class="card-body">
                    <p class="mb-0 fs-5">Không có dữ liệu</p>
                </div>
            </div>
        </div>
        <InfiniteLoading v-if="bookingClients.next_page_url" @infinite="loadMore" distance="500"/>
    </div>

    <Modal v-if="bookingSelected" modalId="modal" :modalTitle="'Thông tin đặt vé'">
        <template #body>
            <div>
                <p class="mb-0">Khách hàng: <strong>{{ bookingSelected?.client?.name }}</strong></p>
                <p class="mb-0">Email: <strong>{{ bookingSelected?.client?.email }}</strong></p>
                <p class="mb-0">Số điện thoại: <strong>{{ bookingSelected?.client?.phone_number }}</strong></p>
                <p class="mb-0">Tổng tiền thanh toán: <strong>{{ numberWithCommas(bookingSelected.price ?? 0) }}
                        vnd</strong></p>
            </div>
            <br />
            <h5>Bảng thông tin chi tiết:</h5>
            <table class="table table-bordered border-primary" style="width: 600px">
                <thead>
                    <tr>
                        <th width="15%" scope="col" class="text-center">Khán phòng</th>
                        <th width="20%" scope="col" class="text-center">Ghế</th>
                        <th width="20%" scope="col" class="text-center">Hạng vé</th>
                        <th width="45%" scope="col" class="text-center">Đơn giá</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="booking in bookingSelected.bookings">
                        <td class="text-center">{{ booking.seat.hall }}</td>
                        <td class="text-center">{{ booking.seat.name }}</td>
                        <td class="text-center">{{ booking.ticket_class.name }}</td>
                        <td class="text-center">{{ numberWithCommas(booking.ticket_class.price) }} vnd</td>
                    </tr>
                </tbody>
            </table>
        </template>
        <template #footer>
            <button class="btn btn-info" @click="bookingSelected = {}" data-bs-dismiss="modal">Đóng</button>
            <button class="btn btn-danger" @click="confirmBooked" data-bs-dismiss="modal">Xác nhận thanh toán thành
                công</button>
        </template>
    </Modal>
</template>
<script setup>
    import InfiniteLoading from "v3-infinite-loading";
    import "v3-infinite-loading/lib/style.css";
    import { ref, onMounted, computed } from "vue";
    import { useRoute } from "vue-router";
    import { getEventAPI } from "../../../api/admin/events";
    import { HttpStatusCode } from "axios";
    import { acceptBookingAPI, getListBookingsAPI } from "../../../api/admin/booking";
    import { numberWithCommas } from "../../../helpers/number";
    import { useToast } from "vue-toastification";
    import Modal from '../../../components/Modal.vue';

    const TAB_ALL = 'all';
    const TAB_PAID = 'paid';
    const TAB_UNPAID = 'unpaid';
    const route = useRoute();
    const toast = useToast();
    let bookingClients = ref({
        data: [],
        total: 0,
        current_page: 1,
        next_page_url: null,
    });
    let event = ref({});
    let searchString = ref("");
    let bookingSelected = ref({});
    let tab = ref(TAB_ALL);

    let bookingsGroupedByTab = computed(() => {
        return bookingClients.value.data.flatMap(bookingClient => {
            switch (tab.value) {
                case TAB_ALL:
                    return bookingClient;
                case TAB_PAID:
                    if (bookingClient.bookings[0].isBooked) return bookingClient;
                    return [];
                case TAB_UNPAID:
                    if (!bookingClient.bookings[0].isBooked) return bookingClient;
                    return [];
            }
        })
    });

    onMounted(async () => {
        await getEvent();
        await getBookings();
        window.Echo.private(`admin.client-booking-event-${event.value.id}`).listen(
            "AdminClientBookingTicket",
            (e) => {
                e.bookings.price = e.bookings.bookings.reduce((previous, current) => {
                    return previous + current.ticket_class.price;
                }, 0);
                bookingClients.value.data = [e.bookings, ...bookingClients.value.data];
                toast.info(e.bookings.name + " vừa mua vé!", {
                    position: "top-center"
                })
            }
        );
    })

    const changeTab = (name) => { tab.value = name };

    const getEvent = async () => {
        let response = await getEventAPI(route.params.eventId);
        switch (response.status) {
            case HttpStatusCode.Ok:
                event.value = response.data;
                break;
            default:
                console.log(response);
                break;
        }
    }

    const getBookings = async (isLoadMore = false) => {
        let response = await getListBookingsAPI(route.params.eventId, bookingClients.value.current_page, searchString.value);
        switch (response.status) {
            case HttpStatusCode.Ok:
                if (!isLoadMore) bookingClients.value.data = [];
                for (let i = 0; i < response.data.data.length; i++) {
                    response.data.data[i].price = response.data.data[i].bookings.reduce((previous, current) => {
                        return previous + current.ticket_class.price;
                    }, 0);
                }
                bookingClients.value.data.push(...response.data.data);
                bookingClients.value.next_page_url = response.data.next_page_url;
                bookingClients.value.total = response.data.total;
                break;
            default:
                console.log(response);
                break;
        }
    }

    const loadMore = async () => {
        bookingClients.value.current_page++;
        await getBookings(true);
    }

    const confirmBooked = async () => {
        let isConfirm = confirm(`Bạn xác nhận việc thanh toán là thành công với khách hàng (${bookingSelected.value.name} - ${bookingSelected.value.phone_number})?`)
        if (!isConfirm) return;
        let response = await acceptBookingAPI(bookingSelected.value.event_id, bookingSelected.value.id);
        switch (response.status) {
            case HttpStatusCode.Ok:
                bookingClients.value.forEach(bookingClient => {
                    bookingClient.bookings.forEach(booking => {
                        booking.isBooked = true;
                        booking.isPending = false;
                        booking.start_pending = null;
                    })
                })
                toast.success("Xác nhận đơn mua vé thành công");
                break;
            default:
                toast.error("Xác nhận đơn mua vé thất bại");
        }
    }
</script>