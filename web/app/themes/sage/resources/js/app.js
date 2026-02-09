import { createApp } from 'vue/dist/vue.esm-bundler';
import CategoryList from './components/CategoryList.vue';
import AiAssistant from './components/AiAssistant/AiAssistant.vue';
import Gallery from './components/Gallery/Gallery.vue';

import { Fancybox } from "@fancyapps/ui/dist/fancybox/";
import "@fancyapps/ui/dist/fancybox/fancybox.css";

import.meta.glob([
  '../images/**',
  '../fonts/**',
]);

window.addEventListener('load', () => {
    Fancybox.bind('[data-fancybox]', {});
    
    parts();

    if (document.getElementById('main-preloader')) {
        const preloader = document.getElementById('main-preloader');
        setTimeout(() => preloader.remove(), 2000);
    }

    if (document.querySelector('.categories-horizontal')) headerSticky();
    if (document.querySelector('.hexagon-categories')) anchor();
    if (document.querySelector('.faq-accordeon')) accordion_toggle();

    document.querySelector('.menu-open').onclick = e => {
        e.preventDefault();

        document.body.classList.toggle('menu-opened');
    }

    document.querySelector('.popup-overlay').onclick = e => document.body.classList.remove('menu-opened');
});

function parts() {
    const particleCount = 50;
    const container = document.body.querySelector('.particle-wrap');
    
    for (let i = 0; i < particleCount; i++) {
        const particle = document.createElement('div');
        particle.className = 'particle';
        const size = Math.random() * 2.5 + 0.5; // Half size (0.5 to 3px)
        particle.style.width = `${size}px`;
        particle.style.height = `${size}px`;
        particle.style.left = `${Math.random() * 100}vw`;
        particle.style.top = `${Math.random() * 100}vh`;
        const duration = Math.random() * 20 + 100; // Slower animation (20-40s)
        const animationName = `move${Math.floor(Math.random() * 4) + 1}`;
        particle.style.animation = `${animationName} ${duration}s linear infinite`;
        container.appendChild(particle);
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

    if (!sections.length || !navLinks.length) return;

    function updateActiveLink() {
        const scrollPosition = window.scrollY + 150; 
        let currentSection = null;
        let minDistance = Infinity;

        sections.forEach((section) => {
            const sectionTop = section.offsetTop;
            const distance = Math.abs(scrollPosition - sectionTop);

            if (scrollPosition >= sectionTop - 200 && distance < minDistance) {
                minDistance = distance;
                currentSection = section;
            }
        });

        if (currentSection) {
            navLinks.forEach((link) => link.classList.remove("active"));
            
            const activeLink = document.querySelector(
                `.hexagon-categories li[data-id="${currentSection.id}"]`
            );
            
            if (activeLink) {
                activeLink.classList.add("active");
            }
        }
    }

    navLinks.forEach((link) => {
        link.addEventListener("click", (e) => {
            const targetId = link.getAttribute("data-id");
            const targetSection = document.getElementById(targetId);

            if (targetSection) {
                e.preventDefault();
                
                navLinks.forEach((l) => l.classList.remove("active"));
                link.classList.add("active");

                targetSection.scrollIntoView({
                    behavior: "smooth",
                    block: "start"
                });
            }
        });
    });

    let timeout;
    window.addEventListener("scroll", () => {
        if (timeout) clearTimeout(timeout);
        timeout = setTimeout(updateActiveLink, 5);
    });

    updateActiveLink();
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

const gallery = createApp({});
gallery.component('gallery', Gallery);
gallery.mount('#gallery-category')