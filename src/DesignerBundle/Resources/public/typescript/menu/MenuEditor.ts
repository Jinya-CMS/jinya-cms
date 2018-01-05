class MenuEditorViewModel {
    submit = () => {
        let data = JSON.parse(Util.jsonStringifyWithoutCycle(ko.toJS(this)));
        let call = new Ajax.Request(this.submitUrl);
        let dfd: Promise<any>;

        if (this.menuId) {
            dfd = call.put(data);
        } else {
            dfd = call.post(data);
        }

        dfd.then(value => {
            location.href = value.redirectTarget;
        }, (reason: Ajax.Error) => {
            Modal.alert(reason.message, reason.details.message);
        });
    };
    addItemAfter = (selectedItem: MenuItem) => {
        this.menu().addItemAfter(selectedItem);
    };
    addItemBefore = (selectedItem: MenuItem) => {
        this.menu().addItemBefore(selectedItem);
    };
    private loadMenu = (loadUrl: string) => {
        let call = new Ajax.Request(loadUrl);
        call.get().then((data) => {
            let menu = new Menu(data);
            this.menu(menu);
        }, (error) => {
            Modal.alert(error.message, error.details.message);
        });
    };
    private submitUrl: string;
    private menuId: number;

    constructor(element: Element) {
        this.submitUrl = element.getAttribute('data-submit-url');

        if (element.hasAttribute('data-menu-id')) {
            this.menuId = parseInt(element.getAttribute('data-menu-id'));
            this.loadMenu(element.getAttribute('data-load-url'));
        }
    }

    private _edit = ko.observable<boolean>(true);

    get edit(): KnockoutObservable<boolean> {
        return this._edit;
    }

    set edit(value: KnockoutObservable<boolean>) {
        this._edit = value;
    }

    private _menu = ko.observable<Menu>(new Menu('{}'));

    get menu(): KnockoutObservable<Menu> {
        return this._menu;
    }

    set menu(value: KnockoutObservable<Menu>) {
        this._menu = value;
    }
}

class MenuEditor {
    static init = (() => {
        let element = document.querySelector('[data-menu-editor]');
        if (element) {
            let vm = new MenuEditorViewModel(element);
            ko.applyBindings(vm, element);
        }
    })();
}