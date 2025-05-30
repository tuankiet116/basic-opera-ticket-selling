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
        names: Array<string>,
    }>, eventId: number | null) {
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

export const useStoreExportStatus = defineStore("storeExportStatus", () => {
    const toastId: Ref<number | null> = ref(null);
    function setToastId(value: number) {
        toastId.value = value;
    }
    return { toastId, setToastId };
});

export const useStoreTemporaryBooking = defineStore("storeTemporaryBooking", () => {
    const token: Ref<string | null> = ref(null);
    const timeRemaining: Ref<number> = ref(10 * 60 * 1000);

    function setToken(tokenTemporary: string | null) {
        token.value = tokenTemporary;
    }

    function setTimeRemaining(time: number) {
        timeRemaining.value = time;
    }
    return { token, setToken, timeRemaining, setTimeRemaining };
});