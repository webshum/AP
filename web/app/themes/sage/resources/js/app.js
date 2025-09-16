import { createApp } from 'vue/dist/vue.esm-bundler';
import CategoryList from './components/CategoryList.vue';
import AiAssistant from './components/AiAssistant.vue';
import.meta.glob([
  '../images/**',
  '../fonts/**',
]);

window.onload = () => {
    if (document.querySelector('.categories-horizontal')) headerSticky();
    if (document.querySelector('.hexagon-categories')) anchor();
    if (document.querySelector('.faq-accordeon')) accordion_toggle();
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

function accordion_toggle() {
    let accordeon = document.querySelectorAll('.accordeon');
    let flag = true;

    if (accordeon != null) {
        for (let i = 0; i < accordeon.length; i++) {
            const item = accordeon[i].querySelectorAll('.item-accordeon');

            for (let j = 0; j < item.length; j++) {
                if (item[j].classList.contains('active')) {
                    let inner = item[j].querySelector('.inner');
                    let content = item[j].querySelector('.content-accordeon'); 
                    content.style.height = (inner.clientHeight + 2) + 'px';
                }

                let btn = item[j].querySelector('.btn-accordeon');
                
                btn.addEventListener('click', openAccordeon);
            }
        }
    }

    function openAccordeon(e) {
        let item = this.closest('.accordeon').querySelectorAll('.item-accordeon');
        let inner = this.parentNode.querySelector('.inner');
        let content = this.parentNode.querySelector('.content-accordeon');  

        if (this.parentNode.classList.contains('active')) {            
            this.parentNode.classList.remove('active');
            content.removeAttribute('style');
        } else {   
            this.parentNode.classList.add('active');
            content.style.height = (inner.clientHeight + 2) + 'px';
        }    
    }
}

const app = createApp({});
app.component('category-list', CategoryList);
app.mount('#categories');

const ai = createApp({});
ai.component('ai-assistant', AiAssistant);
ai.mount('#ai');