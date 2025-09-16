<script setup>
import AiIcon from '../../images/ic-robot.svg';
import CloseIcon from '../../images/ic-close.svg';
import SendIcon from '../../images/ic-send.svg';
import UserIcon from '../../images/ic-user.svg';
import PreloaderIcon from '../../images/ic-preloader.svg';
import SaveIcon from '../../images/ic-save.svg';

import { ref, nextTick, watch, onMounted } from 'vue'; 

const dialog = ref([]);
const prompt = ref('');
const response = ref(null);
const preloader = ref(false);
const dialogRef = ref(null);
const showDialog = ref(false);
const url = import.meta.env.VITE_API_URL;

onMounted(() => {
	const saved  = localStorage.getItem('chatMessages');

	if (saved) dialog.value = JSON.parse(saved);
});

watch(dialog, (newValue) => {
	localStorage.setItem('chatMessages', JSON.stringify(newValue));
}, { deep: true });

async function send() {
	if (!prompt.value) return;

	preloader.value = true;

	try {
		const endpoint = `${url}/chat`;

		dialog.value.push([{
			role: 'user',
			content: prompt.value
		}]);

		const res = await fetch(endpoint, {
			method: "POST",
			headers: {"Content-Type": "application/json"},
			body: JSON.stringify({ message: dialog.value }),
		})

		if (res.ok) {
			const data = await res.json();
			let index = dialog.value.length - 1;

			dialog.value[index].push({
				role: 'assistant',
				content: data.choices[0].message.content
			});
		}
	} catch (e) {
		console.error(e);
	} finally {
		preloader.value = false;
		prompt.value = '';

		await nextTick();
		scrollToBottom();
	}
}

function scrollToBottom() {
	if (dialogRef.value) {
		dialogRef.value.scrollTop = dialogRef.value.scrollHeight;
	}
}

async function saveAnswer(index) {
	preloader.value = true;

	try {
		const endpoint = `${url}/faqs`;

		const res = await fetch(endpoint, {
			method: 'POST',
			headers: {"Content-Type": "application/json"},
			body: JSON.stringify(dialog.value[index])
		});

		if (res.ok) {
			const data = await res.json();
		}
	} catch (e) {
		console.error(e);
	} finally {
		preloader.value = false;
	}
}
</script>

<template>
	<button class="btn-ai" v-if="!showDialog" @click="showDialog = true">
		<SendIcon class="w-8 h-8" />
	</button>

	<form action="#" class="ai-assistant" v-if="showDialog">
		<div class="head">
			<div>
				<div class="avatar">
					<AiIcon class="w-5 h-5 text-[#ff354c]" />
				</div>
				
				<h3>AI Assistant</h3>
			</div>
			
			<CloseIcon class="close w-5 h-5 text-white" @click="showDialog = false" />
		</div>
		
		<div class="dialog" ref="dialogRef" v-if="dialog.length">
			<article v-for="(conversation, convIndex) in dialog" :key="convIndex">
				<div v-for="(message, msgIndex) in conversation" :key="msgIndex">
					<div class="question" v-if="message.role == 'user'">
						<div class="message">{{ message.content }}</div>
						<div class="avatar">
							<UserIcon class="w-5 h-5 text-[#808cff]" />
						</div>
					</div>	

					<div class="answer" v-if="message.role == 'assistant'">
						<div class="avatar">
							<AiIcon class="w-5 h-5 text-[#808cff]" />
						</div>
						<div class="message">
							{{ message.content }}
							<SaveIcon class="btn-save w-5 h-5 text-[#808cff]" @click="saveAnswer(convIndex)" />
						</div>
					</div>
				</div>
			</article>
		</div>

		<div class="ask">
			<PreloaderIcon class="preloader" v-if="preloader" />

			<input v-model="prompt" class="text-input" type="text" placeholder="Zadaj pytanie...">

			<button @click.prevent="send()">
				<SendIcon class="w-7 h-7" />
			</button>
		</div>
	</form>
</template>