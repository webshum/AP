import { createApp } from 'vue/dist/vue.esm-bundler';
import CategoryList from './components/CategoryList.vue';
import.meta.glob([
  '../images/**',
  '../fonts/**',
]);

window.onload = () => {
    cursor();
    anchor();
}

function cursor() {
    let curs = document.querySelector('.cursor');

    document.addEventListener('mousemove', (e) => {
        if (!curs) return;
        let x = e.pageX;
        let y = e.pageY;
        curs.style.left = (x - 175) + "px";
        curs.style.top = (y - 175) + "px";
        curs.style.opacity = "1";
    });
}

function anchor() {
    const singleContent = document.querySelector('.single-content');
    const aside = singleContent.querySelector('aside');
    const anchors = singleContent.querySelectorAll('[id^="anchor-"]');

    anchors.forEach(anchor => {
        const link = document.createElement('a');
        link.href = `#${anchor.id}`;
        link.className = 'hexagono';
        link.dataset.label = anchor.textContent;
        aside.appendChild(link);
    }); 
    console.log(anchors);
}

const app = createApp({});
app.component('category-list', CategoryList);
app.mount('#categories');