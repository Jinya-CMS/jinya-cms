var FormItemModalViewModel = /** @class */ (function () {
    function FormItemModalViewModel(modal, formItem) {
        var _this = this;
        this.save = function () {
            _this.modal.trigger('save');
            _this.modal.hide();
        };
        this._item = ko.observable();
        this.item(formItem);
        this.modal = modal;
    }
    Object.defineProperty(FormItemModalViewModel.prototype, "item", {
        get: function () {
            return this._item;
        },
        set: function (value) {
            this._item = value;
        },
        enumerable: true,
        configurable: true
    });
    return FormItemModalViewModel;
}());
//# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiRm9ybUl0ZW1Nb2RhbFZpZXdNb2RlbC5qcyIsInNvdXJjZVJvb3QiOiIiLCJzb3VyY2VzIjpbIkZvcm1JdGVtTW9kYWxWaWV3TW9kZWwudHMiXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6IkFBQUE7SUFPSSxnQ0FBWSxLQUFZLEVBQUUsUUFBa0I7UUFBNUMsaUJBR0M7UUFURCxTQUFJLEdBQUc7WUFDSCxLQUFJLENBQUMsS0FBSyxDQUFDLE9BQU8sQ0FBQyxNQUFNLENBQUMsQ0FBQztZQUMzQixLQUFJLENBQUMsS0FBSyxDQUFDLElBQUksRUFBRSxDQUFDO1FBQ3RCLENBQUMsQ0FBQztRQVFNLFVBQUssR0FBRyxFQUFFLENBQUMsVUFBVSxFQUFZLENBQUM7UUFKdEMsSUFBSSxDQUFDLElBQUksQ0FBQyxRQUFRLENBQUMsQ0FBQztRQUNwQixJQUFJLENBQUMsS0FBSyxHQUFHLEtBQUssQ0FBQztJQUN2QixDQUFDO0lBSUQsc0JBQUksd0NBQUk7YUFBUjtZQUNJLE1BQU0sQ0FBQyxJQUFJLENBQUMsS0FBSyxDQUFDO1FBQ3RCLENBQUM7YUFFRCxVQUFTLEtBQW1DO1lBQ3hDLElBQUksQ0FBQyxLQUFLLEdBQUcsS0FBSyxDQUFDO1FBQ3ZCLENBQUM7OztPQUpBO0lBS0wsNkJBQUM7QUFBRCxDQUFDLEFBckJELElBcUJDIn0=