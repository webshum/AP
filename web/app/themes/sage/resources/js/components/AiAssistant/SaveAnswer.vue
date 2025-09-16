<script setup>
import SaveIcon from '../../../images/ic-save.svg';
import { ref } from 'vue';

const disabled = ref(false);
const url = import.meta.env.VITE_API_URL;

const props = defineProps({
	convIndex: {
		type: Number, 
		default: 0
	},
	dialog: {
		type: Array, 
		default: []
	}
});

async function saveAnswer(index) {
	disabled.value = true;

	try {
		const endpoint = `${url}/faqs`;

		const res = await fetch(endpoint, {
			method: 'POST',
			headers: {"Content-Type": "application/json"},
			body: JSON.stringify(props.dialog[index])
		});

		if (res.ok) {
			const data = await res.json();
		}
	} catch (e) {
		console.error(e);
	}
}
</script>

<template>
	<SaveIcon 
		class="btn-save w-5 h-5 text-[#808cff]" 
		:class="{disabled: disabled}" 
		@click="saveAnswer(convIndex)" 
	/>
</template>