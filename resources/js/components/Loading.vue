<template>
    <Loading v-model:active="props.isActive" is-full-page color="rgb(205, 153, 86)"/>
</template>
<script setup>
import Loading from 'vue-loading-overlay';
import 'vue-loading-overlay/dist/css/index.css';

const props = defineProps({
    isActive: false
})
</script>