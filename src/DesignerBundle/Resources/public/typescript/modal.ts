class Modal {
    static activate = () => {
        let triggers = document.querySelectorAll('[data-toggle=modal]');

        for (let i = 0; i < triggers.length; i++) {
            let trigger = triggers.item(i);
            let target = trigger.getAttribute('data-target');
            let modal = new Modal(target);
            trigger.addEventListener(trigger.getAttribute('data-trigger') || 'click', () => {
                modal.show();
            });
        }
    };
    private static overlay: Element = document.createElement('div');
    private static current: Modal;
    show = () => {
        this.body.appendChild(Modal.overlay);
        setTimeout(() => {
            classie.add(this.body, 'md-show');
            classie.add(this.modalElement, 'md-show');
        }, 300);

        Modal.current = this;
    };
    hide = () => {
        classie.remove(this.body, 'md-show');
        classie.remove(this.modalElement, 'md-show');
        setTimeout(() => {
            if (this.body.querySelector('.md-overlay')) {
                this.body.removeChild(Modal.overlay);
            }
        }, 300);
    };
    private modalElement: Element;
    private body: Element;

    constructor(selector: string) {
        this.modalElement = document.querySelector(selector);
        this.body = document.querySelector('body');

        classie.add(Modal.overlay, 'md-overlay');
        Modal.overlay.addEventListener('click', () => {
            Modal.current.hide();
        });

        let closeButtons = this.modalElement.querySelectorAll('[data-action=close]');
        for (let i = 0; i < closeButtons.length; i++) {
            closeButtons[i].addEventListener('click', () => {
                this.hide();
            });
        }
    }
}

Modal.activate();