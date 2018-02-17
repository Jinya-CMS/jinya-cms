var OverviewViewModel = /** @class */ (function () {
    function OverviewViewModel(getListUrl, loadFailureMessage) {
        var _this = this;
        this.matchHeight = function () {
            $('.card img').imagesLoaded(function (data) {
                $('.card').matchHeight();
            });
        };
        this.select = function (item) {
            _this.selectedItem(item);
            $('body').data(TableElementSelector.selectedIdAttribute, item.id);
            $('.sidebar').removeClass('no-edit');
        };
        this.loadData = function () {
            if (_this.more() && !_this.loading()) {
                if (_this.xhr) {
                    _this.xhr.abort();
                }
                _this.loading(true);
                _this.xhr = $.getJSON(_this.getListUrl, {
                    keyword: _this.search(),
                    label: _this.label()
                });
                _this.xhr.done(function () {
                    _this.loading(false);
                }).then(function (data) {
                    ko.utils.arrayForEach(data.data, function (entry) {
                        _this.items.push(entry);
                    });
                    _this.more(data.more);
                    _this.getListUrl = data.moreLink;
                    window.history.pushState([], $('title').text(), location.origin + location.pathname + '?' + $.param({
                        keyword: _this.search(),
                        label: _this.label()
                    }));
                    $('[data-label]').removeClass('active');
                    $("[data-label=" + _this.label()).addClass('active');
                }, function (data) {
                    if (data.readyState != 0) {
                        Messenger().post({
                            message: _this.loadFailureMessage,
                            type: 'error'
                        });
                    }
                });
            }
        };
        this.scrolled = function (data, event) {
            var elem = event.target;
            if (elem.scrollTop > (elem.scrollHeight - elem.offsetHeight - 200)) {
                _this.loadData();
            }
        };
        this.search = ko.observable('');
        this.label = ko.observable(null);
        this.originalGetListUrl = '';
        this.searchTriggered = function (newValue) {
            if (!newValue || newValue.length >= 3) {
                _this.items([]);
                _this.getListUrl = _this.originalGetListUrl;
                _this.more(true);
                _this.loading(false);
                _this.loadData();
            }
        };
        this.labelTriggered = function (newValue) {
            _this.items([]);
            _this.getListUrl = _this.originalGetListUrl;
            _this.more(true);
            _this.loading(false);
            _this.loadData();
        };
        this.$container = $('.overview-list');
        this.items = ko.observableArray();
        this.more = ko.observable(true);
        this.loading = ko.observable(false);
        this.selectedItem = ko.observable({});
        this.getListUrl = this.originalGetListUrl = getListUrl;
        this.loadFailureMessage = loadFailureMessage;
        this.loadData();
        this.search.subscribe(this.searchTriggered);
        this.label.subscribe(this.labelTriggered);
        OverviewViewModel.CurrentVm = this;
    }
    OverviewViewModel.selectedClass = 'selected';
    OverviewViewModel.CurrentVm = null;
    return OverviewViewModel;
}());
//# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiT3ZlcnZpZXdWaWV3TW9kZWwuanMiLCJzb3VyY2VSb290IjoiIiwic291cmNlcyI6WyJPdmVydmlld1ZpZXdNb2RlbC50cyJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiQUFFQTtJQW1GSSwyQkFBbUIsVUFBa0IsRUFBRSxrQkFBMEI7UUFBakUsaUJBT0M7UUF2Rk0sZ0JBQVcsR0FBRztZQUNqQixDQUFDLENBQUMsV0FBVyxDQUFDLENBQUMsWUFBWSxDQUFDLFVBQUMsSUFBSTtnQkFDN0IsQ0FBQyxDQUFDLE9BQU8sQ0FBQyxDQUFDLFdBQVcsRUFBRSxDQUFDO1lBQzdCLENBQUMsQ0FBQyxDQUFDO1FBQ1AsQ0FBQyxDQUFDO1FBQ0ssV0FBTSxHQUFHLFVBQUMsSUFBSTtZQUNqQixLQUFJLENBQUMsWUFBWSxDQUFDLElBQUksQ0FBQyxDQUFDO1lBRXhCLENBQUMsQ0FBQyxNQUFNLENBQUMsQ0FBQyxJQUFJLENBQUMsb0JBQW9CLENBQUMsbUJBQW1CLEVBQUUsSUFBSSxDQUFDLEVBQUUsQ0FBQyxDQUFDO1lBQ2xFLENBQUMsQ0FBQyxVQUFVLENBQUMsQ0FBQyxXQUFXLENBQUMsU0FBUyxDQUFDLENBQUM7UUFDekMsQ0FBQyxDQUFDO1FBQ0ssYUFBUSxHQUFHO1lBQ2QsRUFBRSxDQUFDLENBQUMsS0FBSSxDQUFDLElBQUksRUFBRSxJQUFJLENBQUMsS0FBSSxDQUFDLE9BQU8sRUFBRSxDQUFDLENBQUMsQ0FBQztnQkFDakMsRUFBRSxDQUFDLENBQUMsS0FBSSxDQUFDLEdBQUcsQ0FBQyxDQUFDLENBQUM7b0JBQ1gsS0FBSSxDQUFDLEdBQUcsQ0FBQyxLQUFLLEVBQUUsQ0FBQztnQkFDckIsQ0FBQztnQkFDRCxLQUFJLENBQUMsT0FBTyxDQUFDLElBQUksQ0FBQyxDQUFDO2dCQUNuQixLQUFJLENBQUMsR0FBRyxHQUFHLENBQUMsQ0FBQyxPQUFPLENBQUMsS0FBSSxDQUFDLFVBQVUsRUFBRTtvQkFDbEMsT0FBTyxFQUFFLEtBQUksQ0FBQyxNQUFNLEVBQUU7b0JBQ3RC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