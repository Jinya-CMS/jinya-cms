var FormEditorViewModel = /** @class */ (function () {
    function FormEditorViewModel(element) {
        var _this = this;
        this.addItem = function () {
            var modalNode = document.querySelector('#modal');
            modalNode.innerHTML = document.querySelector('#add-modal').innerHTML;
            var injectedModal = modalNode.firstElementChild;
            var item = new FormItem();
            var modal = Modal.get(injectedModal, false);
            var vm = new FormItemModalViewModel(modal, item);
            ko.applyBindings(vm, injectedModal);
            modal.on('closed', function () {
                ko.cleanNode(injectedModal);
                modalNode.innerHTML = '<div id="modal" />';
            });
            modal.on('save', function () {
                item = vm.item();
                _this.items.push(item);
            });
            modal.show();
        };
        this.editItem = function (item) {
            var modalNode = document.querySelector('#modal');
            modalNode.innerHTML = document.querySelector('#add-modal').innerHTML;
            var injectedModal = modalNode.firstElementChild;
            var modal = Modal.get(injectedModal, false);
            var vm = new FormItemModalViewModel(modal, item);
            ko.applyBindings(vm, injectedModal);
            modal.on('closed', function () {
                ko.cleanNode(injectedModal);
                modalNode.innerHTML = '<div id="modal" />';
            });
            modal.on('save', function () {
                item = vm.item();
            });
            modal.show();
        };
        this.submit = function () {
            var call = new Ajax.Request(_this.submitUrl);
            var dfd = null;
            var data = ko.toJS(_this);
            for (var i = 0; i < data.items.length; i++) {
                data.items[i].options = {
                    required: data.items[i].required || false
                };
                if (data.items[i].selectOptions) {
                    data.items[i].options.choices = data.items[i].selectOptions.split(/\r?\n/g);
                }
                delete data.items[i].selectOptions;
                delete data.items[i].required;
            }
            if (_this.formId) {
                dfd = call.put(data);
            }
            else {
                dfd = call.post(data);
            }
            dfd.then(function (data) {
                location.href = data.redirectTarget;
            });
        };
        this.loadForm = function (loadUrl) {
            var call = new Ajax.Request(loadUrl);
            call.get().then(function (data) {
                for (var i = 0; i < data.items.length; i++) {
                    var item = data.items[i];
                    var formItem = new FormItem();
                    formItem.options(item.options);
                    formItem.helpText(item.helpText);
                    formItem.label(item.label);
                    formItem.required(item.required);
                    formItem.selectOptions(item.selectOptions);
                    formItem.type(item.type);
                    _this.items.push(formItem);
                }
                _this.slug(data.slug);
                _this.title(data.title);
                _this.description(data.description);
                _this.toAddress(data.toAddress);
            }, function (error) {
                Modal.alert(error.message, error.details.message);
            });
        };
        this._slug = ko.observable();
        this._title = ko.observable();
        this._toAddress = ko.observable();
        this._description = ko.observable();
        this._items = ko.observableArray();
        this.submitUrl = element.getAttribute('data-submit-url');
        if (element.hasAttribute('data-form-id')) {
            this.formId = parseInt(element.getAttribute('data-form-id'));
            this.loadForm(element.getAttribute('data-load-url'));
        }
    }
    Object.defineProperty(FormEditorViewModel.prototype, "slug", {
        get: function () {
            return this._slug;
        },
        set: function (value) {
            this._slug = value;
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(FormEditorViewModel.prototype, "title", {
        get: function () {
            return this._title;
        },
        set: function (value) {
            this._title = value;
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(FormEditorViewModel.prototype, "toAddress", {
        get: function () {
            return this._toAddress;
        },
        set: function (value) {
            this._toAddress = value;
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(FormEditorViewModel.prototype, "description", {
        get: function () {
            return this._description;
        },
        set: function (value) {
            this._description = value;
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(FormEditorViewModel.prototype, "items", {
        get: function () {
            return this._items;
        },
        set: function (value) {
            this._items = value;
        },
        enumerable: true,
        configurable: true
    });
    return FormEditorViewModel;
}());
var FormEditor = /** @class */ (function () {
    function FormEditor() {
    }
    FormEditor.init = (function () {
        var editor = document.querySelector('[data-form-editor]');
        if (editor) {
            var vm = new FormEditorViewModel(editor);
            ko.applyBindings(vm, editor);
        }
    })();
    return FormEditor;
}());
//# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiRm9ybUVkaXRvci5qcyIsInNvdXJjZVJvb3QiOiIiLCJzb3VyY2VzIjpbIkZvcm1FZGl0b3IudHMiXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6IkFBQUE7SUFzRkksNkJBQVksT0FBZ0I7UUFBNUIsaUJBT0M7UUE1RkQsWUFBTyxHQUFHO1lBQ04sSUFBSSxTQUFTLEdBQUcsUUFBUSxDQUFDLGFBQWEsQ0FBQyxRQUFRLENBQUMsQ0FBQztZQUNqRCxTQUFTLENBQUMsU0FBUyxHQUFHLFFBQVEsQ0FBQyxhQUFhLENBQUMsWUFBWSxDQUFDLENBQUMsU0FBUyxDQUFDO1lBQ3JFLElBQUksYUFBYSxHQUFHLFNBQVMsQ0FBQyxpQkFBaUIsQ0FBQztZQUVoRCxJQUFJLElBQUksR0FBRyxJQUFJLFFBQVEsRUFBRSxDQUFDO1lBQzFCLElBQUksS0FBSyxHQUFHLEtBQUssQ0FBQyxHQUFHLENBQUMsYUFBYSxFQUFFLEtBQUssQ0FBQyxDQUFDO1lBQzVDLElBQUksRUFBRSxHQUFHLElBQUksc0JBQXNCLENBQUMsS0FBSyxFQUFFLElBQUksQ0FBQyxDQUFDO1lBQ2pELEVBQUUsQ0FBQyxhQUFhLENBQUMsRUFBRSxFQUFFLGFBQWEsQ0FBQyxDQUFDO1lBQ3BDLEtBQUssQ0FBQyxFQUFFLENBQUMsUUFBUSxFQUFFO2dCQUNmLEVBQUUsQ0FBQyxTQUFTLENBQUMsYUFBYSxDQUFDLENBQUM7Z0JBQzVCLFNBQVMsQ0FBQyxTQUFTLEdBQUcsb0JBQW9CLENBQUM7WUFDL0MsQ0FBQyxDQUFDLENBQUM7WUFDSCxLQUFLLENBQUMsRUFBRSxDQUFDLE1BQU0sRUFBRTtnQkFDYixJQUFJLEdBQUcsRUFBRSxDQUFDLElBQUksRUFBRSxDQUFDO2dCQUNqQixLQUFJLENBQUMsS0FBSyxDQUFDLElBQUksQ0FBQyxJQUFJLENBQUMsQ0FBQztZQUMxQixDQUFDLENBQUMsQ0FBQztZQUNILEtBQUssQ0FBQyxJQUFJLEVBQUUsQ0FBQztRQUNqQixDQUFDLENBQUM7UUFDRixhQUFRLEdBQUcsVUFBQyxJQUFj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