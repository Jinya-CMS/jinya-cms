class EditArtworkViewModel extends ArtworkModificationViewModel {
    static init = (element: Element, id: number, parentVm: DesignerViewModel, initialSource: string) => {
        let vm = new EditArtworkViewModel(element, id, parentVm, initialSource);
        ko.applyBindings(vm, element);
        vm.load();

        return vm;
    };
    save = () => {
        let selectedItem = this.selectedItem();
        let slug = selectedItem.slug;
        let saveUrl = `${this.saveUrl.replace('%23tempslug%23', slug)}?id=${this.id}`;
        let ajax = new Ajax.Request(saveUrl);
        ajax.put({}).then(() => {
            let modal = Modal.get(this.element);
            modal.hide();

            this.parentVm.load();
        }, (data) => {
            alert(data.message);
        });
    };
    deleteImage = () => {
        let deleteUrl = this.deleteUrl.replace('%23tempid%23', this.id.toString());
        let ajax = new Ajax.Request(deleteUrl);
        ajax.delete().then(() => {
            let modal = Modal.get(this.element);
            modal.hide();

            this.parentVm.load();
        }, (data) => {
            alert(data.message);
        });
    };
    private saveUrl: string;
    private deleteUrl: string;
    private parentVm: DesignerViewModel;
    private id: number;

    constructor(element: Element, id: number, parentVm: DesignerViewModel, initialSource: string) {
        super();
        this.element = element;
        this.id = id;
        this.saveUrl = element.getAttribute('data-save-url');
        this.deleteUrl = element.getAttribute('data-delete-url');
        this.originalUrl = this.sourceUrl = element.getAttribute('data-source-url');
        this.parentVm = parentVm;
        this.initialSource(initialSource);
    }

    private _initialSource = ko.observable<string>();

    get initialSource(): KnockoutObservable<string> {
        return this._initialSource;
    }

    set initialSource(value: KnockoutObservable<string>) {
        this._initialSource = value;
    }
}