<script setup>
import { onMounted, ref } from 'vue';

const images = ref([]);
const activeCategory = ref(null);
const preloader = ref(false);
const url = import.meta.env.VITE_API_URL;

const { categories } = defineProps({
    categories: {
        type: Object,
        default: {}
    }
});

async function getImages(id) {
    try {
        preloader.value = true;

        const res = await fetch(url + '/gallery', {
            method: 'POST',
            headers: {"Content-Type": "application/json"},
            body: JSON.stringify({
                id: id
            })
        });

        if (res.ok) {
            const data = await res.json();
            images.value = data;
            activeCategory.value = id;
            preloader.value = false;
        }
    } catch (e) {
        console.error(e);
    } finally {
        preloader.value = false;
    }
}

onMounted(async () => {
    if (categories[0]) {
        await getImages(categories[0].term_id);
    }
});
</script>

<template>
    <ul class="gallery-categories">
        <li 
            v-for="category in categories" 
            :key="category.term_id"
            :class="{active: activeCategory === category.term_id}"
            @click="getImages(category.term_id)"
        >
            {{ category.name }}
        </li>
    </ul>

    <div class="gallery-grid" v-if="images && images.length">
        <a 
            data-fancybox="gallery" 
            :data-src="image.url"
            :data-caption="image.alt || ''"
            v-for="(image, index) in images" :key="index"
        >
            <img :src="image.url" :alt="image.alt">
        </a>
    </div>

    <div id="main-preloader" v-if="preloader">
        <svg><use xlink:href="#settings"></use></svg>
    </div>
</template>