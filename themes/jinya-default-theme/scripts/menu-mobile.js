(() => {
  const hamburger = document.querySelector('.jinya-menu__hamburger');
  const menu = document.querySelector('.jinya-menu');
  const menuItems = menu.querySelectorAll('.jinya-menu__item');
  const backArrow = menu.querySelector('.has--back-arrow');
  const openSubmenu = (event) => {
    const submenus = menu.querySelectorAll('.jinya-submenu');
    submenus?.forEach((submenu) => submenu.classList.add('is--hidden'));
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
    const currentItem = menu?.querySelector('.is--current');

    hamburger.classList.add('is--open');
    menuItems.forEach((item) => {
      const link = item?.querySelector('.has--children');
      link?.classList.remove('has--children');
      link?.setAttribute('data-has-children', true);

      item?.classList.remove('is--hidden');
      item?.querySelector('.jinya-menu__link')
        ?.addEventListener('click', openSubmenu, true);
    });

    menu?.classList.add('is--open');
    backArrow?.classList.add('is--hidden');
    currentItem?.classList.remove('is--current');
    document.body.classList.add('is--menu-open');

    // eslint-disable-next-line
    event.target.addEventListener('click', closeHamburger);
  }

  function closeHamburger(event) {
    event.target.removeEventListener('click', closeHamburger);

    event.preventDefault();

    const childLinks = menu?.querySelectorAll('[data-has-children="true"]');
    childLinks?.forEach((item) => item.classList.add('has--children'));

    hamburger.addEventListener('click', openHamburger);
    hamburger.classList.remove('is--open');
    menu?.classList.remove('is--open');
    document.body.classList.remove('is--menu-open');
  }

  hamburger.addEventListener('click', openHamburger);
})();
