import jqXHR = JQuery.jqXHR;

class OverviewViewModel {
    static selectedClass: string = 'selected';
    public static CurrentVm: OverviewViewModel = null;
    public matchHeight = () => {
        $('.card img').imagesLoaded((data) => {
            let scrollPosition = this.$container.scrollTop();
            $('.card').matchHeight();
            this.$container.scrollTop(scrollPosition);
        });
    };
    public select = (item) => {
        this.selectedItem(item);

        $('body').data(TableElementSelector.selectedIdAttribute, item.id);
        $('.sidebar').removeClass('no-edit');
    };
    public loadData = () => {
        if (this.more() && !this.loading()) {
            if (this.xhr) {
                this.xhr.abort();
            }
            this.loading(true);
            this.xhr = $.getJSON(this.getListUrl, {
                keyword: this.search(),
                label: this.label()
            });

            this.xhr.done(() => {
                this.loading(false);
            }).then((data) => {
                ko.utils.arrayForEach(data.data, (entry) => {
                    this.items.push(entry);
                });
                this.more(data.more);
                this.getListUrl = data.moreLink;
                window.history.pushState([], $('title').text(), location.origin + location.pathname + '?' + $.param({
                    keyword: this.search(),
                    label: this.label()
                }));
                $('[data-label]').removeClass('active');
                $(`[data-label=${this.label()}`).addClass('active');
            }, (data) => {
                if (data.readyState != 0) {
                    Messenger().post({
                        message: this.loadFailureMessage,
                        type: 'error'
                    })
                }
            });
        }
    };
    public scrolled = (data, event) => {
        let elem = event.target;
        if (elem.scrollTop > (elem.scrollHeight - elem.offsetHeight - 200)) {
            this.loadData();
        }
    };
    public search = ko.observable('');
    public label = ko.observable(null);
    private xhr: jqXHR;
    private originalGetListUrl = '';
    private searchTriggered = (newValue: string) => {
        if (!newValue || newValue.length >= 3) {
            this.items([]);
            this.getListUrl = this.originalGetListUrl;
            this.more(true);
            this.loading(false);
            this.loadData();
        }
    };
    private labelTriggered = (newValue: string) => {
        this.items([]);
        this.getListUrl = this.originalGetListUrl;
        this.more(true);
        this.loading(false);
        this.loadData();
    };
    private $container = $('.overview-list');
    private getListUrl: string;
    private loadFailureMessage: string;
    private items = ko.observableArray();
    private more = ko.observable<boolean>(true);
    private loading = ko.observable<boolean>(false);
    private selectedItem = ko.observable({});

    public constructor(getListUrl: string, loadFailureMessage: string) {
        this.getListUrl = this.originalGetListUrl = getListUrl;
        this.loadFailureMessage = loadFailureMessage;
        this.loadData();
        this.search.subscribe(this.searchTriggered);
        this.label.subscribe(this.labelTriggered);
        OverviewViewModel.CurrentVm = this;
    }
}