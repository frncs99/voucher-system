<script setup>
import { router } from '@inertiajs/vue3';

function refresh() {
    router.get(window.location.href);
}
</script>

<template>
    <a class="font-normal text-xs" href="#" @click="refresh"><i class="fa fa-refresh" aria-hidden="true"></i></a>
</template>