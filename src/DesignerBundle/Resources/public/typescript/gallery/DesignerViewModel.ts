class DesignerViewModel {
    static activate = () => {
        let target = document.querySelector('.gallery-images');
        let vm = new DesignerViewModel();
        ko.applyBindings(vm, target);

        let url = target.getAttribute('data-source');
        vm.load(url);
    };
    addImage = () => {
        let picker = new Modal('#add-modal');
        picker.show();
    };
    editImage = () => {
        let picker = new Modal('#edit-modal');
        picker.show();
    };
    load = (url: string) => {
        let ajax = new Ajax(url);
        ajax.get().then((data) => {
            this.images(data);
        });
    };

    private _images = ko.observableArray<Gallery.Image>();

    get images(): KnockoutObservableArray<Gallery.Image> {
        return this._images;
    }

    set images(value: KnockoutObservableArray<Gallery.Image>) {
        this._images = value;
    }
}

DesignerViewModel.activate();