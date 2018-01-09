class Route {
    constructor(data: any | string) {
        if (data instanceof String) {
            data = JSON.parse(data.toString());
        }
        if (data) {
            this.name(data.name);
            this.parameter(data.parameter);
            this.url(data.url);
        }
    }

    private _valid = ko.pureComputed(() => {
        return this.name() && this.url();
    });

    get valid(): KnockoutComputed<any> {
        return this._valid;
    }

    set valid(value: KnockoutComputed<any>) {
        this._valid = value;
    }

    private _name = ko.observable<string>();

    get name(): KnockoutObservable<string> {
        return this._name;
    }

    set name(value: KnockoutObservable<string>) {
        this._name = value;
    }

    private _url = ko.observable<string>();

    get url(): KnockoutObservable<string> {
        return this._url;
    }

    set url(value: KnockoutObservable<string>) {
        this._url = value;
    }

    private _parameter = ko.observableArray<string>();

    get parameter(): KnockoutObservableArray<string> {
        return this._parameter;
    }

    set parameter(value: KnockoutObservableArray<string>) {
        this._parameter = value;
    }
}

class MenuItem {
    addItemAfter = (selectedItem: MenuItem) => {
        MenuTools.addItem(this).then((item: MenuItem) => {
            let currentItemIndex = this.children.indexOf(selectedItem) + 1;
            if (currentItemIndex === -1) {
                this.children.push(item);
            } else {
                this.children.splice(currentItemIndex, 0, item);
            }
        });
    };
    addItemBefore = (selectedItem: MenuItem) => {
        MenuTools.addItem(this).then((item: MenuItem) => {
            let currentItemIndex = this.children.indexOf(selectedItem);
            this.children.splice(currentItemIndex, 0, item);
        });
    };
    editItem = (item: MenuItem) => {
        MenuTools.editItem(item).then((item: MenuItem) => {
        });
    };
    deleteItem = (item: MenuItem) => {
        MenuTools.deleteItem(item).then((item: MenuItem) => {
            this.parent().children.remove(item);
        });
    };

    constructor(data: any | string, parent: MenuItem | Menu) {
        if (data instanceof String) {
            data = JSON.parse(data.toString());
        }

        this.route(new Route(null));

        if (data) {
            let route = new Route(data.route);
            this.id(data.id || Date.now());
            this.title(data.title);
            this.pageType(data.pageType);
            this.displayUrl(data.displayUrl);
            if (parent instanceof MenuItem) {
                this.parent(parent);
            } else if (parent instanceof Menu) {
                this.menu(parent);
            }
            this.route(route);

            if (data.children) {
                for (let i = 0; i < data.children.length; i++) {
                    let child = data.children[i];
                    let menuItem = new MenuItem(child, this);
                    this.children.push(menuItem);
                }
            }
        }
    }

    private _displayUrl = ko.observable<string>();

    get displayUrl(): KnockoutObservable<string> {
        return this._displayUrl;
    }

    set displayUrl(value: KnockoutObservable<string>) {
        this._displayUrl = value;
    }

    private _menu = ko.observable<Menu>();

    get menu(): KnockoutObservable<Menu> {
        return this._menu;
    }

    set menu(value: KnockoutObservable<Menu>) {
        this._menu = value;
    }

    private _pageType = ko.observable<string>();

    get pageType(): KnockoutObservable<string> {
        return this._pageType;
    }

    set pageType(value: KnockoutObservable<string>) {
        this._pageType = value;
    }

    private _routes = ko.observableArray<Route>();

    get routes(): KnockoutObservableArray<Route> {
        return this._routes;
    }

    set routes(value: KnockoutObservableArray<Route>) {
        this._routes = value;
    }

    private _valid = ko.pureComputed(() => {
        let valid = this.route() && this.route().valid() && this.title();

        for (let i = 0; i < this.children.length; i++) {
            let child = this.children()[i];
            valid = valid && child.valid();
        }

        return valid;
    });

    get valid(): KnockoutComputed<any> {
        return this._valid;
    }

    set valid(value: KnockoutComputed<any>) {
        this._valid = value;
    }

    private _id = ko.observable<number>();

    get id(): KnockoutObservable<number> {
        return this._id;
    }

    set id(value: KnockoutObservable<number>) {
        this._id = value;
    }

    private _title = ko.observable<string>();

    get title(): KnockoutObservable<string> {
        return this._title;
    }

    set title(value: KnockoutObservable<string>) {
        this._title = value;
    }

    private _route = ko.observable<Route>(new Route(null));

    get route(): KnockoutObservable<Route> {
        return this._route;
    }

