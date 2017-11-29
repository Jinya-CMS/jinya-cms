abstract class ArtworkModificationViewModel {
    public select = (item: Gallery.Image) => {
        this.selectedItem(item);
    };
    public scrolled = (data, event) => {
        let elem = event.target;
        if (elem.scrollTop > (elem.scrollHeight - elem.offsetHeight - 200)) {
            this.load();
        }
    };
    protected load = () => {
        if (this.originalUrl === this.sourceUrl) {
            this.artworks([]);
        }
        if (this.more) {
            this.more = false;
            let ajax = new Ajax(this.sourceUrl);
            ajax.get().then((data) => {
                ko.utils.arrayPushAll(this.artworks, data.data);
                this.sourceUrl = data.moreLink;
                this.more = data.more;
            }, (data) => {
                this.error(data.message);
            });
        }
    };
    private more = true;

    private _originalUrl: string;

    get originalUrl(): string {
        return this._originalUrl;
    }

    set originalUrl(value: string) {
        this._originalUrl = value;
    }

    private _selectedItem = ko.observable<Gallery.Image>(new Gallery.Image());

    get selectedItem(): KnockoutObservable<Gallery.Image> {
        return this._selectedItem;
    }

    set selectedItem(value: KnockoutObservable<Gallery.Image>) {
        this._selectedItem = value;
    }

    private _element: Element;

    get element(): Element {
        return this._element;
    }

    set element(value: Element) {
        this._element = value;
    }

    private _artworks = ko.observableArray<Gallery.Image>([]);

    get artworks(): KnockoutObservableArray<Gallery.Image> {
        return this._artworks;
    }

    set artworks(value: KnockoutObservableArray<Gallery.Image>) {
        this._artworks = value;
    }

    private _error = ko.observable<string>();

    get error(): KnockoutObservable<string> {
        return this._error;
    }

    set error(value: KnockoutObservable<string>) {
        this._error = value;
    }

    private _position: number;

    get position(): number {
        return this._position;
    }

    set position(value: number) {
        this._position = value;
    }

    private _sourceUrl: string;

    protected get sourceUrl(): string {
        return this._sourceUrl;
    }

    protected set sourceUrl(value: string) {
        this._sourceUrl = value;
    }
}