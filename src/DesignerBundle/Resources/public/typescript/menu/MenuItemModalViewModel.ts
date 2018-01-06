class MenuItemModalViewModel {
    save = () => {
        this.modal.trigger('save');
        this.modal.hide(true, true);
    };
    private modal: Modal;

    constructor(modal: Modal, menuItem: MenuItem, fetchUrl: string) {
        this.item(menuItem);
        this.modal = modal;

        this.selectedType.subscribe(newValue => {
            if (newValue !== 'empty') {
                let call = new Ajax.Request(`${fetchUrl}?type=${newValue}`);
                call.get().then(value => {
                    this.routes(value.routes);
                });
            } else {
                this.selectedRoute({parameter: '', name: '#'});
            }
        });
        this.selectedRoute.subscribe(newValue => {
            this.item().route().parameter(newValue.parameter);
            this.item().route().name(newValue.name);
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

    private _selectedType = ko.observable<string>();

    get selectedType(): KnockoutObservable<string> {
        return this._selectedType;
    }

    set selectedType(value: KnockoutObservable<string>) {
        this._selectedType = value;
    }

    private _routes = ko.observableArray();

    get routes(): KnockoutObservableArray<any> {
        return this._routes;
    }

    set routes(value: KnockoutObservableArray<any>) {
        this._routes = value;
    }

    private _selectedRoute = ko.observable();

    get selectedRoute(): KnockoutObservable<any> {
        return this._selectedRoute;
    }

    set selectedRoute(value: KnockoutObservable<any>) {
        this._selectedRoute = value;
    }
}