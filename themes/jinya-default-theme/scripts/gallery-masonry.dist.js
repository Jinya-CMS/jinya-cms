"use strict";

(function () {
  document.addEventListener('DOMContentLoaded', function () {
    var items = document.querySelectorAll('[data-action=masonry-click]');
    items.forEach(function (item) {
      var openModal = function openModal() {
        var parent = item.parentElement;
        var clone = parent.cloneNode(true);
        clone.classList.add('is--modal');
        document.body.classList.add('is--open');
        clone.querySelector('img').removeEventListener('click', openModal);
        document.body.appendChild(clone);

        var closeModal = function closeModal() {
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