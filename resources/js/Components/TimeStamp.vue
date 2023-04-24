<script setup>

const currentdate = new Date(); 
const datetime = "Last Sync: " + currentdate.getFullYear() + "-"
                + (currentdate.getMonth()+1)  + "-"
                + currentdate.getDate() + " "  
                + currentdate.getHours() + ":"  
                + currentdate.getMinutes() + ":" 
                + currentdate.getSeconds();
</script>

<template>
    <span class="font-normal text-gray-800 leading-tight text-sm">
        {{ datetime }}
    </span>
</template>