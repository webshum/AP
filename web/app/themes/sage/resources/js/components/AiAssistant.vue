<script setup>
import AiIcon from '../../images/ic-robot.svg';
import CloseIcon from '../../images/ic-close.svg';
import SendIcon from '../../images/ic-send.svg';
import UserIcon from '../../images/ic-user.svg';
import PreloaderIcon from '../../images/ic-preloader.svg';

import { ref, nextTick } from 'vue'; 

const dialog = ref([]);
const prompt = ref('');
const response = ref(null);
const preloader = ref(false);
const dialogRef = ref(null);
const showDialog = ref(false);
const url = import.meta.env.VITE_API_URL;

async function send() {
	preloader.value = true;

	try {
		const endpoint = `${url}/chat`;

		dialog.value.push({type: 'question', text: prompt.value});

		const res = await fetch(endpoint, {
			method: "POST",
			headers: {"Content-Type": "application/json"},
			body: JSON.stringify({ message: prompt.value }),
		})

		if (res.ok) {
			const data = await res.json();
			dialog.value.push({type: 'answer', text: data.choices[0].message.content});
			
			prompt.value = '';
			preloader.value = false;

			await nextTick();
			scrollToBottom();
		}
	} catch (e) {
		console.error(e);
		preloader.value = false;
	}
}

function scrollToBottom() {
	if (dialogRef.value) {
		dialogRef.value.scrollTop = dialogRef.value.scrollHeight;
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
			<div v-for="(message, index) in dialog" :key="index">
				<div class="answer" v-if="message.type == 'answer'">
					<div class="avatar">
						<AiIcon class="w-5 h-5 text-[#808cff]" />
					</div>
					<div class="message">{{ message.text }}</div>
				</div>

				<div class="question" v-if="message.type == 'question'">
					<div class="message">{{ message.text }}</div>
					<div class="avatar">
						<UserIcon class="w-5 h-5 text-[#808cff]" />
					</div>
				</div>	
			</div>
		</div>

		<div class="ask">
			<PreloaderIcon class="preloader" v-if="preloader" />

			<input v-model="prompt" class="text-input" type="text" placeholder="Ask a question...">

			<button @click.prevent="send()">
				<SendIcon class="w-7 h-7" />
			</button>
		</div>
	</form>
</template>