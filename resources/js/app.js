import Alpine from 'alpinejs';
import { initRecaptchaForms } from './recaptcha';
import { initImageUploadZones } from './image-upload';

window.Alpine = Alpine;

Alpine.start();
initRecaptchaForms();

document.addEventListener('DOMContentLoaded', initImageUploadZones);
