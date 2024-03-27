'use strict';

(function () {
  const isDescendant = function isDescendant(parent, child) {
    let node = child.parentNode;

    while (node != null) {
      if (node === parent) {
        return true;
      }

      node = node.parentNode;
    }

    return false;
  };

  const backArrow = document.querySelector('.jinya-menu__back-arrow');
  const allMenuItems = document.querySelectorAll('.jinya-menu__item');
  const menu = document.querySelector('.jinya-menu');

  const hideAllExceptCurrent = function hideAllExceptCurrent(currentItem) {
    allMenuItems.forEach(function (item) {
      if (item !== currentItem) {
        item.classList.add('is--hidden');
      } else {
        item.classList.add('is--current');
        item.classList.remove('is--hidden');
        const subItems = item.querySelectorAll('.jinya-submenu__item');
        subItems.forEach(function (subItem) {
          if (!isDescendant(currentItem, subItem)) {
            subItem.classList.add('is--hidden');
          } else {
            subItem.classList.remove('is--hidden');
          }
        });
        backArrow.parentElement.classList.remove('is--hidden');
        menu.classList.add('is--open');
      }
    });
  };

  menu.querySelectorAll('.has--children')
    .forEach(function (item) {
      item.addEventListener('click', function (event) {
        if (event.target.classList.contains('has--children')) {
          event.preventDefault();
          hideAllExceptCurrent(event.target.parentElement);
        }
      });
    });
  backArrow.addEventListener('click', function () {
    document.querySelector('.is--current')
      .classList
      .remove('is--current');
    allMenuItems.forEach(function (item) {
      item.classList.remove('is--hidden');
    });
    backArrow.parentElement.classList.add('is--hidden');
    menu.classList.remove('is--open');
  });
})();
