var FilePicker = /** @class */ (function () {
    function FilePicker() {
    }
    FilePicker.init = (function () {
        var buttons = document.querySelectorAll('[data-toggle=file]');
        var _loop_1 = function (i) {
            var button = buttons.item(i);
            var target = button.getAttribute('data-target');
            var fileInput = document.querySelector("input[type=file]#" + target);
            var textInput = document.querySelector("input[type=text][data-id=" + target + "]");
            fileInput.addEventListener('change', function (evt) {
                textInput.value = fileInput.value;
            });
            button.addEventListener('click', function (evt) { return fileInput.click(); });
        };
        for (var i = 0; i < buttons.length; i++) {
            _loop_1(i);
        }
    })();
    return FilePicker;
}());
//# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiZmlsZXBpY2tlci5qcyIsInNvdXJjZVJvb3QiOiIiLCJzb3VyY2VzIjpbImZpbGVwaWNrZXIudHMiXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6IkFBQUE7SUFBQTtJQWdCQSxDQUFDO0lBZmtCLGVBQUksR0FBRyxDQUFDO1FBQ25CLElBQUksT0FBTyxHQUFHLFFBQVEsQ0FBQyxnQkFBZ0IsQ0FBb0Isb0JBQW9CLENBQUMsQ0FBQztnQ0FDeEUsQ0FBQztZQUNOLElBQUksTUFBTSxHQUFHLE9BQU8sQ0FBQyxJQUFJLENBQUMsQ0FBQyxDQUFDLENBQUM7WUFDN0IsSUFBSSxNQUFNLEdBQUcsTUFBTSxDQUFDLFlBQVksQ0FBQyxhQUFhLENBQUMsQ0FBQztZQUNoRCxJQUFJLFNBQVMsR0FBRyxRQUFRLENBQUMsYUFBYSxDQUFtQixzQkFBb0IsTUFBUSxDQUFDLENBQUM7WUFDdkYsSUFBSSxTQUFTLEdBQUcsUUFBUSxDQUFDLGFBQWEsQ0FBbUIsOEJBQTRCLE1BQU0sTUFBRyxDQUFDLENBQUM7WUFFaEcsU0FBUyxDQUFDLGdCQUFnQixDQUFDLFFBQVEsRUFBRSxVQUFBLEdBQUc7Z0JBQ3BDLFNBQVMsQ0FBQyxLQUFLLEdBQUcsU0FBUyxDQUFDLEtBQUssQ0FBQztZQUN0QyxDQUFDLENBQUMsQ0FBQztZQUVILE1BQU0sQ0FBQyxnQkFBZ0IsQ0FBQyxPQUFPLEVBQUUsVUFBQSxHQUFHLElBQUksT0FBQSxTQUFTLENBQUMsS0FBSyxFQUFFLEVBQWpCLENBQWlCLENBQUMsQ0FBQztRQUMvRCxDQUFDO1FBWEQsR0FBRyxDQUFDLENBQUMsSUFBSSxDQUFDLEdBQUcsQ0FBQyxFQUFFLENBQUMsR0FBRyxPQUFPLENBQUMsTUFBTSxFQUFFLENBQUMsRUFBRTtvQkFBOUIsQ0FBQztTQVdUO0lBQ0wsQ0FBQyxDQUFDLEVBQUUsQ0FBQztJQUNULGlCQUFDO0NBQUEsQUFoQkQsSUFnQkMifQ==