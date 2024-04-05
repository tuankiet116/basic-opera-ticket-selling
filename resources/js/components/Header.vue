<template>
    <div class="container-fluid shadow row pe-0 w-100 position-sticky top-0 bg-white w-100 m-0 z-1">
        <div class="container-fluid py-2 d-flex row">
            <div class="col p-0">
                <router-link :to="{ name: 'index' }">
                    <img height="50px" src="../../images/image.png" />
                </router-link>
            </div>
            <div class="col text-center p-0">
                <h1 class="fs-2">Booking Tickets</h1>
            </div>
            <div class="col p-0"></div>
        </div>
    </div>
</template>
<script setup>
import { ref } from 'vue'

const counter = ref(0)
</script>