import { defineStore } from "pinia";
import { ref, type Ref } from "vue";
import { PreBookingData } from "./types/seats";

export const useAuthenticateStore = defineStore("authentication", () => {
    const isAdminLoggedIn = ref(false);
    function setAdminLoggedIn(isLoggedIn: boolean = true) {
        isAdminLoggedIn.value = isLoggedIn;
    }
    return { isAdminLoggedIn, setAdminLoggedIn };
});

export const useStoreBooking = defineStore("storeBooking", () => {
    const seatBooking: Ref<Array<any>> = ref([]);
    const eventBooking: Ref<number | null> = ref(null);
    function setBooking(seats: Array<{
        hall: number,
        names: Array<string>
    }>, eventId: number) {
        seatBooking.value = seats;
        eventBooking.value = eventId;
    }
    return { seatBooking, setBooking, eventBooking };
});

export const useStoreLoading = defineStore("storeLoading", () => {
    const isLoading: Ref<boolean> = ref(false);
    function setIsLoading(value: boolean) {
        isLoading.value = value;
    }
    return { isLoading, setIsLoading };
})