<script setup>
import Header from './components/Header.vue';
import Footer from './components/Footer.vue';

</script>
<template>
    <div class="container-fluid p-0">
        <Header/>
        <router-view></router-view>
        <Footer/>
    </div>
</template>