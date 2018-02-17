var ArtworkModificationViewModel = /** @class */ (function () {
    function ArtworkModificationViewModel() {
        var _this = this;
        this.select = function (item) {
            _this.selectedItem(item);
        };
        this.scrolled = function (data, event) {
            var elem = event.target;
            if (elem.scrollTop > (elem.scrollHeight - elem.offsetHeight - 200)) {
                _this.load();
            }
        };
        this.load = function () {
            if (_this.originalUrl === _this.sourceUrl) {
                _this.artworks([]);
            }
            if (_this.more) {
                _this.more = false;
                var ajax = new Ajax.Request(_this.sourceUrl);
                ajax.get().then(function (data) {
                    ko.utils.arrayPushAll(_this.artworks, data.data);
                    _this.sourceUrl = data.moreLink;
                    _this.more = data.more;
                }, function (data) {
                    _this.error(data.message);
                });
            }
        };
        this.more = true;
        this._selectedItem = ko.observable(new Gallery.Image());
        this._artworks = ko.observableArray([]);
        this._error = ko.observable();
        this._canSave = ko.pureComputed(function () {
            return _this.selectedItem().id;
        });
    }
    Object.defineProperty(ArtworkModificationViewModel.prototype, "originalUrl", {
        get: function () {
            return this._originalUrl;
        },
        set: function (value) {
            this._originalUrl = value;
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(ArtworkModificationViewModel.prototype, "selectedItem", {
        get: function () {
            return this._selectedItem;
        },
        set: function (value) {
            this._selectedItem = value;
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(ArtworkModificationViewModel.prototype, "element", {
        get: function () {
            return this._element;
        },
        set: function (value) {
            this._element = value;
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(ArtworkModificationViewModel.prototype, "artworks", {
        get: function () {
            return this._artworks;
        },
        set: function (value) {
            this._artworks = value;
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(ArtworkModificationViewModel.prototype, "error", {
        get: function () {
            return this._error;
        },
        set: function (value) {
            this._error = value;
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(ArtworkModificationViewModel.prototype, "position", {
        get: function () {
            return this._position;
        },
        set: function (value) {
            this._position = value;
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(ArtworkModificationViewModel.prototype, "canSave", {
        get: function () {
            return this._canSave;
        },
        set: function (value) {
            this._canSave = value;
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(ArtworkModificationViewModel.prototype, "sourceUrl", {
        get: function () {
            return this._sourceUrl;
        },
        set: function (value) {
            this._sourceUrl = value;
        },
        enumerable: true,
        configurable: true
    });
    return ArtworkModificationViewModel;
}());
//# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiQXJ0d29ya01vZGlmaWNhdGlvblZpZXdNb2RlbC5qcyIsInNvdXJjZVJvb3QiOiIiLCJzb3VyY2VzIjpbIkFydHdvcmtNb2RpZmljYXRpb25WaWV3TW9kZWwudHMiXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6IkFBQUE7SUFBQTtRQUFBLGlCQTZHQztRQTVHVSxXQUFNLEdBQUcsVUFBQyxJQUFtQjtZQUNoQyxLQUFJLENBQUMsWUFBWSxDQUFDLElBQUksQ0FBQyxDQUFDO1FBQzVCLENBQUMsQ0FBQztRQUNLLGFBQVEsR0FBRyxVQUFDLElBQUksRUFBRSxLQUFLO1lBQzFCLElBQUksSUFBSSxHQUFHLEtBQUssQ0FBQyxNQUFNLENBQUM7WUFDeEIsRUFBRSxDQUFDLENBQUMsSUFBSSxDQUFDLFNBQVMsR0FBRyxDQUFDLElBQUksQ0FBQyxZQUFZLEdBQUcsSUFBSSxDQUFDLFlBQVksR0FBRyxHQUFHLENBQUMsQ0FBQyxDQUFDLENBQUM7Z0JBQ2pFLEtBQUksQ0FBQyxJQUFJLEVBQUUsQ0FBQztZQUNoQixDQUFDO1FBQ0wsQ0FBQyxDQUFDO1FBQ1EsU0FBSSxHQUFHO1lBQ2IsRUFBRSxDQUFDLENBQUMsS0FBSSxDQUFDLFdBQVcsS0FBSyxLQUFJLENBQUMsU0FBUyxDQUFDLENBQUMsQ0FBQztnQkFDdEMsS0FBSSxDQUFDLFFBQVEsQ0FBQyxFQUFFLENBQUMsQ0FBQztZQUN0QixDQUFDO1lBQ0QsRUFBRSxDQUFDLENBQUMsS0FBSSxDQUFDLElBQUksQ0FBQyxDQUFDLENBQUM7Z0JBQ1osS0FBSSxDQUFDLElBQUksR0FBRyxLQUFLLENBQUM7Z0JBQ2xCLElBQUksSUFBSSxHQUFHLElBQUksSUFBSSxDQUFDLE9BQU8sQ0FBQyxLQUFJLENBQUMsU0FBUyxDQUFDLENBQUM7Z0JBQzVDLElBQUksQ0FBQyxHQUFHLEVBQUUsQ0FBQyxJQUFJLENBQUMsVUFBQyxJQUFJO29CQUNqQixFQUFFLENBQUMsS0FBSyxDQUFDLFlBQVksQ0FBQyxLQUFJLENBQUMsUUFBUSxFQUFFLElBQUks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