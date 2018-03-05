var MenuItemModalViewModel = /** @class */ (function () {
    function MenuItemModalViewModel(modal, menuItem, fetchUrl) {
        var _this = this;
        this.save = function () {
            _this.modal.trigger('save');
            _this.modal.hide(true, true);
        };
        this._canSave = ko.pureComputed(function () {
            return _this.item().valid();
        });
        this._item = ko.observable();
        var pageType = menuItem.pageType();
        menuItem.pageType('');
        this.item(menuItem);
        this.item().pageType.subscribe(function (newValue) {
            if (newValue !== 'empty' && newValue !== 'external') {
                var call = new Ajax.Request(fetchUrl + "?type=" + newValue);
                call.get().then(function (value) {
                    _this.item().routes.removeAll();
                    for (var i = 0; i < value.routes.length; i++) {
                        var route = new Route(value.routes[i]);
                        _this.item().routes.push(route);
                    }
                });
            }
            else {
                _this.item().route(new Route({ url: '#', name: '#', parameter: '#' }));
                if (newValue === 'empty') {
                    _this.item().displayUrl('');
                }
            }
        });
        this.item().pageType(pageType);
        this.modal = modal;
    }
    Object.defineProperty(MenuItemModalViewModel.prototype, "canSave", {
        get: function () {
            return this._canSave;
        },
        set: function (value) {
            this._canSave = value;
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(MenuItemModalViewModel.prototype, "item", {
        get: function () {
            return this._item;
        },
        set: function (value) {
            this._item = value;
        },
        enumerable: true,
        configurable: true
    });
    return MenuItemModalViewModel;
}());
//# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiTWVudUl0ZW1Nb2RhbFZpZXdNb2RlbC5qcyIsInNvdXJjZVJvb3QiOiIiLCJzb3VyY2VzIjpbIk1lbnVJdGVtTW9kYWxWaWV3TW9kZWwudHMiXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6IkFBQUE7SUFPSSxnQ0FBWSxLQUFZLEVBQUUsUUFBa0IsRUFBRSxRQUFnQjtRQUE5RCxpQkF1QkM7UUE3QkQsU0FBSSxHQUFHO1lBQ0gsS0FBSSxDQUFDLEtBQUssQ0FBQyxPQUFPLENBQUMsTUFBTSxDQUFDLENBQUM7WUFDM0IsS0FBSSxDQUFDLEtBQUssQ0FBQyxJQUFJLENBQUMsSUFBSSxFQUFFLElBQUksQ0FBQyxDQUFDO1FBQ2hDLENBQUMsQ0FBQztRQTRCTSxhQUFRLEdBQUcsRUFBRSxDQUFDLFlBQVksQ0FBQztZQUMvQixNQUFNLENBQUMsS0FBSSxDQUFDLElBQUksRUFBRSxDQUFDLEtBQUssRUFBRSxDQUFDO1FBQy9CLENBQUMsQ0FBQyxDQUFDO1FBVUssVUFBSyxHQUFHLEVBQUUsQ0FBQyxVQUFVLEVBQVksQ0FBQztRQXBDdEMsSUFBSSxRQUFRLEdBQUcsUUFBUSxDQUFDLFFBQVEsRUFBRSxDQUFDO1FBQ25DLFFBQVEsQ0FBQyxRQUFRLENBQUMsRUFBRSxDQUFDLENBQUM7UUFDdEIsSUFBSSxDQUFDLElBQUksQ0FBQyxRQUFRLENBQUMsQ0FBQztRQUNwQixJQUFJLENBQUMsSUFBSSxFQUFFLENBQUMsUUFBUSxDQUFDLFNBQVMsQ0FBQyxVQUFBLFFBQVE7WUFDbkMsRUFBRSxDQUFDLENBQUMsUUFBUSxLQUFLLE9BQU8sSUFBSSxRQUFRLEtBQUssVUFBVSxDQUFDLENBQUMsQ0FBQztnQkFDbEQsSUFBSSxJQUFJLEdBQUcsSUFBSSxJQUFJLENBQUMsT0FBTyxDQUFJLFFBQVEsY0FBUyxRQUFVLENBQUMsQ0FBQztnQkFDNUQsSUFBSSxDQUFDLEdBQUcsRUFBRSxDQUFDLElBQUksQ0FBQyxVQUFBLEtBQUs7b0JBQ2pCLEtBQUksQ0FBQyxJQUFJLEVBQUUsQ0FBQyxNQUFNLENBQUMsU0FBUyxFQUFFLENBQUM7b0JBQy9CLEdBQUcsQ0FBQyxDQUFDLElBQUksQ0FBQyxHQUFHLENBQUMsRUFBRSxDQUFDLEdBQUcsS0FBSyxDQUFDLE1BQU0sQ0FBQyxNQUFNLEVBQUUsQ0FBQyxFQUFFLEVBQUUsQ0FBQzt3QkFDM0MsSUFBSSxLQUFLLEdBQUcsSUFBSSxLQUFLLENBQUMsS0FBSyxDQUFDLE1BQU0sQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDO3dCQUN2QyxLQUFJLENBQUMsSUFBSSxFQUFFLENBQUMsTUFBTSxDQUFDLElBQUksQ0FBQyxLQUFLLENBQUMsQ0FBQztvQkFDbkMsQ0FBQztnQkFDTCxDQUFDLENBQUMsQ0FBQztZQUNQLENBQUM7WUFBQyxJQUFJLENBQUMsQ0FBQztnQkFDSixLQUFJLENBQUMsSUFBSSxFQUFFLENBQUMsS0FBSyxDQUFDLElBQUksS0FBSyxDQUFDLEVBQUMsR0FBRyxFQUFFLEdBQUcsRUFBRSxJQUFJLEVBQUUsR0FBRyxFQUFFLFNBQVMsRUFBRSxHQUFHLEVBQUMsQ0FBQyxDQUFDLENBQUM7Z0JBQ3BFLEVBQUUsQ0FBQyxDQUFDLFFBQVEsS0FBSyxPQUFPLENBQUMsQ0FBQyxDQUFDO29CQUN2QixLQUFJLENBQUMsSUFBSSxFQUFFLENBQUMsVUFBVSxDQUFDLEVBQUUsQ0FBQyxDQUFDO2dCQUMvQixDQUFDO1lBQ0wsQ0FBQztRQUNMLENBQUMsQ0FBQyxDQUFDO1FBQ0gsSUFBSSxDQUFDLElBQUksRUFBRSxDQUFDLFFBQVEsQ0FBQyxRQUFRLENBQUMsQ0FBQztRQUMvQixJQUFJLENBQUMsS0FBSyxHQUFHLEtBQUssQ0FBQztJQUN2QixDQUFDO0lBTUQsc0JBQUksMkNBQU87YUFBWDtZQUNJLE1BQU0sQ0FBQyxJQUFJLENBQUMsUUFBUSxDQUFDO1FBQ3pCLENBQUM7YUFFRCxVQUFZLEtBQTRCO1lBQ3BDLElBQUksQ0FBQyxRQUFRLEdBQUcsS0FBSyxDQUFDO1FBQzFCLENBQUM7OztPQUpBO0lBUUQsc0JBQUksd0NBQUk7YUFBUjtZQUNJLE1BQU0sQ0FBQyxJQUFJLENBQUMsS0FBSyxDQUFDO1FBQ3RCLENBQUM7YUFFRCxVQUFTLEtBQW1DO1lBQ3hDLElBQUksQ0FBQyxLQUFLLEdBQUcsS0FBSyxDQUFDO1FBQ3ZCLENBQUM7OztPQUpBO0lBS0wsNkJBQUM7QUFBRCxDQUFDLEFBckRELElBcURDIn0=