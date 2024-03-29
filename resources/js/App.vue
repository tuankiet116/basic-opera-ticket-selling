<script setup>
import Header from './components/Header.vue';
import Footer from './components/Footer.vue';

</script>
<template>
    <div class="container-fluid p-0">
        <Header/>
        <div class="min-vh-100">
            <router-view></router-view>
        </div>
        <Footer/>
    </div>
</template>