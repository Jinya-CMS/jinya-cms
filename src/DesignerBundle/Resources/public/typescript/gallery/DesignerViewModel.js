var Direction;
(function (Direction) {
    Direction[Direction["RIGHT"] = 0] = "RIGHT";
    Direction[Direction["LEFT"] = 1] = "LEFT";
})(Direction || (Direction = {}));
var DesignerViewModel = /** @class */ (function () {
    function DesignerViewModel() {
        var _this = this;
        this.moveRight = function (item) {
            _this.moveImage(item, Direction.RIGHT);
        };
        this.moveLeft = function (item) {
            _this.moveImage(item, Direction.LEFT);
        };
        this.moveImage = function (item, direction) {
            var items = _this.images();
            var filteredItems = items.filter(function (value, index, array) {
                return value.id === item.id;
            });
            var selectedItemIdx = items.indexOf(filteredItems[0]);
            var newPosition = 0;
            if (direction === Direction.LEFT) {
                newPosition = items[selectedItemIdx - 1].position - 1;
            }
            else {
                newPosition = items[selectedItemIdx + 1].position + 1;
            }
            var moveUrl = _this.moveUrl.replace('%23tempid%23', item.id) + "?newPosition=" + newPosition;
            var request = new Ajax.Request(moveUrl);
            request.put({}).then(function () {
                _this.load();
            }, function () {
                Modal.alert(texts['designer.gallery.move.error.title'], texts['designer.gallery.move.error.body']);
            });
        };
        this.addImage = function (item) {
            var template = document.querySelector('#add-modal-template').innerHTML;
            var modal = document.createElement('div');
            modal.innerHTML = template;
            _this.body.appendChild(modal);
            var element = document.querySelector('#add-modal');
            var picker = new Modal(element);
            picker.show();
            var position = item ? item.position : _this.getHighestPosition();
            var vm = AddArtworkViewModel.init(element, position, _this);
            picker.on('closed', function () {
                _this.body.removeChild(modal);
            });
        };
        this.editImage = function (item) {
            var template = document.querySelector('#edit-modal-template').innerHTML;
            var modal = document.createElement('div');
            modal.innerHTML = template;
            _this.body.appendChild(modal);
            var element = document.querySelector('#edit-modal');
            var picker = new Modal(element);
            picker.show();
            var vm = EditArtworkViewModel.init(element, item.id, _this, item.source);
            picker.on('closed', function () {
                _this.body.removeChild(modal);
            });
        };
        this.load = function () {
            var ajax = new Ajax.Request(_this._sourceUrl);
            ajax.get().then(function (data) {
                _this.images(data);
            });
        };
        this.getHighestPosition = function () {
            var images = _this.images();
            var positions = images.sort(function (a, b) {
                return a.position > b.position ? -1 : 1;
            });
        };
        this._images = ko.observableArray();
        this._body = document.querySelector('body');
    }
    Object.defineProperty(DesignerViewModel.prototype, "moveUrl", {
        get: function () {
            return this._moveUrl;
        },
        set: function (value) {
            this._moveUrl = value;
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(DesignerViewModel.prototype, "sourceUrl", {
        get: function () {
            return this._sourceUrl;
        },
        set: function (value) {
            this._sourceUrl = value;
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(DesignerViewModel.prototype, "images", {
        get: function () {
            return this._images;
        },
        set: function (value) {
            this._images = value;
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(DesignerViewModel.prototype, "body", {
        get: function () {
            return this._body;
        },
        enumerable: true,
        configurable: true
    });
    DesignerViewModel.activate = (function () {
        var target = document.querySelector('.gallery');
        var vm = new DesignerViewModel();
        ko.applyBindings(vm, target);
        vm._sourceUrl = target.getAttribute('data-source');
        vm._moveUrl = target.getAttribute('data-move');
        vm.load();
    })();
    return DesignerViewModel;
}());
//# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiRGVzaWduZXJWaWV3TW9kZWwuanMiLCJzb3VyY2VSb290IjoiIiwic291cmNlcyI6WyJEZXNpZ25lclZpZXdNb2RlbC50cyJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiQUFBQSxJQUFLLFNBR0o7QUFIRCxXQUFLLFNBQVM7SUFDViwyQ0FBSyxDQUFBO0lBQ0wseUNBQUksQ0FBQTtBQUNSLENBQUMsRUFISSxTQUFTLEtBQVQsU0FBUyxRQUdiO0FBRUQ7SUFBQTtRQUFBLGlCQXFIQztRQTNHRyxjQUFTLEdBQUcsVUFBQyxJQUFtQjtZQUM1QixLQUFJLENBQUMsU0FBUyxDQUFDLElBQUksRUFBRSxTQUFTLENBQUMsS0FBSyxDQUFDLENBQUM7UUFDMUMsQ0FBQyxDQUFDO1FBQ0YsYUFBUSxHQUFHLFVBQUMsSUFBbUI7WUFDM0IsS0FBSSxDQUFDLFNBQVMsQ0FBQyxJQUFJLEVBQUUsU0FBUyxDQUFDLElBQUksQ0FBQyxDQUFDO1FBQ3pDLENBQUMsQ0FBQztRQUNGLGNBQVMsR0FBRyxVQUFDLElBQW1CLEVBQUUsU0FBb0I7WUFDbEQsSUFBSSxLQUFLLEdBQUcsS0FBSSxDQUFDLE1BQU0sRUFBRSxDQUFDO1lBQzFCLElBQUksYUFBYSxHQUFHLEtBQUssQ0FBQyxNQUFNLENBQUMsVUFBQyxLQUFLLEVBQUUsS0FBSyxFQUFFLEtBQUs7Z0JBQ2pELE1BQU0sQ0FBQyxLQUFLLENBQUMsRUFBRSxLQUFLLElBQUksQ0FBQyxFQUFFLENBQUM7WUFDaEMsQ0FBQyxDQUFDLENBQUM7WUFDSCxJQUFJLGVBQWUsR0FBRyxLQUFLLENBQUMsT0FBTyxDQUFDLGFBQWEsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDO1lBQ3RELElBQUksV0FBVyxHQUFHLENBQUMsQ0FBQztZQUNwQixFQUFFLENBQUMsQ0FBQyxTQUFTLEtBQUssU0FBUyxDQUFDLElBQUksQ0FBQyxDQUFDLENBQUM7Z0JBQy9CLFdBQVcsR0FBRyxLQUFLLENBQUMsZUFBZSxHQUFHLENBQUMsQ0FBQyxDQUFDLFFBQVEsR0FBRyxD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