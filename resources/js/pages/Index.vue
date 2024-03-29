<template>
    <div class="container-sm">
        <h3>Danh sách sự kiện</h3>
        <div class="row shadow border-bottom bg-white">
            <div class="col-4">
                <figure class="image__wrapper" v-lazyload>
                    <img class="image__item w-100"
                        :data-url="'https://img.hoguomopera.vn/files/upload/event/poster-ba-n-ve-ngang-32-ba-n-mo-i-20240317044559.png'"
                        alt="random image">
                </figure>
            </div>
            <div class="col-8">
                <h4 class="text-primary">Four Seasons Concert</h4>
            </div>
        </div>
    </div>
</template>
<script setup>
</script>