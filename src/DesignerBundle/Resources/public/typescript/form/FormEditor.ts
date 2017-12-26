class FormEditorViewModel {
    addItem = () => {
        let modalNode = document.querySelector('#modal');
        modalNode.innerHTML = document.querySelector('#add-modal').innerHTML;
        let injectedModal = modalNode.firstElementChild;

        let item = new FormItem();
        let modal = Modal.get(injectedModal, false);
        let vm = new FormItemModalViewModel(modal, item);
        ko.applyBindings(vm, injectedModal);
        modal.on('closed', () => {
            ko.cleanNode(injectedModal);
            modalNode.innerHTML = '<div id="modal" />';
        });
        modal.on('save', () => {
            item = vm.item();
            this.items.push(item);
        });
        modal.show();
    };
    submit = () => {
        let call = new Ajax.Request(this.submitUrl);
        let dfd: Promise<any> = null;
        let data = ko.toJS(this);
        for (let i = 0; i < data.items.length; i++) {
            data.items[i].options = {
                required: data.required || false
            };
            if (data.items[i].selectOptions) {
                data.items[i].options.choices = data.items[i].selectOptions.split(/\r\n/g);
            }
            delete data.items[i].selectOptions;
            delete data.items[i].required;
        }

        if (this.formId) {
            dfd = call.put(data);
        } else {
            dfd = call.post(data);
        }
        dfd.then((data) => {
            location.href = data.redirectTarget;
        });
    };
    private formId: Number;
    private submitUrl: string;
    private loadForm = (loadUrl: string) => {
        let call = new Ajax.Request(loadUrl);
        call.get().then((data) => {
            let formItems: FormItem[] = data.items;
            this.items(formItems);
            this.slug(data.slug);
            this.title(data.title);
            this.description(data.description);
        }, (error) => {
            Modal.alert(error.message, error.details.message);
        });
    };

    constructor(element: Element) {
        this.submitUrl = element.getAttribute('data-submit-url');

        if (element.hasAttribute('data-form-id')) {
            this.formId = parseInt(element.getAttribute('data-form-id'));
            this.loadForm(element.getAttribute('data-load-url'));
        }
    }

    private _slug = ko.observable<string>();

    get slug(): KnockoutObservable<string> {
        return this._slug;
    }

    set slug(value: KnockoutObservable<string>) {
        this._slug = value;
    }

    private _title = ko.observable<string>();

    get title(): KnockoutObservable<string> {
        return this._title;
    }

    set title(value: KnockoutObservable<string>) {
        this._title = value;
    }

    private _toAddress = ko.observable<string>();

    get toAddress(): KnockoutObservable<string> {
        return this._toAddress;
    }

    set toAddress(value: KnockoutObservable<string>) {
        this._toAddress = value;
    }

    private _description = ko.observable<string>();

    get description(): KnockoutObservable<string> {
        return this._description;
    }

    set description(value: KnockoutObservable<string>) {
        this._description = value;
    }

    private _items = ko.observableArray<FormItem>();

    get items(): KnockoutObservableArray<FormItem> {
        return this._items;
    }

    set items(value: KnockoutObservableArray<FormItem>) {
        this._items = value;
    }
}

class FormEditor {
    static init = (() => {
        let editor = document.querySelector('[data-form-editor]');
        let vm = new FormEditorViewModel(editor);
        ko.applyBindings(vm, editor);
    })();
}