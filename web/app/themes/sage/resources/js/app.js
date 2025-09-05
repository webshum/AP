import { createApp } from 'vue/dist/vue.esm-bundler';
import CategoryList from './components/CategoryList.vue';
import.meta.glob([
  '../images/**',
  '../fonts/**',
]);

window.onload = () => {
    if (document.querySelector('.categories-horizontal')) headerSticky();
    if (document.querySelector('.hexagon-categories')) anchor();
    if (document.querySelector('.cursor')) cursor();

    document.querySelector('.menu-open').onclick = e => {
        e.preventDefault();

        document.body.classList.toggle('menu-opened');
    }
}

function headerSticky() {
    const sticky = document.querySelector('.categories-horizontal');

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            console.log(entry);
            if (!entry.isIntersecting) {

                sticky.classList.add('is-sticky');
            } else {
                sticky.classList.remove('is-sticky');
            }
        });
    }, {
        threshold: [1.0],
        rootMargin: "-1px 0px 0px 0px"
    });

    observer.observe(sticky);
}

function anchor() {
    const sections = document.querySelectorAll(".post-category");
    const navLinks = document.querySelectorAll(".hexagon-categories li");

    if (!sections.length || !navLinks.length) {
        console.warn("No sections or nav links found.");
        return;
    }

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    navLinks.forEach((link) => link.classList.remove("active"));

                    const activeLink = document.querySelector(
                        `.hexagon-categories li[data-id="${entry.target.id}"]`
                    );

                    if (activeLink) {
                        activeLink.classList.add("active");
                    } else {
                        console.warn(`No link found for section ID: ${entry.target.id}`);
                    }
                }
            });
        },
        {
            root: null,
            rootMargin: "-20% 0px -20% 0px",
            threshold: 0.2,
        }
    );

    sections.forEach((section) => {
        if (section.id) {
            observer.observe(section);
        } else {
            console.warn("Section missing ID:", section);
        }
    });
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

const app = createApp({});
app.component('category-list', CategoryList);
app.mount('#categories');