import Alpine from 'alpinejs';
import '@hotwired/turbo';
import { initRecaptchaForms } from './recaptcha';
import { initImageUploadZones } from './image-upload';

window.Alpine = Alpine;

const bootPageScripts = () => {
    initRecaptchaForms();
    initImageUploadZones();
};

document.addEventListener('turbo:load', () => {
    Alpine.start();
    bootPageScripts();
});

document.addEventListener('turbo:before-cache', () => {
    document.querySelectorAll('[x-data]').forEach((element) => {
        if (element._x_dataStack) {
            Alpine.destroyTree(element);
        }
    });
});
