class OverviewViewModel {
    static selectedClass: string = 'selected';
    public matchHeight = () => {
        $('.card img').imagesLoaded(() => {
            $('.card').matchHeight();
        });
    };
    public select = (item) => {
        this.selectedItem(item);

        $('body').data(TableElementSelector.selectedIdAttribute, item.id);
        $('.sidebar').removeClass('no-edit');
    };
    private getListUrl: string;
    private loadFailureMessage: string;
    private items = ko.observableArray();
    private more = ko.observable<boolean>(true);
    private loading = ko.observable<boolean>(false);
    private selectedItem = ko.observable({});

    public constructor(getListUrl: string, loadFailureMessage: string) {
        this.getListUrl = getListUrl;
        this.loadFailureMessage = loadFailureMessage;
        this.loadData();
    }

    public loadData() {
        this.loading(true);
        $.getJSON(this.getListUrl).done(() => {
            this.loading(false);
        }).then((data) => {
            this.items(data.data);
            this.more(data.more);
            this.getListUrl = data.moreLink;
        }, () => {
            Messenger().post({
                message: this.loadFailureMessage
            })
        });
    }
}