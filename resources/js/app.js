import './bootstrap';
import '../css/app.css';

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.alert:not(.alert-permanent), .js-auto-dismiss').forEach((element) => {
        window.setTimeout(() => {
            element.style.opacity = '0';
            window.setTimeout(() => element.remove(), 250);
        }, 5000);
    });
});
