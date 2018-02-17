var MenuTools = /** @class */ (function () {
    function MenuTools() {
    }
    MenuTools.addItem = function (parent) {
        return new Promise(function (resolve, reject) {
            var modalNode = document.querySelector('#modal');
            modalNode.innerHTML = document.querySelector('#element-modal').innerHTML;
            var injectedModal = modalNode.firstElementChild;
            var item = new MenuItem({ highlighted: false }, parent);
            var modal = Modal.get(injectedModal, false);
            var vm = new MenuItemModalViewModel(modal, item, injectedModal.getAttribute('data-fetch-url'));
            ko.applyBindings(vm, injectedModal);
            modal.on('closed', function () {
                ko.cleanNode(injectedModal);
                modalNode.innerHTML = '<div id="modal" />';
                reject();
            });
            modal.on('save', function () {
                item = vm.item();
                item.route().url(item.displayUrl());
                resolve(item);
            });
            modal.show();
        });
    };
    MenuTools.editItem = function (item) {
        return new Promise(function (resolve, reject) {
            var modalNode = document.querySelector('#modal');
            modalNode.innerHTML = document.querySelector('#element-modal').innerHTML;
            var injectedModal = modalNode.firstElementChild;
            var modal = Modal.get(injectedModal, false);
            var vm = new MenuItemModalViewModel(modal, item, injectedModal.getAttribute('data-fetch-url'));
            ko.applyBindings(vm, injectedModal);
            modal.on('closed', function () {
                ko.cleanNode(injectedModal);
                modalNode.innerHTML = '<div id="modal" />';
                reject();
            });
            modal.on('save', function () {
                item = vm.item();
                item.route().url(item.displayUrl());
                resolve(item);
            });
            modal.show();
        });
    };
    MenuTools.deleteItem = function (item) {
        return new Promise(function (resolve, reject) {
            Modal.confirm(texts['designer.menu.editor.item.delete.title'], texts['designer.menu.editor.item.delete.content'].replace('title', item.title()))
                .then(function (value) { return value ? resolve(item) : reject(); })
                .catch(function (reason) { return reject(); });
        });
    };
    MenuTools.formatJson = function (json) {
        var result = '';
        var keys = Object.keys(json);
        for (var i = 0; i < keys.length; i++) {
            result += keys[i] + " <i class=\"fa fa-long-arrow-right\" aria-hidden=\"true\"></i> " + json[keys[i]];
        }
        return result;
    };
    return MenuTools;
}());
var Route = /** @class */ (function () {
    function Route(data) {
        var _this = this;
        this._valid = ko.pureComputed(function () {
            return _this.name() && _this.url();
        });
        this._name = ko.observable();
        this._url = ko.observable();
        this._parameter = ko.observableArray();
        if (data instanceof String) {
            data = JSON.parse(data.toString());
        }
        if (data) {
            this.name(data.name);
            this.parameter(data.parameter);
            this.url(data.url);
        }
    }
    Object.defineProperty(Route.prototype, "valid", {
        get: function () {
            return this._valid;
        },
        set: function (value) {
            this._valid = value;
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(Route.prototype, "name", {
        get: function () {
            return this._name;
        },
        set: function (value) {
            this._name = value;
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(Route.prototype, "url", {
        get: function () {
            return this._url;
        },
        set: function (value) {
            this._url = value;
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(Route.prototype, "parameter", {
        get: function () {
            return this._parameter;
        },
        set: function (value) {
            this._parameter = value;
        },
        enumerable: true,
        configurable: true
    });
    return Route;
}());
var MenuItem = /** @class */ (function () {
    function MenuItem(data, parent) {
        var _this = this;
        this.addItemAfter = function (selectedItem) {
            MenuTools.addItem(_this).then(function (item) {
                var currentItemIndex = _this.children.indexOf(selectedItem) + 1;
                if (currentItemIndex === -1) {
                    _this.children.push(item);
                }
                else {
                    _this.children.splice(currentItemIndex, 0, item);
                }
            });
        };
        this.addItemBefore = function (selectedItem) {
            MenuTools.addItem(_this).then(function (item) {
                var currentItemIndex = _this.children.indexOf(selectedItem);
                _this.children.splice(currentItemIndex, 0, item);
            });
        };
        this.editItem = function (item) {
            MenuTools.editItem(item).then(function (item) {
            });
        };
        this.deleteItem = function (item) {
            MenuTools.deleteItem(item).then(function (item) {
                _this.parent().children.remove(item);
            });
        };
        this._highlighted = ko.observable();
        this._displayUrl = ko.observable();
        this._menu = ko.observable();
        this._pageType = ko.observable();
        this._routes = ko.observableArray();
        this._valid = ko.pureComputed(function () {
            var valid = _this.route() && _this.route().valid() && _this.title();
            for (var i = 0; i < _this.children.length; i++) {
                var child = _this.children()[i];
                valid = valid && child.valid();
            }
            return valid;
        });
        this._id = ko.observable();
        this._title = ko.observable();
        this._route = ko.observable(new Route(null));
        this._parent = ko.observable();
        this._children = ko.observableArray();
        if (data instanceof String) {
            data = JSON.parse(data.toString());
        }
        this.route(new Route(null));
        if (data) {
            var route = new Route(data.route);
            this.id(data.id || Date.now());
            this.title(data.title);
            this.pageType(data.pageType);
            this.displayUrl(data.displayUrl);
            this.highlighted(data.highlighted);
            if (parent instanceof MenuItem) {
                this.parent(parent);
            }
            else if (parent instanceof Menu) {
                this.menu(parent);
            }
            this.route(route);
            if (data.children) {
                for (var i = 0; i < data.children.length; i++) {
                    var child = data.children[i];
                    var menuItem = new MenuItem(child, this);
                    this.children.push(menuItem);
                }
            }
        }
    }
    Object.defineProperty(MenuItem.prototype, "highlighted", {
        get: function () {
            return this._highlighted;
        },
        set: function (value) {
            this._highlighted = value;
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(MenuItem.prototype, "displayUrl", {
        get: function () {
            return this._displayUrl;
        },
        set: function (value) {
            this._displayUrl = value;
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(MenuItem.prototype, "menu", {
        get: function () {
            return this._menu;
        },
        set: function (value) {
            this._menu = value;
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(MenuItem.prototype, "pageType", {
        get: function () {
            return this._pageType;
        },
        set: function (value) {
            this._pageType = value;
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(MenuItem.prototype, "routes", {
        get: function () {
            return this._routes;
        },
        set: function (value) {
            this._routes = value;
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(MenuItem.prototype, "valid", {
        get: function () {
            return this._valid;
        },
        set: function (value) {
            this._valid = value;
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(MenuItem.prototype, "id", {
        get: function () {
            return this._id;
        },
        set: function (value) {
            this._id = value;
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(MenuItem.prototype, "title", {
        get: function () {
            return this._title;
        },
        set: function (value) {
            this._title = value;
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(MenuItem.prototype, "route", {
        get: function () {
            return this._route;
        },
        set: function (value) {
            this._route = value;
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(MenuItem.prototype, "parent", {
        get: function () {
            return this._parent;
        },
        set: function (value) {
            this._parent = value;
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(MenuItem.prototype, "children", {
        get: function () {
            return this._children;
        },
        set: function (value) {
            this._children = value;
        },
        enumerable: true,
        configurable: true
    });
    return MenuItem;
}());
var Menu = /** @class */ (function () {
    function Menu(data) {
        var _this = this;
        this.addItemAfter = function (selectedItem) {
            MenuTools.addItem(_this).then(function (item) {
                var currentItemIndex = _this.children.indexOf(selectedItem) + 1;
                if (currentItemIndex === -1) {
                    _this.children.push(item);
                }
                else {
                    _this.children.splice(currentItemIndex, 0, item);
                }
            });
        };
        this.addItemBefore = function (selectedItem) {
            MenuTools.addItem(_this).then(function (item) {
                var currentItemIndex = _this.children.indexOf(selectedItem);
                _this.children.splice(currentItemIndex, 0, item);
            });
        };
        this._logo = ko.observable();
        this._name = ko.observable();
        this._id = ko.observable();
        this._children = ko.observableArray();
        if (data instanceof String) {
            data = JSON.parse(data.toString());
        }
        this.id(data.id);
        this.name(data.name);
        this.logo(data.logo);
        if (data.children) {
            for (var i = 0; i < data.children.length; i++) {
                var child = data.children[i];
                var menuItem = new MenuItem(child, this);
                this.children.push(menuItem);
            }
        }
    }
    Object.defineProperty(Menu.prototype, "logo", {
        get: function () {
            return this._logo;
        },
        set: function (value) {
            this._logo = value;
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(Menu.prototype, "name", {
        get: function () {
            return this._name;
        },
        set: function (value) {
            this._name = value;
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(Menu.prototype, "id", {
        get: function () {
            return this._id;
        },
        set: function (value) {
            this._id = value;
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(Menu.prototype, "children", {
        get: function () {
            return this._children;
        },
        set: function (value) {
            this._children = value;
        },
        enumerable: true,
        configurable: true
    });
    return Menu;
}());
//# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiTWVudS5qcyIsInNvdXJjZVJvb3QiOiIiLCJzb3VyY2VzIjpbIk1lbnUudHMiXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6IkFBQUE7SUFBQTtJQWtFQSxDQUFDO0lBakVVLGlCQUFPLEdBQUcsVUFBQyxNQUF1QjtRQUNyQyxNQUFNLENBQUMsSUFBSSxPQUFPLENBQVcsVUFBQyxPQUFPLEVBQUUsTUFBTTtZQUN6QyxJQUFJLFNBQVMsR0FBRyxRQUFRLENBQUMsYUFBYSxDQUFDLFFBQVEsQ0FBQyxDQUFDO1lBQ2pELFNBQVMsQ0FBQyxTQUFTLEdBQUcsUUFBUSxDQUFDLGFBQWEsQ0FBQyxnQkFBZ0IsQ0FBQyxDQUFDLFNBQVMsQ0FBQztZQUN6RSxJQUFJLGFBQWEsR0FBRyxTQUFTLENBQUMsaUJBQWlCLENBQUM7WUFFaEQsSUFBSSxJQUFJLEdBQUcsSUFBSSxRQUFRLENBQUMsRUFBQyxXQUFXLEVBQUUsS0FBSyxFQUFDLEVBQUUsTUFBTSxDQUFDLENBQUM7WUFDdEQsSUFBSSxLQUFLLEdBQUcsS0FBSyxDQUFDLEdBQUcsQ0FBQyxhQUFhLEVBQUUsS0FBSyxDQUFDLENBQUM7WUFDNUMsSUFBSSxFQUFFLEdBQUcsSUFBSSxzQkFBc0IsQ0FBQyxLQUFLLEVBQUUsSUFBSSxFQUFFLGFBQWEsQ0FBQyxZQUFZLENBQUMsZ0JBQWdCLENBQUMsQ0FBQyxDQUFDO1lBQy9GLEVBQUUsQ0FBQyxhQUFhLENBQUMsRUFBRSxFQUFFLGFBQWEsQ0FBQyxDQUFDO1lBQ3BDLEtBQUssQ0FBQyxFQUFFLENBQUMsUUFBUSxFQUFFO2dCQUNmLEVBQUUsQ0FBQyxTQUFTLENBQUMsYUFBYSxDQUFDLENBQUM7Z0JBQzVCLFNBQVMsQ0FBQyxTQUFTLEdBQUcsb0JBQW9CLENBQUM7Z0JBQzNDLE1BQU0sRUFBRSxDQUFDO1lBQ2IsQ0FBQyxDQUFDLENBQUM7WUFDSCxLQUFLLENBQUMsRUFBRSxDQUFDLE1BQU0sRUFBRTtnQkFDYixJQUFJLEdBQUcsRUFBRSxDQUFDLElBQUksRUFBRSxDQUFDO2dCQUNq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