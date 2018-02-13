class MenuEditorViewModel {
    submit = () => {
        let data = this.stringifyMenu();
        let call = new Ajax.Request(this.submitUrl);
        let dfd: Promise<any>;

        let formData = new FormData();
        formData.append('_menu', data);

        let logoUpload: HTMLInputElement = document.querySelector('[data-logo-upload]');

        if (logoUpload.files.length > 0) {
            formData.append('_logo', logoUpload.files[0]);
        }

        dfd = call.post(formData);

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
    private stringifyMenu = () => {
        return JSON.stringify(ko.toJS(this.menu()), (key, value) => {
            if (key === 'menu' || key === 'parent' || key === '_menu' || key === '_parent') {
                return value ? value.id : '';
            } else {
                return value;
            }
        });
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

    private _logo = ko.observable<string>();

    get logo(): KnockoutObservable<string> {
        return this._logo;
    }

    set logo(value: KnockoutObservable<string>) {
        this._logo = value;
    }

    private _triggerFileInput = () => {
        let input: HTMLInputElement = document.querySelector('[data-logo-upload]');
        input.addEventListener('change', (event: Event) => {
            this.logo(input.value);
            let file = input.files[0];
            let fileReader = new FileReader();
            fileReader.readAsDataURL(file);
            fileReader.addEventListener('load', (data) => {
                this.menu().logo(fileReader.result);
            });
        });
        input.click();
    };

    get triggerFileInput(): () => void {
        return this._triggerFileInput;
    }

    set triggerFileInput(value: () => void) {
        this._triggerFileInput = value;
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
    private static init = (() => {
        let element = document.querySelector('[data-menu-editor]');
        if (element) {
            let vm = new MenuEditorViewModel(element);
            ko.applyBindings(vm, element);
        }
    })();
}