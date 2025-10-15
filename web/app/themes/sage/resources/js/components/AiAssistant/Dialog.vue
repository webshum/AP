<script setup>
import UserIcon from '../../../images/ic-user.svg';
import AiIcon from '../../../images/ic-robot.svg';
import ArchitectIcon from '../../../images/ic-architect.svg';
import FireIcon from '../../../images/ic-fire.svg';
import WaterIcon from '../../../images/ic-water.svg';
import AirIcon from '../../../images/ic-air.svg';
import SunIcon from '../../../images/ic-sun.svg';
import LightningIcon from '../../../images/ic-lightning.svg';

import { ref } from 'vue';
import SaveAnswer from './SaveAnswer.vue';
import { marked } from 'marked'

const expandedMessages = ref({});
const messageRefs = ref({});
const needsReadMore = ref({});

const props = defineProps({
	dialog: {
		type: Array,
		default: []
	}
});

const icons = {
	architect: ArchitectIcon,
	fire: FireIcon,
	water: WaterIcon,
	air: AirIcon,
	sun: SunIcon,
	lightning: LightningIcon,
	user: UserIcon
}

const renderMarkdown = (text) => {
	return marked.parse(text || '');
};

function toggleExpand(index) {
	expandedMessages.value[index] = !expandedMessages.value[index];
} 

const checkHeight = (el, index) => {
	if (el) {
		messageRefs.value[index] = el;
		needsReadMore.value[index] = el.scrollHeight > 190;
	}
}
</script>

<template>
	<article v-for="(message, index) in dialog" :key="index">
		<!-- Assistant -->
		<div 
			:class="`agent-${message.name} answer`" 
			v-if="message.role === 'assistant'"
		>
			<div class="avatar">
				<span>{{ message.name }}</span>
				<component 
					:is="icons[message.name] || icons.architect" 
					:class="`ic-${message.name} size-6`"
				/>
			</div>
			<div class="message">
				<div
					:ref="(el) => checkHeight(el, index)"
					v-html="renderMarkdown(message.content)"
					:style="{
						maxHeight: expandedMessages[index] ? 'none' : '190px',
						overflow: 'hidden'
					}"
				></div>

				<div 
					class="read-more"
					v-if="needsReadMore[index]"
					@click="toggleExpand(index)"
				>
					{{ expandedMessages[index] ? 'Read Less' : 'Read More' }}
				</div>
			</div>
		</div>

		<!-- User -->
		<div class="question" v-else-if="message.role === 'user'">
			<div class="message">{{ message.content }}</div>
			<div class="avatar">
				<UserIcon class="w-5 h-5 text-[#808cff]" />
			</div>
		</div>
	</article>
</template>
