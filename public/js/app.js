document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.alert:not(.alert-permanent), .js-auto-dismiss').forEach(function (element) {
        window.setTimeout(function () {
            element.style.opacity = '0';
            window.setTimeout(function () {
                element.remove();
            }, 250);
        }, 5000);
    });
});
