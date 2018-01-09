class MenuItemModalViewModel {
    save = () => {
        this.modal.trigger('save');
        this.modal.hide(true, true);
    };
    private modal: Modal;

    constructor(modal: Modal, menuItem: MenuItem, fetchUrl: string) {
        this.item(menuItem);
        this.modal = modal;

        this.item().pageType.subscribe(newValue => {
            if (newValue !== 'empty' && newValue !== 'external') {
                let call = new Ajax.Request(`${fetchUrl}?type=${newValue}`);
                call.get().then(value => {
                    this.item().routes.removeAll();
                    for (let i = 0; i < value.routes.length; i++) {
                        let route = new Route(value.routes[i]);
                        this.item().routes.push(route);
                    }
                });
            } else {
                this.item().route(new Route({url: '#', name: '#', parameter: '#'}));
                if (newValue === 'empty') {
                    this.item().displayUrl('');
                }
            }
        });
    }

    private _canSave = ko.pureComputed(() => {
        return this.item().valid();
    });

    get canSave(): KnockoutComputed<any> {
        return this._canSave;
    }

    set canSave(value: KnockoutComputed<any>) {
        this._canSave = value;
    }

    private _item = ko.observable<MenuItem>();

    get item(): KnockoutObservable<MenuItem> {
        return this._item;
    }

    set item(value: KnockoutObservable<MenuItem>) {
        this._item = value;
    }
}