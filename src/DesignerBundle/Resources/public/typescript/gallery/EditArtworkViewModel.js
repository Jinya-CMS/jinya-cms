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
var EditArtworkViewModel = /** @class */ (function (_super) {
    __extends(EditArtworkViewModel, _super);
    function EditArtworkViewModel(element, id, parentVm, initialSource) {
        var _this = _super.call(this) || this;
        _this.save = function () {
            var selectedItem = _this.selectedItem();
            var slug = selectedItem.slug;
            var saveUrl = _this.saveUrl.replace('%23tempslug%23', slug) + "?id=" + _this.id;
            var ajax = new Ajax.Request(saveUrl);
            ajax.put({}).then(function () {
                var modal = Modal.get(_this.element);
                modal.hide();
                _this.parentVm.load();
            }, function (data) {
                Modal.alert(data.message, data.details.message);
            });
        };
        _this.deleteImage = function () {
            var confirmMessage = "<img src=\"" + _this.initialSource() + "\" /><p>" + texts['designer.gallery.delete.image.message'] + "</p>";
            Modal.confirm(texts['designer.gallery.delete.image.title'], confirmMessage, texts['generic.delete'], texts['generic.dont.delete']).then(function (result) {
                if (result) {
                    var deleteUrl = _this.deleteUrl.replace('%23tempid%23', _this.id.toString());
                    var ajax = new Ajax.Request(deleteUrl);
                    ajax.delete().then(function () {
                        var modal = Modal.get(_this.element);
                        modal.hide();
                        _this.parentVm.load();
                    }, function (data) {
                        Modal.alert(data.message, data.details.message);
                    });
                }
            });
        };
        _this._initialSource = ko.observable();
        _this.element = element;
        _this.id = id;
        _this.saveUrl = element.getAttribute('data-save-url');
        _this.deleteUrl = element.getAttribute('data-delete-url');
        _this.originalUrl = _this.sourceUrl = element.getAttribute('data-source-url');
        _this.parentVm = parentVm;
        _this.initialSource(initialSource);
        return _this;
    }
    Object.defineProperty(EditArtworkViewModel.prototype, "initialSource", {
        get: function () {
            return this._initialSource;
        },
        set: function (value) {
            this._initialSource = value;
        },
        enumerable: true,
        configurable: true
    });
    EditArtworkViewModel.init = function (element, id, parentVm, initialSource) {
        var vm = new EditArtworkViewModel(element, id, parentVm, initialSource);
        ko.applyBindings(vm, element);
        vm.load();
        return vm;
    };
    return EditArtworkViewModel;
}(ArtworkModificationViewModel));
//# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiRWRpdEFydHdvcmtWaWV3TW9kZWwuanMiLCJzb3VyY2VSb290IjoiIiwic291cmNlcyI6WyJFZGl0QXJ0d29ya1ZpZXdNb2RlbC50cyJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiOzs7Ozs7Ozs7O0FBQUE7SUFBbUMsd0NBQTRCO0lBNEMzRCw4QkFBWSxPQUFnQixFQUFFLEVBQVUsRUFBRSxRQUEyQixFQUFFLGFBQXFCO1FBQTVGLFlBQ0ksaUJBQU8sU0FRVjtRQTdDRCxVQUFJLEdBQUc7WUFDSCxJQUFJLFlBQVksR0FBRyxLQUFJLENBQUMsWUFBWSxFQUFFLENBQUM7WUFDdkMsSUFBSSxJQUFJLEdBQUcsWUFBWSxDQUFDLElBQUksQ0FBQztZQUM3QixJQUFJLE9BQU8sR0FBTSxLQUFJLENBQUMsT0FBTyxDQUFDLE9BQU8sQ0FBQyxnQkFBZ0IsRUFBRSxJQUFJLENBQUMsWUFBTyxLQUFJLENBQUMsRUFBSSxDQUFDO1lBQzlFLElBQUksSUFBSSxHQUFHLElBQUksSUFBSSxDQUFDLE9BQU8sQ0FBQyxPQUFPLENBQUMsQ0FBQztZQUNyQyxJQUFJLENBQUMsR0FBRyxDQUFDLEVBQUUsQ0FBQyxDQUFDLElBQUksQ0FBQztnQkFDZCxJQUFJLEtBQUssR0FBRyxLQUFLLENBQUMsR0FBRyxDQUFDLEtBQUksQ0FBQyxPQUFPLENBQUMsQ0FBQztnQkFDcEMsS0FBSyxDQUFDLElBQUksRUFBRSxDQUFDO2dCQUViLEtBQUksQ0FBQyxRQUFRLENBQUMsSUFBSSxFQUFFLENBQUM7WUFDekIsQ0FBQyxFQUFFLFVBQUMsSUFBZ0I7Z0JBQ2hCLEtBQUssQ0FBQyxLQUFLLENBQUMsSUFBSSxDQUFDLE9BQU8sRUFBRSxJQUFJLENBQUMsT0FBTyxDQUFDLE9BQU8sQ0FBQyxDQUFDO1lBQ3BELENBQUMsQ0FBQyxDQUFDO1FBQ1AsQ0FBQyxDQUFDO1FBQ0YsaUJBQVcsR0FBRztZQUNWLElBQUksY0FBYyxHQUFHLGdCQUFhLEtBQUksQ0FBQyxhQUFhLEVBQUUsZ0JBQVUsS0FBSyxDQUFDLHVD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