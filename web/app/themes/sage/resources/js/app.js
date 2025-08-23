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

    const headings = Array.from(article.querySelectorAll("h1, h2, h3, h4, h5, h6"));
    if (!headings.length) return;

    // ---------- TOC (aside) ----------
    const ul = document.createElement("ul");
    ul.classList.add("hexagon-categories");

    headings.forEach((heading, index) => {
        if (!heading.id) heading.id = "heading-" + index;

        const li = document.createElement("li");
        li.classList.add(heading.tagName.toLowerCase());

        const a = document.createElement("a");
        a.href = "#" + heading.id;
        a.textContent = heading.textContent;

        const i = document.createElement("a");
        i.href = "#" + heading.id;
        i.classList.add("ic-hexagon");

        li.appendChild(a);
        li.appendChild(i);
        ul.appendChild(li);
    });

    // очистимо, щоб не плодити дублікати
    aside.innerHTML = "";
    aside.appendChild(ul);

    // ---------- Розбиття контенту від анхора до анхора ----------
    // Використовуємо Range, щоб коректно вирізати вміст незалежно від вкладеності елементів
    const blocksFrag = document.createDocumentFragment();

    for (let i = 0; i < headings.length; i++) {
        const start = headings[i];
        const next = headings[i + 1] || null;

        if (!start.isConnected) continue;

        const range = document.createRange();
        range.setStartBefore(start);

        if (next && next.isConnected) {
            range.setEndBefore(next);
        } else if (article.lastChild) {
            range.setEndAfter(article.lastChild);
        } else {
            // якщо з якоїсь причини немає lastChild — пропускаємо
            continue;
        }

        const block = document.createElement("div");
        block.classList.add("anchor-block");
        block.dataset.anchor = start.id;

        const contents = range.extractContents(); // вирізає зі статті від заголовка до наступного заголовка
        block.appendChild(contents);
        blocksFrag.appendChild(block);
    }

    // Замінюємо контент статті тільки на зібрані блоки (від анхора до анхора)
    article.innerHTML = "";
    article.appendChild(blocksFrag);
}

if (document.querySelector(".js-content")) {
    addAnchores();
}

const app = createApp({});
app.component('category-list', CategoryList);
app.mount('#categories');