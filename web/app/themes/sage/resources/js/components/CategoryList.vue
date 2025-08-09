<script setup>
import { onMounted, ref } from 'vue';

const categories = ref([]);
const currentCategory = ref(null);

async function fetchCategoriesData() {
	const response = await fetch('http://localhost:8300/wp-json/categories/v1/list');

	if (response.ok) {
		const json = await response.json();
		categories.value = json;
	}
}

async function goToCategory(category) {
	currentCategory.value = category;
	history.pushState({}, '', `/category/${category.slug}`);

	setTimeout(() => {
		window.location.href = `/category/${category.slug}`;
	}, 1000);
}

onMounted(() => {
	fetchCategoriesData();
});
</script>

<template>
	<transition name="fade-slide" mode="out-in">
		<ul
			class="categories"
			v-if="categories.length"
			:key="currentCategory?.id || 'root'"
		>
			<li v-for="(category, index) in categories" :key="index">
				<a href="#" @click.prevent="goToCategory(category)">
					<img :src="category.image" alt="">
					<span>{{ category.name }}</span>
				</a>
			</li>
		</ul>
	</transition>
</template>

<style>
.fade-slide-enter-active,
.fade-slide-leave-active {
	transition: all 1.5s ease;
}

.fade-slide-enter-from {
	
}

.fade-slide-leave-to {
  	transform: scale(0);
}
</style>