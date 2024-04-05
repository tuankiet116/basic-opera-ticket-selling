<template>
    <div class="d-flex">
        <SideBar />
        <div class="w-100">
            <router-view></router-view>
        </div>
    </div>
</template>

<script setup>
import SideBar from '../../components/admin/SideBar.vue';
</script>