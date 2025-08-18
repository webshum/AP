import { createApp } from 'vue/dist/vue.esm-bundler';
import CategoryList from './components/CategoryList.vue';
import.meta.glob([
  '../images/**',
  '../fonts/**',
]);

window.onload = () => {
    if (document.querySelector('.cursor')) cursor();
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

function addAnchores() {
    const article = document.querySelector(".js-content");
    const aside = document.querySelector(".js-aside");

    if (!article || !aside) return;

    const headings = article.querySelectorAll("h1, h2, h3, h4, h5, h6");

    if (!headings.length) return;

    const ul = document.createElement("ul");
    ul.classList.add('hexagon-categories');

    headings.forEach((heading, index) => {
        if (!heading.id) {
            heading.id = "heading-" + index;
        }

        const li = document.createElement("li");
        li.classList.add(heading.tagName.toLowerCase());

        const a = document.createElement("a");
        a.href = "#" + heading.id;
        a.textContent = heading.textContent;

        const i = document.createElement("a");
        i.href = "#" + heading.id;
        i.classList.add('ic-hexagon');

        li.appendChild(a);
        li.appendChild(i);
        ul.appendChild(li);
    });

    aside.appendChild(ul);
}

if (document.querySelector(".js-content")) {
    addAnchores();
}

const app = createApp({});
app.component('category-list', CategoryList);
app.mount('#categories');