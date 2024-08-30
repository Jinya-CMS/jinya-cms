'use strict';

(function () {
  document.addEventListener('DOMContentLoaded', function () {
    const items = document.querySelectorAll('[data-action=masonry-click]');
    items.forEach(function (item) {
      const openModal = function openModal() {
        const parent = item.parentElement;
        const clone = parent.cloneNode(true);
        clone.classList.add('is--modal');
        document.body.classList.add('is--open');
        clone.querySelector('img')
          .removeEventListener('click', openModal);
        document.body.appendChild(clone);

        const closeModal = function closeModal() {
          clone.classList.remove('is--open');
          setTimeout(function () {
            document.body.classList.remove('is--open');
            document.body.removeChild(clone);
          }, 501);
        };

        clone.addEventListener('click', closeModal);
        setTimeout(function () {
          return clone.classList.add('is--open');
        }, 10);
      };

      item.addEventListener('click', openModal);
    });
  });
})();
