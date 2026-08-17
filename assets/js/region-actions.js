// File role: UI actions for region management pages.

document.addEventListener('DOMContentLoaded', function () {
    const deleteLinks = document.querySelectorAll('[data-delete-region]');

    deleteLinks.forEach(function (link) {
        link.addEventListener('click', function (event) {
            const shouldDelete = window.confirm('Are you sure you want to delete this region?');
            if (!shouldDelete) {
                event.preventDefault();
            }
        });
    });
});
