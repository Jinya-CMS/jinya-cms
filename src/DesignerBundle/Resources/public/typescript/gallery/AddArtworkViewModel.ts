class AddArtworkViewModel extends ArtworkModificationViewModel {
    static init = (element: Element, position: number, parentVm: DesignerViewModel) => {
        let vm = new AddArtworkViewModel(element, position, parentVm);
        ko.applyBindings(vm, element);
        vm.load();

        return vm;
    };

    save = () => {
        let selectedItem = this.selectedItem();
        let slug = selectedItem.slug;
        let saveUrl = `${this.saveUrl.replace('%23tempslug%23', slug)}?position=${this.position}`;
        let ajax = new Ajax.Request(saveUrl);
        ajax.post({}).then(() => {
            let modal = Modal.get(this.element);
            modal.hide();

            this.parentVm.load();
        }, (data: Ajax.Error) => {
            Modal.alert(data.message, data.details.message);
        });
    };

    private saveUrl: string;
    private parentVm: DesignerViewModel;

    constructor(element: Element, position: number, parentVm: DesignerViewModel) {
        super();
        this.element = element;
        this.position = position;
        this.saveUrl = element.getAttribute('data-save-url');
        this.originalUrl = this.sourceUrl = element.getAttribute('data-source-url');
        this.parentVm = parentVm;
    }
}