    set route(value: KnockoutObservable<Route>) {
        this._route = value;
    }

    private _parent = ko.observable<MenuItem>();

    get parent(): KnockoutObservable<MenuItem> {
        return this._parent;
    }

    set parent(value: KnockoutObservable<MenuItem>) {
        this._parent = value;
    }

    private _children = ko.observableArray<MenuItem>();

    get children(): KnockoutObservableArray<MenuItem> {
        return this._children;
    }

    set children(value: KnockoutObservableArray<MenuItem>) {
        this._children = value;
    }
}

class MenuTools {
    static addItem = (parent: MenuItem | Menu): Promise<MenuItem> => {
        return new Promise<MenuItem>((resolve, reject) => {
            let modalNode = document.querySelector('#modal');
            modalNode.innerHTML = document.querySelector('#element-modal').innerHTML;
            let injectedModal = modalNode.firstElementChild;

            let item = new MenuItem({}, parent);
            let modal = Modal.get(injectedModal, false);
            let vm = new MenuItemModalViewModel(modal, item, injectedModal.getAttribute('data-fetch-url'));
            ko.applyBindings(vm, injectedModal);
            modal.on('closed', () => {
                ko.cleanNode(injectedModal);
                modalNode.innerHTML = '<div id="modal" />';
                reject();
            });
            modal.on('save', () => {
                item = vm.item();
                item.route().url(item.displayUrl());
                resolve(item);
            });
            modal.show();
        });
    };

    static editItem = (item: MenuItem): Promise<MenuItem> => {
        return new Promise((resolve, reject) => {
            let modalNode = document.querySelector('#modal');
            modalNode.innerHTML = document.querySelector('#element-modal').innerHTML;
            let injectedModal = modalNode.firstElementChild;

            let modal = Modal.get(injectedModal, false);
            let vm = new MenuItemModalViewModel(modal, item, injectedModal.getAttribute('data-fetch-url'));
            ko.applyBindings(vm, injectedModal);
            modal.on('closed', () => {
                ko.cleanNode(injectedModal);
                modalNode.innerHTML = '<div id="modal" />';
                reject();
            });
            modal.on('save', () => {
                item = vm.item();
                item.route().url(item.displayUrl());
                resolve(item);
            });
            modal.show();
        });
    };

    static deleteItem = (item: MenuItem): Promise<MenuItem> => {
        return new Promise((resolve, reject) => {
            Modal.confirm(texts['designer.menu.editor.item.delete.title'], texts['designer.menu.editor.item.delete.content'].replace('title', item.title()))
                .then(value => value ? resolve(item) : reject())
                .catch(reason => reject());
        });
    };

    static formatJson = (json: any) => {
        let result = '';
        let keys = Object.keys(json);

        for (let i = 0; i < keys.length; i++) {
            result += `${keys[i]} <i class="fa fa-long-arrow-right" aria-hidden="true"></i> ${json[keys[i]]}`;
        }

        return result;
    };
}

class Menu {
    addItemAfter = (selectedItem: MenuItem) => {
        MenuTools.addItem(this).then((item: MenuItem) => {
            let currentItemIndex = this.children.indexOf(selectedItem) + 1;
            if (currentItemIndex === -1) {
                this.children.push(item);
            } else {
                this.children.splice(currentItemIndex, 0, item);
            }
        });
    };
    addItemBefore = (selectedItem: MenuItem) => {
        MenuTools.addItem(this).then((item: MenuItem) => {
            let currentItemIndex = this.children.indexOf(selectedItem);
            this.children.splice(currentItemIndex, 0, item);
        });
    };

    constructor(data: any | string) {
        if (data instanceof String) {
            data = JSON.parse(data.toString());
        }

        this.id(data.id);
        this.name(data.name);

        if (data.children) {
            for (let i = 0; i < data.children.length; i++) {
                let child = data.children[i];
                let menuItem = new MenuItem(child, this);
                this.children.push(menuItem);
            }
        }
    }

    private _name = ko.observable<string>();
    get name(): KnockoutObservable<string> {
        return this._name;
    }

    set name(value: KnockoutObservable<string>) {
        this._name = value;
    }

    private _id = ko.observable<number>();

    get id(): KnockoutObservable<number> {
        return this._id;
    }

    set id(value: KnockoutObservable<number>) {
        this._id = value;
    }

    private _children = ko.observableArray<MenuItem>();

    get children(): KnockoutObservableArray<MenuItem> {
        return this._children;
    }

    set children(value: KnockoutObservableArray<MenuItem>) {
        this._children = value;
    }
}