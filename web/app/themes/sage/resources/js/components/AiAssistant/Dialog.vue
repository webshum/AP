<script setup>
import UserIcon from '../../../images/ic-user.svg';
import AiIcon from '../../../images/ic-robot.svg';
import SaveAnswer from './SaveAnswer.vue';

const props = defineProps({
	dialog: {
		type: Array,
		default: []
	}
});
</script>

<template>
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
					<SaveAnswer :dialog="dialog" :convIndex="convIndex" />
				</div>
			</div>
		</div>
	</article>
</template>