class FormItemModalViewModel {
    save = () => {
        this.modal.trigger('save');
        this.modal.hide();
    };
    private modal: Modal;

    constructor(modal: Modal, formItem: FormItem) {
        this.item(formItem);
        this.modal = modal;
    }

    private _item = ko.observable<FormItem>();

    get item(): KnockoutObservable<FormItem> {
        return this._item;
    }

    set item(value: KnockoutObservable<FormItem>) {
        this._item = value;
    }
}