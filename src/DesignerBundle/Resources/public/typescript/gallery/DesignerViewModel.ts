enum Direction {
    RIGHT,
    LEFT
}

class DesignerViewModel {
    private static activate = (() => {
        let target = document.querySelector('.gallery');
        let vm = new DesignerViewModel();
        ko.applyBindings(vm, target);

        vm._sourceUrl = target.getAttribute('data-source');
        vm._moveUrl = target.getAttribute('data-move');
        vm.load();
    })();
    moveRight = (item: Gallery.Image) => {
        this.moveImage(item, Direction.RIGHT);
    };
    moveLeft = (item: Gallery.Image) => {
        this.moveImage(item, Direction.LEFT);
    };
    moveImage = (item: Gallery.Image, direction: Direction) => {
        let items = this.images();
        let filteredItems = items.filter((value, index, array) => {
            return value.id === item.id;
        });
        let selectedItemIdx = items.indexOf(filteredItems[0]);
        let newPosition = 0;
        if (direction === Direction.LEFT) {
            newPosition = items[selectedItemIdx - 1].position - 1;
        } else {
            newPosition = items[selectedItemIdx + 1].position + 1;
        }
        let moveUrl = `${this.moveUrl.replace('%23tempid%23', item.id)}?newPosition=${newPosition}`;

        let request = new Ajax.Request(moveUrl);
        request.put({}).then(() => {
            this.load();
        }, () => {
            Modal.alert(texts['designer.gallery.move.error.title'], texts['designer.gallery.move.error.body']);
        });
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
    editImage = (item) => {
        let template = document.querySelector('#edit-modal-template').innerHTML;
        let modal = document.createElement('div');
        modal.innerHTML = template;
        this.body.appendChild(modal);

        let element = document.querySelector('#edit-modal');
        let picker = new Modal(element);
        picker.show();

        let vm = EditArtworkViewModel.init(element, item.id, this, item.source);
        picker.on('closed', () => {
            this.body.removeChild(modal);
        });
    };
    load = () => {
        let ajax = new Ajax.Request(this._sourceUrl);
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

    private _moveUrl: string;

    get moveUrl(): string {
        return this._moveUrl;
    }

    set moveUrl(value: string) {
        this._moveUrl = value;
    }

    private _sourceUrl: string;

    get sourceUrl(): string {
        return this._sourceUrl;
    }

    set sourceUrl(value: string) {
        this._sourceUrl = value;
    }

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