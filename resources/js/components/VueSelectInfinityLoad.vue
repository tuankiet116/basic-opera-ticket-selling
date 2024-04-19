<template>
    <VueSelect :options="props.data" :filterable="true" @open="onOpen" @close="onClose" @search="fetchData">
        <template #list-footer>
            <li v-show="hasNextPage" ref="load" class="loader">
                Đang tìm kiếm dữ liệu...
            </li>
        </template>
    </VueSelect>
</template>

<script setup>
import VueSelect from "vue-select";
import { ref, onMounted } from "vue";

let observer = ref(null);
let load = ref(null);
const emits = defineEmits("fetchData");
const props = defineProps({
    data: {
        type: Array,
        default: []
    }
});

onMounted(() => {
    observer.value = new IntersectionObserver(infiniteScroll)
});

const onOpen = async () => {
    if (load.value) {
        observer.value.observe(load.value)
    }
}
const onClose = () => {
    observer.value.disconnect()
}
const infiniteScroll = async ([{ isIntersecting, target }]) => {
    if (isIntersecting) {
        const ul = target.offsetParent
        const scrollTop = target.offsetParent.scrollTop
        ul.scrollTop = scrollTop
    }
}

const fetchData = () => {
    emits("fetchData");
}
</script>

<style scoped>
.loader {
    text-align: center;
    color: #bbbbbb;
}
</style>