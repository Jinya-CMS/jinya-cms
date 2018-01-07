class Menu {
    static init = (() => {
        let menu = document.querySelector('.menu > ul');
        let menuItems = menu.querySelectorAll('li');

        for (let i = 0; i < menuItems.length; i++) {
            let menuItem = menuItems[i];
            if (menuItem.querySelectorAll('ul').length > 0) {
                menuItem.addEventListener('click', () => {
                    let openMenuItems = menu.querySelectorAll('li.open');

                    for (let i = 0; i < openMenuItems.length; i++) {
                        let menuItem = openMenuItems[i];
                        classie.remove(menuItem, 'open');
                    }
                    classie.add(menuItem, 'open');
                });
            }
        }
    })();
}