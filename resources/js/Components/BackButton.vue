<script setup>
function back () {
    window.history.back();
}
</script>

<template>
    <a href="#" @click="back"><i class="fa fa-arrow-left" aria-hidden="true"></i></a>
</template>