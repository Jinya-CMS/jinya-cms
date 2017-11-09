class OverviewViewModel {
    static selectedClass: string = 'selected';
    public matchHeight = () => {
        $('.card img').imagesLoaded(() => {
            $('.card').matchHeight();
        });
    };
    public select = (gallery) => {
        let $this = $(event.srcElement);
        this.selectedGallery(gallery);

        $('body').data(TableElementSelector.selectedIdAttribute, gallery.id);
        $('.sidebar').removeClass('no-edit');
    };
    private getListUrl: string;
    private loadFailureMessage: string;
    private galleries = ko.observableArray();
    private more = ko.observable<boolean>(true);
    private loading = ko.observable<boolean>(false);
    private selectedGallery = ko.observable({});

    public constructor(getListUrl: string, loadFailureMessage: string) {
        this.getListUrl = getListUrl;
        this.loadData();
    }

    public loadData() {
        this.loading(true);
        $.getJSON(this.getListUrl).done(() => {
            this.loading(false);
        }).then((data) => {
            this.galleries(data.data);
            this.more(data.more);
            this.getListUrl = data.moreLink;
        }, () => {
            Messenger().post({
                message: this.loadFailureMessage
            })
        });
    }
}