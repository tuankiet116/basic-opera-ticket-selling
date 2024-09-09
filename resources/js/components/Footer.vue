<template>
    <div class="container-fluid text-center">
        <hr />
        <div class="row justify-content-center">
            <div class="text-start col-auto">
                <h1 class="fs-3">{{ $t("footer.company_name") }}</h1>
                <p class="mb-2">
                    <svg width="20px" focusable="false" aria-hidden="true" viewBox="0 0 24 24">
                        <path d="M4 10h3v7H4zm6.5 0h3v7h-3zM2 19h20v3H2zm15-9h3v7h-3zm-5-9L2 6v2h20V6z"></path>
                    </svg>
                    {{ $t("footer.address") }}: {{ $t("footer.address_detail") }}
                </p>
                <p class="mb-2">
                    <svg width="20px" focusable="false" aria-hidden="true" viewBox="0 0 24 24">
                        <path
                            d="M20.01 15.38c-1.23 0-2.42-.2-3.53-.56-.35-.12-.74-.03-1.01.24l-1.57 1.97c-2.83-1.35-5.48-3.9-6.89-6.83l1.95-1.66c.27-.28.35-.67.24-1.02-.37-1.11-.56-2.3-.56-3.53 0-.54-.45-.99-.99-.99H4.19C3.65 3 3 3.24 3 3.99 3 13.28 10.73 21 20.01 21c.71 0 .99-.63.99-1.18v-3.45c0-.54-.45-.99-.99-.99z">
                        </path>
                    </svg>
                    {{ $t("footer.phone_number") }}: <a href="tel:0888011280">088.801.1280</a> / <a
                        href="tel:0825583888">082.558.3888</a>
                </p>
                <p class="mb-2">
                    <svg width="20px" focusable="false" aria-hidden="true" viewBox="0 0 24 24">
                        <path
                            d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 14H4V8l8 5 8-5v10zm-8-7L4 6h16l-8 5z">
                        </path>
                    </svg>
                    E-mail: <a href="mailto:nhatminhshd.media@gmail.com">nhatminhshd.media@gmail.com</a>
                </p>
            </div>
        </div>
        <hr />
        <p>{{ $t("footer.copy_right") }}</p>
    </div>
</template>