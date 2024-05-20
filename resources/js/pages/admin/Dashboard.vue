<template>
    <div class="d-flex">
        <Loading :isActive="isLoading"/>
        <SideBar />
        <div class="w-100">
            <router-view></router-view>
        </div>
    </div>
</template>

<script setup>
import Loading from '../../components/Loading.vue';
import SideBar from '../../components/admin/SideBar.vue';
import { useStoreLoading } from '../../pinia';
import { ref } from "vue";

const storeLoading = useStoreLoading();
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
</script>