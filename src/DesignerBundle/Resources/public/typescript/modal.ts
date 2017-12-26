class Modal {
    static get = (element: Element, cache: boolean = true): Modal => {
        if (cache) {
            let modal = Modal.modals[element.id];
            if (modal) {
                return modal;
            }
        }

        return new Modal(element, cache);
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
    static alert = (title: string, message: string, okButton: string = texts['generic.close']) => {
        return new Promise((resolve, reject) => {
            let id = Math.random();
            let template = `
<div class="md-modal" id="alert-modal-${id}">
    <div class="md-content">
        <span class="md-title">${title}</span>
        <div>
            <p>${message}</p>
        </div>    
        <div class="md-footer">
            <button data-action="close"
                    class="button button-round-s button-border-medium primary">${okButton}</button>
        </div>
    </div>
</div>
            `;
            let openModals = document.querySelectorAll('.md-show.md-modal');
            classiex.remove(openModals, 'md-show');
            classiex.add(openModals, 'md-hidden-for-alert');
            let modalElement = Util.htmlToElement(template);
            Modal.body.appendChild(modalElement);

            let modal = Modal.get(modalElement);
            modal.canHide = false;
            modal.on('closed', () => {
                let hiddenModals = document.querySelectorAll('.md-hidden-for-alert');
                classiex.add(openModals, 'md-show');
                classiex.remove(openModals, 'md-hidden-for-alert');
                Modal.body.removeChild(modalElement);
                resolve({
                    'clicked': true
                });
                modal.showOverlay();
            });
            modal.show();
        });
    };
    static confirm = (title: string, message: string, positiveButton = Util.getText('generic.yes'), negativeButton = Util.getText('generic.no'), ignoreButton = Util.getText('generic.cancel')) => {
        return new Promise((resolve, reject) => {
            let id = Math.random();
            let template = `
<div class="md-modal" id="alert-modal-${id}">
    <div class="md-content">
        <span class="md-title">${title}</span>
        <div>
            <p>${message}</p>
        </div>    
        <div class="md-footer">
            <button data-action="positive"
                    class="button button-round-s button-border-medium primary">${positiveButton}</button>
            <button data-action="negative"
                    class="button button-round-s button-border-medium secondary">${negativeButton}</button>
            <button data-action="ignore"
                    class="button button-round-s button-border-medium secondary inverse button-right">${ignoreButton}</button>
        </div>
    </div>
</div>
            `;
            let openModals = document.querySelectorAll('.md-show.md-modal');
            classiex.remove(openModals, 'md-show');
            classiex.add(openModals, 'md-hidden-for-alert');
            let modalElement = Util.htmlToElement(template);
            Modal.body.appendChild(modalElement);

            let modal = Modal.get(modalElement);
            modal.canHide = false;
            modal.on('closed', () => {
                let hiddenModals = document.querySelectorAll('.md-hidden-for-alert');
                classiex.add(openModals, 'md-show');
                classiex.remove(openModals, 'md-hidden-for-alert');
                Modal.body.removeChild(modalElement);
            });
            modal.on('opening', () => {
                let positiveButton = modalElement.querySelector('[data-action=positive]');
                positiveButton.addEventListener('click', () => {
                    resolve(true);
                    modal.hide(false);
                });

                let negativeButton = modalElement.querySelector('[data-action=negative]');
                negativeButton.addEventListener('click', () => {
                    resolve(false);
                    modal.hide(false);
                });

                let ignoreButton = modalElement.querySelector('[data-action=ignore]');
                ignoreButton.addEventListener('click', () => {
                    reject();
                    modal.hide(false);
                });
            });
            modal.show();
        });
    };
    private static modals = {};
    private static overlay: Element = document.createElement('div');
    private static current: Modal;
    private static body = document.querySelector('body');
    show = () => {
        this.trigger('opening');
        this.showOverlay();
        setTimeout(() => {
            classie.add(this.modalElement, 'md-show');
        }, 300);

        Modal.current = this;
        this.trigger('opened');
    };
    hide = (removeOverlay = true) => {
        this.trigger('closing');
        classie.remove(this.modalElement, 'md-show');
        if (removeOverlay) {
            classie.remove(Modal.body, 'md-show');
            setTimeout(() => {
                if (Modal.body.querySelector('.md-overlay')) {
                    try {
                        Modal.body.removeChild(Modal.overlay);
                    } catch {
                    }
                }
            }, 300);
        }
        this.trigger('closed');
    };
    on = (event: string, callback: (args) => void) => {
        this.subscriber[event] = this.subscriber[event] || [];
        this.subscriber[event].push(callback);
    };
    trigger = (event: string, data?: any) => {
        let callbacks = this.subscriber[event] || [];
        for (let i = 0; i < callbacks.length; i++) {
            callbacks[i]({
                event: event,
                data: data,
                callOrder: i
            });
        }
    };
    private showOverlay = () => {
        Modal.body.appendChild(Modal.overlay);
        setTimeout(() => {
            classie.add(Modal.body, 'md-show');
        }, 300);
    };
    private modalElement: Element;
    private subscriber = {};

    constructor(selector: string | Element, cache: boolean = true) {
        if (typeof(selector) === 'string') {
            this.modalElement = document.querySelector(selector);
        } else {
            this.modalElement = selector;
        }

        classie.add(Modal.overlay, 'md-overlay');
        Modal.overlay.addEventListener('click', () => {
            if (Modal.current.canHide) {
                Modal.current.hide();
            }
        });

        let closeButtons = this.modalElement.querySelectorAll('[data-action=close]');
        for (let i = 0; i < closeButtons.length; i++) {
            closeButtons[i].addEventListener('click', () => {
                this.hide();
            });
        }

        if (cache) {
            Modal.modals[this.modalElement.id] = this;
        }
    }

    private _canHide: boolean;

    get canHide(): boolean {
        return this._canHide;
    }

    set canHide(value: boolean) {
        this._canHide = value;
    }
}

Modal.activate();