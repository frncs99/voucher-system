<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { computed } from "vue";
import BackButton from "@/Components/BackButton.vue";

const props = defineProps({ status: Number });

const title = computed(() => {
    return {
        401: "401: Unauthorized",
        403: "403: Forbidden",
        404: "404: Page Not Found",
        419: "419: Page Expired",
        500: "500: Server Error",
        503: "503: Service Unavailable",
    }[props.status];
});

const description = computed(() => {
    return {
        401: "Sorry, you are not authorized.",
        403: "Sorry, you are forbidden from accessing this page.",
        404: "Sorry, the page you are looking for could not be found.",
        500: "Whoops, session expired.",
        500: "Whoops, something went wrong on our servers.",
        503: "Sorry, we are doing some maintenance. Please check back soon.",
    }[props.status];
});
</script>

<template>
    <Head :title="title" />
    
    <div
        class="relative sm:flex sm:justify-center sm:items-center min-h-screen bg-dots-darker bg-center bg-gray-100 dark:bg-dots-lighter dark:bg-gray-900 selection:bg-red-500 selection:text-white"
    >
        <div class="max-w-7xl mx-auto p-6 lg:p-8">
            <div class="container">
                <div class="row text-center">
                    <div
                        class="col-lg-6 offset-lg-3 col-sm-6 offset-sm-3 col-12 p-3 error-main"
                    >
                        <div class="row">
                            <div
                                class="col-lg-8 col-12 col-sm-10 offset-lg-2 offset-sm-1"
                            >
                                <h1 class="m-0">{{ title }}</h1>
                                <h6 class="uppercase tracking-wider">
                                    {{ description }}
                                    <br />
                                    <BackButton /> &nbsp; CLICK HERE TO GO BACK.
                                </h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
.error-main h1 {
    font-weight: bold;
    color: white;
    font-size: 150px;
    text-shadow: 2px 4px 5px #6e6e6e;
}
.error-main h6 {
    color: white;
    font-size: 30px;
}

.bg-dots-darker {
    background-image: url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z' fill='rgba(0,0,0,0.07)'/%3E%3C/svg%3E");
}
@media (prefers-color-scheme: dark) {
    .dark\:bg-dots-lighter {
        background-image: url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z' fill='rgba(255,255,255,0.07)'/%3E%3C/svg%3E");
    }
}
</style>
