class Route {
    private routeKey = ko.pureComputed(() => {
        return this.name() + this.parameter();
    });

    constructor(data: any | string) {
        if (data instanceof String) {
            data = JSON.parse(data.toString());
        }

        this.name(data.name);
        this.parameter(data.parameter);
        this.url(data.url);
    }

    private _url = ko.observable<string>();

    get url(): KnockoutObservable<string> {
        return this._url;
    }

    set url(value: KnockoutObservable<string>) {
        this._url = value;
    }

    private _name = ko.observable<string>();

    get name(): KnockoutObservable<string> {
        return this._name;
    }

    set name(value: KnockoutObservable<string>) {
        this._name = value;
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
    addItem = (selectedItem: MenuItem) => {
        MenuTools.addItem().then((item: MenuItem) => {
            let currentItemIndex = this.children.indexOf(selectedItem);
            this.children.splice(currentItemIndex, 0, item);
        });
    };
    addItemBeginning = () => {
        MenuTools.addItem().then((item: MenuItem) => {
            this.children.splice(0, 0, item);
        });
    };
    editItem = (item: MenuItem) => {
        MenuTools.editItem(item).then((item: MenuItem) => {
        });
    };
    deleteItem = (item: MenuItem) => {
        MenuTools.deleteItem(item).then((item: MenuItem) => {
            this.children.remove(item);
        });
    };

    constructor(data: any | string) {
        if (data instanceof String) {
            data = JSON.parse(data.toString());
        }

        let route = new Route(data.route);
        let parent = new MenuItem(data.parent);

        this.id(data.id);
        this.title(data.title);
        this.parent(parent);
        this.route(route);

        if (data.children) {
            for (let i = 0; i < data.children.length; i++) {
                let child = data.children[i];
                let menuItem = new MenuItem(child);
                this.children.push(menuItem);
            }
        }
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

    private _route = ko.observable<Route>();

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
    static addItem = (): Promise<MenuItem> => {
        return new Promise<MenuItem>((resolve, reject) => {
            let modalNode = document.querySelector('#modal');
            modalNode.innerHTML = document.querySelector('#element-modal').innerHTML;
            let injectedModal = modalNode.firstElementChild;

            let item = new MenuItem({});
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

            let item = new MenuItem({});
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
                resolve(item);
            });
            modal.show();
        });
    };

    static deleteItem = (item: MenuItem): Promise<MenuItem> => {
        return new Promise((resolve, reject) => {

        });
    };
}

class Menu {
    addItem = (selectedItem: MenuItem) => {
        MenuTools.addItem().then((item: MenuItem) => {
            let currentItemIndex = this.children.indexOf(selectedItem);
            this.children.splice(currentItemIndex, 0, item);
        });
    };
    addItemBeginning = () => {
        MenuTools.addItem().then((item: MenuItem) => {
            this.children.splice(0, 0, item);
        });
    };

    constructor(data: any | string) {
        if (data instanceof String) {
            data = JSON.parse(data.toString());
        }

        this.id(data.id);
        this.title(data.title);

        if (data.children) {
            for (let i = 0; i < data.children.length; i++) {
                let child = data.children[i];
                let menuItem = new MenuItem(child);
                this.children.push(menuItem);
            }
        }
    }

    private _title = ko.observable<string>();
    get title(): KnockoutObservable<string> {
        return this._title;
    }

    set title(value: KnockoutObservable<string>) {
        this._title = value;
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