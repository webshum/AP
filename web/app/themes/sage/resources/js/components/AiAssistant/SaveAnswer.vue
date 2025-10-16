<script setup>
import SaveIcon from '../../../images/ic-save.svg';
import { ref, onMounted } from 'vue';
import { marked } from 'marked';

const disabled = ref(false);
const url = import.meta.env.VITE_API_URL;

const props = defineProps({
	dialog: {
		type: Object, 
		default: {}
	},
	userMessage: {
		type: Object,
		default: {}
	}
});

async function saveAnswer() {
	disabled.value = true;
	const params = {
		title: props.userMessage.content,
		content: marked.parse(props.dialog.content)
	};

	try {
		const endpoint = `${url}/faqs`;

		const res = await fetch(endpoint, {
			method: 'POST',
			headers: {"Content-Type": "application/json"},
			body: JSON.stringify(params)
		});

		if (res.ok) {
			if (res.status == 201) {
				disabled.value = true;
			} else {
				const data = await res.json();
			}
		}
	} catch (e) {
		console.error(e);
	}
}

async function hasAnswer() {
	const params = { title: props.userMessage.content };
	const endpoint = `${url}/faqs/has`;

	const res = await fetch(endpoint, {
		method: 'POST',
		headers: { "Content-Type": "application/json" },
		body: JSON.stringify(params)
	});

	if (res.ok) {
		const data = await res.json();
		console.log(data);
		if (data.status === 'exists') {
			disabled.value = true;
		}
	}
}

onMounted(async () => hasAnswer());
</script>

<template>
	<SaveIcon 
		class="btn-save w-5 h-5 text-[#808cff]" 
		:class="{disabled: disabled}" 
		@click="saveAnswer()" 
	/>
</template>