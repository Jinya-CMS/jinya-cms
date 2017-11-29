class DesignerViewModel {
    static activate = () => {
        let target = document.querySelector('.gallery-images');
        let vm = new DesignerViewModel();
        ko.applyBindings(vm, target);

        vm.sourceUrl = target.getAttribute('data-source');
        vm.load();
    };
    addImage = (item: Gallery.Image) => {
        let template = document.querySelector('#add-modal-template').innerHTML;
        let modal = document.createElement('div');
        modal.innerHTML = template;
        this.body.appendChild(modal);
        let element = document.querySelector('#add-modal');
        let picker = new Modal(element);
        picker.show();

        let position = item ? item.position : this.getHighestPosition();

        let vm = AddArtworkViewModel.init(element, position, this);
        picker.on('closed', () => {
            this.body.removeChild(modal);
        });
    };
    editImage = () => {
        let element = document.querySelector('#edit-modal');
        let picker = new Modal(element);
        picker.show();

        let vm = EditArtworkViewModel.init(element, 0);
        picker.on('closed', () => {

        });
    };
    load = () => {
        let ajax = new Ajax(this.sourceUrl);
        ajax.get().then((data) => {
            this.images(data);
        });
    };
    private getHighestPosition = () => {
        let images = this.images();
        let positions = images.sort((a, b) => {
            return a.position > b.position ? -1 : 1;
        });
    };

    private sourceUrl: string;

    private _images = ko.observableArray<Gallery.Image>();

    get images(): KnockoutObservableArray<Gallery.Image> {
        return this._images;
    }

    set images(value: KnockoutObservableArray<Gallery.Image>) {
        this._images = value;
    }

    private _body = document.querySelector('body');

    get body() {
        return this._body;
    }
}

DesignerViewModel.activate();