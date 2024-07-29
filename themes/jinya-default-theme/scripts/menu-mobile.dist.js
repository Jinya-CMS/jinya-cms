'use strict';

(function () {
  const hamburger = document.querySelector('.jinya-menu__hamburger');
  const menu = document.querySelector('.jinya-menu');
  const menuItems = menu.querySelectorAll('.jinya-menu__item');
  const backArrow = menu.querySelector('.has--back-arrow');

  const openSubmenu = function openSubmenu(event) {
    const submenus = menu.querySelectorAll('.jinya-submenu');
    submenus === null || submenus === void 0 ? void 0 : submenus.forEach(function (submenu) {
      return submenu.classList.add('is--hidden');
    });
    const currentSubmenu = event.target.nextElementSibling;

    if (currentSubmenu) {
      event.stopPropagation();
      event.preventDefault();
      currentSubmenu.classList.remove('is--hidden');
    }
  };

  function openHamburger(event) {
    event.preventDefault();
    event.target.removeEventListener('click', openHamburger);
    const currentItem = menu === null || menu === void 0 ? void 0 : menu.querySelector('.is--current');
    hamburger.classList.add('is--open');
    menuItems.forEach(function (item) {
      let _item$querySelector;

      const link = item === null || item === void 0 ? void 0 : item.querySelector('.has--children');
      link === null || link === void 0 ? void 0 : link.classList.remove('has--children');
      link === null || link === void 0 ? void 0 : link.setAttribute('data-has-children', true);
      item === null || item === void 0 ? void 0 : item.classList.remove('is--hidden');
      item === null || item === void 0 ? void 0 : (_item$querySelector = item.querySelector('.jinya-menu__link')) === null || _item$querySelector === void 0 ? void 0 : _item$querySelector.addEventListener('click', openSubmenu, true);
    });
    menu === null || menu === void 0 ? void 0 : menu.classList.add('is--open');
    backArrow === null || backArrow === void 0 ? void 0 : backArrow.classList.add('is--hidden');
    currentItem === null || currentItem === void 0 ? void 0 : currentItem.classList.remove('is--current');
    document.body.classList.add('is--menu-open'); // eslint-disable-next-line

    event.target.addEventListener('click', closeHamburger);
  }

  function closeHamburger(event) {
    event.target.removeEventListener('click', closeHamburger);
    event.preventDefault();
    const childLinks = menu === null || menu === void 0 ? void 0 : menu.querySelectorAll('[data-has-children="true"]');
    childLinks === null || childLinks === void 0 ? void 0 : childLinks.forEach(function (item) {
      return item.classList.add('has--children');
    });
    hamburger.addEventListener('click', openHamburger);
    hamburger.classList.remove('is--open');
    menu === null || menu === void 0 ? void 0 : menu.classList.remove('is--open');
    document.body.classList.remove('is--menu-open');
  }

  hamburger.addEventListener('click', openHamburger);
})();
