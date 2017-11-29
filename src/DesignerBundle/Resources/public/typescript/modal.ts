class Modal {
    static modals = {};
    static get = (element: Element) => {
        let modal = Modal.modals[element.id];
        if (modal) {
            return modal;
        }
        return new Modal(element);
    };
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
        this.trigger('opening');
        this.body.appendChild(Modal.overlay);
        setTimeout(() => {
            classie.add(this.body, 'md-show');
            classie.add(this.modalElement, 'md-show');
        }, 300);

        Modal.current = this;
        this.trigger('opened');
    };
    hide = () => {
        this.trigger('closing');
        classie.remove(this.body, 'md-show');
        classie.remove(this.modalElement, 'md-show');
        setTimeout(() => {
            if (this.body.querySelector('.md-overlay')) {
                this.body.removeChild(Modal.overlay);
            }
        }, 300);
        this.trigger('closed');
    };
    on = (event: string, callback: (args) => void) => {
        this.subscriber[event] = this.subscriber[event] || [];
        this.subscriber[event].push(callback);
    };
    private trigger = (event: string, data?: any) => {
        let callbacks = this.subscriber[event] || [];
        for (let i = 0; i < callbacks.length; i++) {
            callbacks[i]({
                event: event,
                data: data,
                callOrder: i
            });
        }
    };

    private modalElement: Element;
    private body: Element;
    private subscriber = {};

    constructor(selector: string | Element) {
        if (selector instanceof String) {
            this.modalElement = document.querySelector(selector);
        } else {
            this.modalElement = selector;
        }
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

        Modal.modals[this.modalElement.id] = this;
    }
}

Modal.activate();