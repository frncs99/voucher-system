<template>
    <div v-if="links.length > 3">
        <div class="flex flex-wrap mt-8">
            <template v-for="(link, key) in links" :key="key">
                <div
                    v-if="link.url === null"
                    class="outline-gray dont-click mr-1 mb-1 px-4 py-3 text-sm leading-4 border rounded"
                    v-html="link.label"
                />

                <Link
                    v-else
                    class="outline-gray mr-1 mb-1 px-4 py-3 text-sm leading-4 border rounded hover:bg-white focus:border-primary focus:text-primary"
                    :class="{ 'bg-blue-700 text-white': link.active }"
                    :href="link.url"
                >
                <span v-html="link.label"></span>
                </Link>
            </template>
        </div>
    </div>
</template>

<script>
import { defineComponent } from "vue";
import { Link } from '@inertiajs/vue3';
export default defineComponent({
    components: {
        Link,
    },
    props: {
        links: Array,
    },
});
</script>

<style scoped>
.bg-blue-700 {
    background-color: #2583E6;
    border-color: #2583E6 !important;
}

.outline-gray {
    border-color: gray;
}

.dont-click {
    opacity: 0.4;
}
</style>