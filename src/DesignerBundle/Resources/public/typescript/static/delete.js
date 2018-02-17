var Delete = /** @class */ (function () {
    function Delete() {
    }
    Delete.init = (function () {
        var buttons = document.querySelectorAll('[data-delete]');
        var _loop_1 = function (i) {
            var item = buttons[i];
            var title = item.getAttribute('data-delete-title');
            var message = item.getAttribute('data-delete-content');
            var positiveButton = item.getAttribute('data-delete-positive-button');
            var negativeButton = item.getAttribute('data-delete-negative-button');
            item.addEventListener('click', function () {
                Modal.confirm(title, message, positiveButton, negativeButton).then(function (value) {
                    if (value) {
                        var call = new Ajax.Request(item.getAttribute('data-delete-url'));
                        call.delete().then(function () {
                            window.location.href = item.getAttribute('data-redirect-target');
                        }, function (data) {
                            Modal.alert(data.message, data.details.message);
                        });
                    }
                });
            });
        };
        for (var i = 0; i < buttons.length; i++) {
            _loop_1(i);
        }
    })();
    return Delete;
}());
//# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiZGVsZXRlLmpzIiwic291cmNlUm9vdCI6IiIsInNvdXJjZXMiOlsiZGVsZXRlLnRzIl0sIm5hbWVzIjpbXSwibWFwcGluZ3MiOiJBQUFBO0lBQUE7SUF1QkEsQ0FBQztJQXRCa0IsV0FBSSxHQUFHLENBQUM7UUFDbkIsSUFBSSxPQUFPLEdBQUcsUUFBUSxDQUFDLGdCQUFnQixDQUFDLGVBQWUsQ0FBQyxDQUFDO2dDQUNoRCxDQUFDO1lBQ04sSUFBSSxJQUFJLEdBQUcsT0FBTyxDQUFDLENBQUMsQ0FBQyxDQUFDO1lBQ3RCLElBQUksS0FBSyxHQUFHLElBQUksQ0FBQyxZQUFZLENBQUMsbUJBQW1CLENBQUMsQ0FBQztZQUNuRCxJQUFJLE9BQU8sR0FBRyxJQUFJLENBQUMsWUFBWSxDQUFDLHFCQUFxQixDQUFDLENBQUM7WUFDdkQsSUFBSSxjQUFjLEdBQUcsSUFBSSxDQUFDLFlBQVksQ0FBQyw2QkFBNkIsQ0FBQyxDQUFDO1lBQ3RFLElBQUksY0FBYyxHQUFHLElBQUksQ0FBQyxZQUFZLENBQUMsNkJBQTZCLENBQUMsQ0FBQztZQUN0RSxJQUFJLENBQUMsZ0JBQWdCLENBQUMsT0FBTyxFQUFFO2dCQUMzQixLQUFLLENBQUMsT0FBTyxDQUFDLEtBQUssRUFBRSxPQUFPLEVBQUUsY0FBYyxFQUFFLGNBQWMsQ0FBQyxDQUFDLElBQUksQ0FBQyxVQUFDLEtBQUs7b0JBQ3JFLEVBQUUsQ0FBQyxDQUFDLEtBQUssQ0FBQyxDQUFDLENBQUM7d0JBQ1IsSUFBSSxJQUFJLEdBQUcsSUFBSSxJQUFJLENBQUMsT0FBTyxDQUFDLElBQUksQ0FBQyxZQUFZLENBQUMsaUJBQWlCLENBQUMsQ0FBQyxDQUFDO3dCQUNsRSxJQUFJLENBQUMsTUFBTSxFQUFFLENBQUMsSUFBSSxDQUFDOzRCQUNmLE1BQU0sQ0FBQyxRQUFRLENBQUMsSUFBSSxHQUFHLElBQUksQ0FBQyxZQUFZLENBQUMsc0JBQXNCLENBQUMsQ0FBQzt3QkFDckUsQ0FBQyxFQUFFLFVBQUMsSUFBZ0I7NEJBQ2hCLEtBQUssQ0FBQyxLQUFLLENBQUMsSUFBSSxDQUFDLE9BQU8sRUFBRSxJQUFJLENBQUMsT0FBTyxDQUFDLE9BQU8sQ0FBQyxDQUFDO3dCQUNwRCxDQUFDLENBQUMsQ0FBQztvQkFDUCxDQUFDO2dCQUNMLENBQUMsQ0FBQyxDQUFDO1lBQ1AsQ0FBQyxDQUFDLENBQUM7UUFDUCxDQUFDO1FBbEJELEdBQUcsQ0FBQyxDQUFDLElBQUksQ0FBQyxHQUFHLENBQUMsRUFBRSxDQUFDLEdBQUcsT0FBTyxDQUFDLE1BQU0sRUFBRSxDQUFDLEVBQUU7b0JBQTlCLENBQUM7U0FrQlQ7SUFDTCxDQUFDLENBQUMsRUFBRSxDQUFDO0lBQ1QsYUFBQztDQUFBLEFBdkJELElBdUJDIn0=