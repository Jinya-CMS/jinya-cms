/**
 * main.js
 * http://www.codrops.com
 *
 * Licensed under the MIT license.
 * http://www.opensource.org/licenses/mit-license.php
 *
 * Copyright 2014, Codrops
 * http://www.codrops.com
 */
class DesignerMenu {
    private static init = (() => {
        let designerMenu = new DesignerMenu();
        designerMenu.initEvents();
    })();
    private isOpen = false;
    private body: HTMLBodyElement;
    private content: HTMLDivElement;
    private openbtn: HTMLButtonElement;
    private initEvents = () => {
        this.openbtn.addEventListener('click', this.toggleMenu);

        // close the menu element if the target it´s not the menu element or one of its descendants..
        this.content.addEventListener('click', (evt) => {
            let target = evt.target;
            if (this.isOpen && target !== this.openbtn) {
                this.toggleMenu();
            }
        });
    };
    private toggleMenu = () => {
        if (this.isOpen) {
            classie.remove(this.body, 'show-menu');
        }
        else {
            classie.add(this.body, 'show-menu');
        }
        this.isOpen = !this.isOpen;
    };

    private constructor() {
        this.content = document.querySelector<HTMLDivElement>('.content-wrap');
        this.openbtn = document.querySelector<HTMLButtonElement>('[data-toggle=designer-menu]');
        this.body = document.querySelector<HTMLBodyElement>('body');
    }
}