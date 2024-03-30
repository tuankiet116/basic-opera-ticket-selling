<template>
    <div class="container-sm">
        <div class="row justify-content-center align-items-center">
            <div class="col-10">
                <h3 class="py-5 fs-3">Danh sách sự kiện</h3>
                <router-link :to="{ name: 'book-ticket', params: { eventId: 'fake' } }">
                    <div class="row shadow border-bottom bg-white">
                        <div class="col-4">
                            <figure class="image__wrapper" v-lazyload>
                                <img class="image__item w-100"
                                    :data-url="'https://img.hoguomopera.vn/files/upload/event/poster-ba-n-ve-ngang-32-ba-n-mo-i-20240317044559.png'"
                                    alt="random image">
                            </figure>
                        </div>
                        <div class="col-8">
                            <h4 class="text-primary fs-4">Four Seasons Concert</h4>
                            <button class="btn btn-primary fw-bold rounded-pill fs-5">Đặt vé</button>
                            <p class="desc fs-5 text-truncate-custom">
                                Chương trình Hòa nhạc Cổ điển Four Seasons, nằm trong chuỗi sự kiện quốc tế Musical
                                Seasons.
                                Dưới sự dẫn dắt tài ba của Nhạc trưởng Stefan Plewniak, Dàn nhạc Giao hưởng Nhà hát
                                Hoàng
                                gia Versailles và Contre-tenor Théo Imart, kiệt tác “The Four Seasons’’ (Le Quattro
                                Stagioni) của Antonio Vivaldi sẽ được tái hiện đầy kỹ thuật và tinh tế trong hai đêm 21
                                và
                                22/04 tới, hứa hẹn trở thành món quà tinh thần quý giá dành tặng cho những tâm hồn yêu
                                nghệ
                                thuật.
                                Đừng bỏ lỡ cơ hội sở hữu tấm vé của 2 đêm hòa nhạc từ một trong những dàn nhạc nổi tiếng
                                nhất thế giới này nhé!
                            </p>
                        </div>
                    </div>
                </router-link>
            </div>
        </div>
    </div>
</template>
<script setup>
</script>