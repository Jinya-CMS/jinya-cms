"use strict";

(function () {
  var isDescendant = function isDescendant(parent, child) {
    var node = child.parentNode;

    while (node != null) {
      if (node === parent) {
        return true;
      }

      node = node.parentNode;
    }

    return false;
  };

  var backArrow = document.querySelector('.jinya-menu__back-arrow');
  var allMenuItems = document.querySelectorAll('.jinya-menu__item');
  var menu = document.querySelector('.jinya-menu');

  var hideAllExceptCurrent = function hideAllExceptCurrent(currentItem) {
    allMenuItems.forEach(function (item) {
      if (item !== currentItem) {
        item.classList.add('is--hidden');
      } else {
        item.classList.add('is--current');
        item.classList.remove('is--hidden');
        var subItems = item.querySelectorAll('.jinya-submenu__item');
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

  menu.querySelectorAll('.has--children').forEach(function (item) {
    item.addEventListener('click', function (event) {
      if (event.target.classList.contains('has--children')) {
        event.preventDefault();
        hideAllExceptCurrent(event.target.parentElement);
      }
    });
  });
  backArrow.addEventListener('click', function () {
    document.querySelector('.is--current').classList.remove('is--current');
    allMenuItems.forEach(function (item) {
      item.classList.remove('is--hidden');
    });
    backArrow.parentElement.classList.add('is--hidden');
    menu.classList.remove('is--open');
  });
})();