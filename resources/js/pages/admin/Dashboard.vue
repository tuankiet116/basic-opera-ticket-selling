<template>
    <div class="d-flex">
        <Loading :isActive="isLoading" />
        <SideBar />
        <div class="w-100">
            <router-view></router-view>
        </div>
    </div>
</template>

<script setup>
import Loading from '../../components/Loading.vue';
import SideBar from '../../components/admin/SideBar.vue';
import { useStoreExportStatus, useStoreLoading } from '../../pinia';
import { ref, onMounted, onUnmounted } from "vue";
import { useToast } from 'vue-toastification';

const storeLoading = useStoreLoading();
const storeExportStatus = useStoreExportStatus();
const toast = useToast();
let isLoading = ref(false);
storeLoading.$onAction(({
    name, // name of the action
    store, // store instance, same as `someStore`
    args, // array of parameters passed to the action
    after, // hook after the action returns or resolves
    onError, // hook if the action throws or rejects
}) => {
    after(() => {
        isLoading.value = storeLoading.isLoading;
    })
});

onMounted(() => {
    window.Echo.private(`admin.notifications`)
        .listen("AdminSystemNotification", (e) => {
            let toastId = storeExportStatus.toastId;
            toast.update(toastId, {
                content: e.message,
                options: {
                    type: e.status ? "success" : "error",
                    timeout: 2000
                }
            })
        })
})

onUnmounted(() => {
    window.Echo.leave(`admin.notifications`);
})
</script>