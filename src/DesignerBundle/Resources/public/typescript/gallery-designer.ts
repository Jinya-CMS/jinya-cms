/**
 * main3.js
 * http://www.codrops.com
 *
 * Licensed under the MIT license.
 * http://www.opensource.org/licenses/mit-license.php
 *
 * Copyright 2014, Codrops
 * http://www.codrops.com
 */
class GalleryDesigner {
    static getInstance = () => {
        return GalleryDesigner.instance ? GalleryDesigner.instance : GalleryDesigner.instance = new GalleryDesigner();
    };
    private static instance: GalleryDesigner;
    toggleMenu = () => {
        if (this.isAnimating) return false;
        this.isAnimating = true;
        if (this.isOpen) {
            classie.remove(this.bodyEl, 'designer-show-menu');
            // animate path
            setTimeout(() => {
                // reset path
                this.path.attr('d', this.initialPath);
                this.isAnimating = false;
            }, 300);
        }
        else {
            classie.add(this.bodyEl, 'designer-show-menu');
            // animate path
            this.path.animate({'path': this.pathOpen}, 400, mina.easeinout, () => {
                this.isAnimating = false;
            });
        }
        this.isOpen = !this.isOpen;
    };
    init = () => {
        this.initEvents();
    };
    initEvents = () => {
        this.openbtn.addEventListener('click', this.toggleMenu);
        if (this.closebtn) {
            this.closebtn.addEventListener('click', this.toggleMenu);
        }

        // close the menu element if the target it´snap not the menu element or one of its descendants..
        this.content.addEventListener('click', (ev) => {
            let target = ev.target;
            if (this.isOpen && target !== this.openbtn) {
                this.toggleMenu();
            }
        });
    };
    private morphEl: HTMLElement;
    private path: Snap.Element;
    private initialPath: string;
    private pathOpen;
    private isAnimating: boolean;
    private bodyEl: HTMLElement;
    private content: Element | any;
    private openbtn: HTMLElement | any;
    private closebtn: HTMLElement | any;
    private isOpen: boolean;
    private snap: Snap.Paper;

    private constructor() {
        this.bodyEl = document.body;
        this.content = document.querySelector('.content-wrap');
        this.openbtn = document.getElementById('designer-open-button');
        this.closebtn = document.getElementById('designer-close-button');
        this.isOpen = false;
        this.morphEl = document.getElementById('morph-shape');
        this.snap = Snap(this.morphEl.querySelector('svg'));
        this.path = this.snap.select('path');
        this.initialPath = this.path.attr('d');
        this.pathOpen = this.morphEl.getAttribute('data-morph-open');
        this.isAnimating = false;
    }
}

(function () {
    GalleryDesigner.getInstance().init();
})();