class EditArtworkViewModel extends ArtworkModificationViewModel {
    static init = (element: Element, position: number) => {
        let vm = new EditArtworkViewModel(element, position);
        ko.applyBindings(vm, element);
        vm.sourceUrl = element.getAttribute('data-source-url');
        vm.load();

        return vm;
    };

    constructor(element: Element, position: number) {
        super();
        this.element = element;
        this.position = position;
    }
}