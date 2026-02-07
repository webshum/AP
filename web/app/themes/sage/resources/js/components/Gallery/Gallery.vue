<script setup>
import { onMounted, ref, computed } from 'vue';
import { Splide, SplideSlide } from '@splidejs/vue-splide';
import '@splidejs/vue-splide/css/core';

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

const splideOptions = computed(() => {
    const isSlider = categories.length >= 5;

    return {
        type: 'slide',
        perPage: 5,
        gap: 30,
        pagination: false,
        arrows: isSlider,
        breakpoints: {
            1400: {
                arrows: true,
                perPage: 4
            },
            767: {perPage: 1},
        }
    };
});

function onSlideMoved(splide, newIndex) {
    if (window.innerWidth <= 767) {
        const category = categories[newIndex];
        if (category) getImages(category.term_id);
    }
}
</script>

<template>
    <div class="gallery-categories">
        <Splide 
            :options="splideOptions"
            :class="{'is-centered': categories.length < 5}"
            @splide:moved="onSlideMoved"
        >
            <SplideSlide
                v-for="category in categories" 
                :key="category.term_id"
                :class="{active: activeCategory === category.term_id}"
                @click="getImages(category.term_id)"
            >
                <div class="slide">{{ category.name }}</div>
            </SplideSlide>
        </Splide>
    </div>

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