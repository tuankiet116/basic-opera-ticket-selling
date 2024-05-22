<template>
    <div class="container">
        <div class="wrapper-1">
            <div class="wrapper-2 text-center">
                <img src="https://i.ibb.co/Lkn7rkG/thank-you-envelope.png" alt="thank-you-envelope" border="0" />
                <h1 class="fs-4" v-html="$t('success.title')"></h1>
                <p class="fs-6" v-html="$t('success.content') + $t('success.content_0') + $t('success.content_1') + $t('success.content_2')"></p>
                <p class="fs-6" v-html="$t('success.hotline')"></p>
            </div>
            <div class="footer-like">
                <p></p>
            </div>
        </div>
    </div>
</template>
