<template>
    <VueSelect :options="props.list.data" :filterable="true" @open="onOpen" @close="onClose" @search="fetchData">
        <template v-slot:option="option">
            <span :class="option.name"></span>
            {{ option.name }}
        </template>
        <!-- <template #list-footer>
            <li v-show="hasNextPage" ref="load" class="loader">
                Đang tìm kiếm dữ liệu...
            </li>
        </template> -->
    </VueSelect>
</template>

<script setup>
import VueSelect from "vue-select";
import { ref, onMounted } from "vue";

let observer = ref(null);
let load = ref(null);
const emits = defineEmits(["fetchData"]);
const props = defineProps({
    list: {
        type: Object,
        default: {}
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

const fetchData = (search) => {
    emits("fetchData", search);
}
</script>

<style scoped>
.loader {
    text-align: center;
    color: #bbbbbb;
}
</style>