import { defineStore } from "pinia";
import { ref } from "vue";

export const useAuthenticateStore = defineStore("authentication", () => {
    const isAdminLoggedIn = ref(false);
    function setAdminLoggedIn(isLoggedIn: boolean = true) {
        isAdminLoggedIn.value = isLoggedIn;
    }
    return { isAdminLoggedIn, setAdminLoggedIn };
});