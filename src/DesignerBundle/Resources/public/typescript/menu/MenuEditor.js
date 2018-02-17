var MenuEditorViewModel = /** @class */ (function () {
    function MenuEditorViewModel(element) {
        var _this = this;
        this.submit = function () {
            var data = _this.stringifyMenu();
            var call = new Ajax.Request(_this.submitUrl);
            var dfd;
            var formData = new FormData();
            formData.append('_menu', data);
            var logoUpload = document.querySelector('[data-logo-upload]');
            if (logoUpload.files.length > 0) {
                formData.append('_logo', logoUpload.files[0]);
            }
            dfd = call.post(formData);
            dfd.then(function (value) {
                location.href = value.redirectTarget;
            }, function (reason) {
                Modal.alert(reason.message, reason.details.message);
            });
        };
        this.addItemAfter = function (selectedItem) {
            _this.menu().addItemAfter(selectedItem);
        };
        this.addItemBefore = function (selectedItem) {
            _this.menu().addItemBefore(selectedItem);
        };
        this.stringifyMenu = function () {
            return JSON.stringify(ko.toJS(_this.menu()), function (key, value) {
                if (key === 'menu' || key === 'parent' || key === '_menu' || key === '_parent') {
                    return value ? value.id : '';
                }
                else {
                    return value;
                }
            });
        };
        this.loadMenu = function (loadUrl) {
            var call = new Ajax.Request(loadUrl);
            call.get().then(function (data) {
                var menu = new Menu(data);
                _this.menu(menu);
            }, function (error) {
                Modal.alert(error.message, error.details.message);
            });
        };
        this._logo = ko.observable();
        this._triggerFileInput = function () {
            var input = document.querySelector('[data-logo-upload]');
            input.addEventListener('change', function (event) {
                _this.logo(input.value);
                var file = input.files[0];
                var fileReader = new FileReader();
                fileReader.readAsDataURL(file);
                fileReader.addEventListener('load', function (data) {
                    _this.menu().logo(fileReader.result);
                });
            });
            input.click();
        };
        this._edit = ko.observable(true);
        this._menu = ko.observable(new Menu('{}'));
        this.submitUrl = element.getAttribute('data-submit-url');
        if (element.hasAttribute('data-menu-id')) {
            this.menuId = parseInt(element.getAttribute('data-menu-id'));
            this.loadMenu(element.getAttribute('data-load-url'));
        }
    }
    Object.defineProperty(MenuEditorViewModel.prototype, "logo", {
        get: function () {
            return this._logo;
        },
        set: function (value) {
            this._logo = value;
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(MenuEditorViewModel.prototype, "triggerFileInput", {
        get: function () {
            return this._triggerFileInput;
        },
        set: function (value) {
            this._triggerFileInput = value;
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(MenuEditorViewModel.prototype, "edit", {
        get: function () {
            return this._edit;
        },
        set: function (value) {
            this._edit = value;
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(MenuEditorViewModel.prototype, "menu", {
        get: function () {
            return this._menu;
        },
        set: function (value) {
            this._menu = value;
        },
        enumerable: true,
        configurable: true
    });
    return MenuEditorViewModel;
}());
var MenuEditor = /** @class */ (function () {
    function MenuEditor() {
    }
    MenuEditor.init = (function () {
        var element = document.querySelector('[data-menu-editor]');
        if (element) {
            var vm = new MenuEditorViewModel(element);
            ko.applyBindings(vm, element);
        }
    })();
    return MenuEditor;
}());
//# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiTWVudUVkaXRvci5qcyIsInNvdXJjZVJvb3QiOiIiLCJzb3VyY2VzIjpbIk1lbnVFZGl0b3IudHMiXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6IkFBQUE7SUFrREksNkJBQVksT0FBZ0I7UUFBNUIsaUJBT0M7UUF4REQsV0FBTSxHQUFHO1lBQ0wsSUFBSSxJQUFJLEdBQUcsS0FBSSxDQUFDLGFBQWEsRUFBRSxDQUFDO1lBQ2hDLElBQUksSUFBSSxHQUFHLElBQUksSUFBSSxDQUFDLE9BQU8sQ0FBQyxLQUFJLENBQUMsU0FBUyxDQUFDLENBQUM7WUFDNUMsSUFBSSxHQUFpQixDQUFDO1lBRXRCLElBQUksUUFBUSxHQUFHLElBQUksUUFBUSxFQUFFLENBQUM7WUFDOUIsUUFBUSxDQUFDLE1BQU0sQ0FBQyxPQUFPLEVBQUUsSUFBSSxDQUFDLENBQUM7WUFFL0IsSUFBSSxVQUFVLEdBQXFCLFFBQVEsQ0FBQyxhQUFhLENBQUMsb0JBQW9CLENBQUMsQ0FBQztZQUVoRixFQUFFLENBQUMsQ0FBQyxVQUFVLENBQUMsS0FBSyxDQUFDLE1BQU0sR0FBRyxDQUFDLENBQUMsQ0FBQyxDQUFDO2dCQUM5QixRQUFRLENBQUMsTUFBTSxDQUFDLE9BQU8sRUFBRSxVQUFVLENBQUMsS0FBSyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUM7WUFDbEQsQ0FBQztZQUVELEdBQUcsR0FBRyxJQUFJLENBQUMsSUFBSSxDQUFDLFFBQVEsQ0FBQyxDQUFDO1lBRTFCLEdBQUcsQ0FBQyxJQUFJLENBQUMsVUFBQSxLQUFLO2dCQUNWLFFBQVEsQ0FBQyxJQUFJLEdBQUcsS0FBSyxDQUFDLGNBQWMsQ0FBQztZQUN6QyxDQUFDLEVBQUUsVUFBQyxNQUFrQjtnQkFDbEIsS0FBSyxDQUFDLEtBQUssQ0FBQyxNQUFNLENBQUMsT0FBTyxFQUFFLE1BQU0sQ0FBQyxPQUFPLENBQUMsT0FBTyxDQUFDLENBQUM7WUFDeEQsQ0FBQyxDQUFDLENBQUM7UUFDUCxDQUFDLENBQUM7UUFDRixpQkFBWSxHQUFHLFVBQUMs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