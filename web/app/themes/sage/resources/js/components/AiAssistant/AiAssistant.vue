<script setup>
import AiIcon from '../../../images/ic-robot.svg';
import CloseIcon from '../../../images/ic-close.svg';
import PreloaderIcon from '../../../images/ic-preloader.svg';
import SendIcon from '../../../images/ic-send.svg';

import { ref, nextTick, watch, onMounted } from 'vue';
import Dialog from './Dialog.vue'; 
import { getLang } from '../../helpers.js';

const dialog = ref([]);
const prompt = ref('');
const preloader = ref(false);
const dialogRef = ref(null);
const showDialog = ref(false);
const isDisabled = ref(false);
const url = import.meta.env.VITE_API_URL;
const STORAGE_KEY = 'ai_dialog_history';
const STORAGE_TTL = 1000 * 60 * 60 * 24; 

const props = defineProps({
	category: {
		type: String, 
		default: ''
	},
	welcome: {
		type: Boolean,
		default: true
	},
	design: {
		type: String,
		default: 'page',
	}
});

onMounted(() => {
	const savedData = localStorage.getItem(STORAGE_KEY);

	if (savedData) {
		const parsed = JSON.parse(savedData);
		const isExpired = Date.now() - parsed.timestamp > STORAGE_TTL;

		if (!isExpired && Array.isArray(parsed.dialog)) {
			dialog.value = parsed.dialog;
		} else {
			localStorage.removeItem(STORAGE_KEY);
		}
	}
});

watch(dialog, (newDialog) => {
	const dataToSave = {
		dialog: newDialog,
		timestamp: Date.now(),
	};

	localStorage.setItem(STORAGE_KEY, JSON.stringify(dataToSave));
}, { deep: true });

async function send() {
	if (!prompt.value) return;

	preloader.value = true;
	isDisabled.value = true;

	try {
		const endpoint = `${url}/chat`;

		dialog.value.push({
			role: 'user',
			content: prompt.value,
		});

		scrollBottom();

		const res = await fetch(endpoint, {
			method: "POST",
			headers: {"Content-Type": "application/json"},
			body: JSON.stringify({ 
				userMessage: prompt.value,
				message: dialog.value,
				category: props.category,
				lang: getLang()
			}),
		});

		if (res.ok) {
			const data = await res.json();
			dialog.value = data;
			scrollBottom();
		}
	} catch (e) {
		console.error(e);
	} finally {
		preloader.value = false;
		isDisabled.value = false;
		prompt.value = '';
	}
}

if (props.design == 'page') {
	watch(showDialog, async (newShowDialog, oldShowDialog) => {
		if (newShowDialog && !localStorage.getItem(STORAGE_KEY)) {
			preloader.value = true;
			isDisabled.value = true;

			const res = await fetch(`${url}/welcome`, {
				method: 'POST',
				headers: {"Content-Type": "application/json"},
				body: JSON.stringify({lang: getLang()})
			});

			if (res.ok) {
				const data = await res.json();
				dialog.value = data;
				preloader.value = false;
				isDisabled.value = false;
			}
		}
	});
}

async function scrollBottom() {
	await nextTick();
	dialogRef.value.scrollTop = dialogRef.value.scrollHeight;
}
</script>

<template>
	<button class="btn-ai" v-if="!showDialog && design == 'page'" @click="showDialog = true">
		<SendIcon class="w-8 h-8" />
	</button>

	<form 
		action="#" 
		:class="{
			'ai-assistant': true,
			'ai-frontend': design == 'frontend',
		}"
		v-if="showDialog || design == 'frontend'"
	>
		<div class="head" v-if="design == 'page'">
			<div>
				<div class="avatar">
					<AiIcon class="w-5 h-5 text-[#ff354c]" />
				</div>
				
				<h3>AI Assistant</h3>
			</div>
			
			<CloseIcon class="close w-5 h-5 text-white" @click="showDialog = false" />
		</div>

		<div v-if="Object.keys(dialog).length" class="dialog" ref="dialogRef">
			<Dialog :dialog="dialog"/>
		</div>
		
		<div 
			class="ask"
			:class="{disabled: isDisabled}"
		>
			<PreloaderIcon class="preloader" v-if="preloader" />

			<input v-model="prompt" class="text-input" type="text" placeholder="Zadaj pytanie...">

			<button @click.prevent="send()">
				<SendIcon class="w-7 h-7" />
			</button>
		</div>
	</form>
</template>