var FormItem = /** @class */ (function () {
    function FormItem() {
        this._required = ko.observable();
        this._selectOptions = ko.observable();
        this._helpText = ko.observable();
        this._options = ko.observable();
        this._type = ko.observable();
        this._label = ko.observable();
    }
    Object.defineProperty(FormItem.prototype, "required", {
        get: function () {
            return this._required;
        },
        set: function (value) {
            this._required = value;
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(FormItem.prototype, "selectOptions", {
        get: function () {
            return this._selectOptions;
        },
        set: function (value) {
            this._selectOptions = value;
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(FormItem.prototype, "helpText", {
        get: function () {
            return this._helpText;
        },
        set: function (value) {
            this._helpText = value;
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(FormItem.prototype, "options", {
        get: function () {
            return this._options;
        },
        set: function (value) {
            this._options = value;
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(FormItem.prototype, "type", {
        get: function () {
            return this._type;
        },
        set: function (value) {
            this._type = value;
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(FormItem.prototype, "label", {
        get: function () {
            return this._label;
        },
        set: function (value) {
            this._label = value;
        },
        enumerable: true,
        configurable: true
    });
    return FormItem;
}());
//# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiRm9ybUl0ZW0uanMiLCJzb3VyY2VSb290IjoiIiwic291cmNlcyI6WyJGb3JtSXRlbS50cyJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiQUFBQTtJQUFBO1FBQ1ksY0FBUyxHQUFHLEVBQUUsQ0FBQyxVQUFVLEVBQVcsQ0FBQztRQVVyQyxtQkFBYyxHQUFHLEVBQUUsQ0FBQyxVQUFVLEVBQVUsQ0FBQztRQVV6QyxjQUFTLEdBQUcsRUFBRSxDQUFDLFVBQVUsRUFBVSxDQUFDO1FBVXBDLGFBQVEsR0FBRyxFQUFFLENBQUMsVUFBVSxFQUFVLENBQUM7UUFVbkMsVUFBSyxHQUFHLEVBQUUsQ0FBQyxVQUFVLEVBQVUsQ0FBQztRQVVoQyxXQUFNLEdBQUcsRUFBRSxDQUFDLFVBQVUsRUFBVSxDQUFDO0lBUzdDLENBQUM7SUF6REcsc0JBQUksOEJBQVE7YUFBWjtZQUNJLE1BQU0sQ0FBQyxJQUFJLENBQUMsU0FBUyxDQUFDO1FBQzFCLENBQUM7YUFFRCxVQUFhLEtBQWtDO1lBQzNDLElBQUksQ0FBQyxTQUFTLEdBQUcsS0FBSyxDQUFDO1FBQzNCLENBQUM7OztPQUpBO0lBUUQsc0JBQUksbUNBQWE7YUFBakI7WUFDSSxNQUFNLENBQUMsSUFBSSxDQUFDLGNBQWMsQ0FBQztRQUMvQixDQUFDO2FBRUQsVUFBa0IsS0FBaUM7WUFDL0MsSUFBSSxDQUFDLGNBQWMsR0FBRyxLQUFLLENBQUM7UUFDaEMsQ0FBQzs7O09BSkE7SUFRRCxzQkFBSSw4QkFBUTthQUFaO1lBQ0ksTUFBTSxDQUFDLElBQUksQ0FBQyxTQUFTLENBQUM7UUFDMUIsQ0FBQzthQUVELFVBQWEsS0FBaUM7WUFDMUMsSUFBSSxDQUFDLFNBQVMsR0FBRyxLQUFLLENBQUM7UUFDM0IsQ0FBQzs7O09BSkE7SUFRRCxzQkFBSSw2QkFBTzthQUFYO1lBQ0ksTUFBTSxDQUFDLElBQUksQ0FBQyxRQUFRLENBQUM7UUFDekIsQ0FBQzthQUVELFVBQVksS0FBaUM7WUFDekMsSUFBSSxDQUFDLFFBQVEsR0FBRyxLQUFLLENBQUM7UUFDMUIsQ0FBQzs7O09BSkE7SUFRRCxzQkFBSSwwQkFBSTthQUFSO1lBQ0ksTUFBTSxDQUFDLElBQUksQ0FBQyxLQUFLLENBQUM7UUFDdEIsQ0FBQzthQUVELFVBQVMsS0FBaUM7WUFDdEMsSUFBSSxDQUFDLEtBQUssR0FBRyxLQUFLLENBQUM7UUFDdkIsQ0FBQzs7O09BSkE7SUFRRCxzQkFBSSwyQkFBSzthQUFUO1lBQ0ksTUFBTSxDQUFDLElBQUksQ0FBQyxNQUFNLENBQUM7UUFDdkIsQ0FBQzthQUVELFVBQVUsS0FBaUM7WUFDdkMsSUFBSSxDQUFDLE1BQU0sR0FBRyxLQUFLLENBQUM7UUFDeEIsQ0FBQzs7O09BSkE7SUFLTCxlQUFDO0FBQUQsQ0FBQyxBQTVERCxJQTREQyJ9