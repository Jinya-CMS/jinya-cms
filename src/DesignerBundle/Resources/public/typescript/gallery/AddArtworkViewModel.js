var __extends = (this && this.__extends) || (function () {
    var extendStatics = Object.setPrototypeOf ||
        ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
        function (d, b) { for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p]; };
    return function (d, b) {
        extendStatics(d, b);
        function __() { this.constructor = d; }
        d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
    };
})();
var AddArtworkViewModel = /** @class */ (function (_super) {
    __extends(AddArtworkViewModel, _super);
    function AddArtworkViewModel(element, position, parentVm) {
        var _this = _super.call(this) || this;
        _this.save = function () {
            var selectedItem = _this.selectedItem();
            var slug = selectedItem.slug;
            var saveUrl = _this.saveUrl.replace('%23tempslug%23', slug) + "?position=" + (_this.position || 0);
            var ajax = new Ajax.Request(saveUrl);
            ajax.post({}).then(function () {
                var modal = Modal.get(_this.element);
                modal.hide();
                _this.parentVm.load();
            }, function (data) {
                Modal.alert(data.message, data.details.message);
            });
        };
        _this.element = element;
        _this.position = position;
        _this.saveUrl = element.getAttribute('data-save-url');
        _this.originalUrl = _this.sourceUrl = element.getAttribute('data-source-url');
        _this.parentVm = parentVm;
        return _this;
    }
    AddArtworkViewModel.init = function (element, position, parentVm) {
        var vm = new AddArtworkViewModel(element, position, parentVm);
        ko.applyBindings(vm, element);
        vm.load();
        return vm;
    };
    return AddArtworkViewModel;
}(ArtworkModificationViewModel));
//# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiQWRkQXJ0d29ya1ZpZXdNb2RlbC5qcyIsInNvdXJjZVJvb3QiOiIiLCJzb3VyY2VzIjpbIkFkZEFydHdvcmtWaWV3TW9kZWwudHMiXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6Ijs7Ozs7Ozs7OztBQUFBO0lBQWtDLHVDQUE0QjtJQTJCMUQsNkJBQVksT0FBZ0IsRUFBRSxRQUFnQixFQUFFLFFBQTJCO1FBQTNFLFlBQ0ksaUJBQU8sU0FNVjtRQXpCRCxVQUFJLEdBQUc7WUFDSCxJQUFJLFlBQVksR0FBRyxLQUFJLENBQUMsWUFBWSxFQUFFLENBQUM7WUFDdkMsSUFBSSxJQUFJLEdBQUcsWUFBWSxDQUFDLElBQUksQ0FBQztZQUM3QixJQUFJLE9BQU8sR0FBTSxLQUFJLENBQUMsT0FBTyxDQUFDLE9BQU8sQ0FBQyxnQkFBZ0IsRUFBRSxJQUFJLENBQUMsbUJBQWEsS0FBSSxDQUFDLFFBQVEsSUFBSSxDQUFDLENBQUUsQ0FBQztZQUMvRixJQUFJLElBQUksR0FBRyxJQUFJLElBQUksQ0FBQyxPQUFPLENBQUMsT0FBTyxDQUFDLENBQUM7WUFDckMsSUFBSSxDQUFDLElBQUksQ0FBQyxFQUFFLENBQUMsQ0FBQyxJQUFJLENBQUM7Z0JBQ2YsSUFBSSxLQUFLLEdBQUcsS0FBSyxDQUFDLEdBQUcsQ0FBQyxLQUFJLENBQUMsT0FBTyxDQUFDLENBQUM7Z0JBQ3BDLEtBQUssQ0FBQyxJQUFJLEVBQUUsQ0FBQztnQkFFYixLQUFJLENBQUMsUUFBUSxDQUFDLElBQUksRUFBRSxDQUFDO1lBQ3pCLENBQUMsRUFBRSxVQUFDLElBQWdCO2dCQUNoQixLQUFLLENBQUMsS0FBSyxDQUFDLElBQUksQ0FBQyxPQUFPLEVBQUUsSUFBSSxDQUFDLE9BQU8sQ0FBQyxPQUFPLENBQUMsQ0FBQztZQUNwRCxDQUFDLENBQUMsQ0FBQztRQUNQLENBQUMsQ0FBQztRQU9FLEtBQUksQ0FBQyxPQUFPLEdBQUcsT0FBTyxDQUFDO1FBQ3ZCLEtBQUksQ0FBQyxRQUFRLEdBQUcsUUFBUSxDQUFDO1FBQ3pCLEtBQUksQ0FBQyxPQUFPLEdBQUcsT0FBTyxDQUFDLFlBQVksQ0FBQyxlQUFlLENBQUMsQ0FBQztRQUNyRCxLQUFJLENBQUMsV0FBVyxHQUFHLEtBQUksQ0FBQyxTQUFTLEdBQUcsT0FBTyxDQUFDLFlBQVksQ0FBQyxpQkFBaUIsQ0FBQyxDQUFDO1FBQzVFLEtBQUksQ0FBQyxRQUFRLEdBQUcsUUFBUSxDQUFDOztJQUM3QixDQUFDO0lBakNNLHdCQUFJLEdBQUcsVUFBQyxPQUFnQixFQUFFLFFBQWdCLEVBQUUsUUFBMkI7UUFDMUUsSUFBSSxFQUFFLEdBQUcsSUFBSSxtQkFBbUIsQ0FBQyxPQUFPLEVBQUUsUUFBUSxFQUFFLFFBQVEsQ0FBQyxDQUFDO1FBQzlELEVBQUUsQ0FBQyxhQUFhLENBQUMsRUFBRSxFQUFFLE9BQU8sQ0FBQyxDQUFDO1FBQzlCLEVBQUUsQ0FBQyxJQUFJLEVBQUUsQ0FBQztRQUVWLE1BQU0sQ0FBQyxFQUFFLENBQUM7SUFDZCxDQUFDLENBQUM7SUE0Qk4sMEJBQUM7Q0FBQSxBQW5DRCxDQUFrQyw0QkFBNEIsR0FtQzdEIn0=