var FilePickerActivator = /** @class */ (function () {
    function FilePickerActivator() {
    }
    FilePickerActivator.init = function () {
        var $fileInputs = $('input[type=file]');
        $fileInputs.each(function (index, element) {
            var $element = $(element);
            var $parent = $element.parent();
            var placeholder = $element.attr('placeholder');
            var $inputGroup = $('<div/>', {
                'class': 'input-group'
            });
            var $input = $('<input/>', {
                'type': 'text',
                'class': 'form-control',
                'placeholder': placeholder,
                'aria-label': placeholder,
                'readonly': true
            });
            var $span = $('<span/>', {
                'class': 'input-group-btn'
            });
            var $button = $('<button/>', {
                'class': 'btn btn-secondary',
                'type': 'button',
                'text': Util.getText('generic.file.pick')
            });
            $element.hide();
            $parent.append($inputGroup.append($input).append($span.append($button)));
            $button.click(function () {
                $element.click();
            });
            $element.change(function () {
                $input.val($element.val());
            });
        });
    };
    return FilePickerActivator;
}());
$(function () {
    FilePickerActivator.init();
});
//# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiRmlsZVBpY2tlckFjdGl2YXRvci5qcyIsInNvdXJjZVJvb3QiOiIiLCJzb3VyY2VzIjpbIkZpbGVQaWNrZXJBY3RpdmF0b3IudHMiXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6IkFBQUE7SUFBQTtJQTJDQSxDQUFDO0lBMUNpQix3QkFBSSxHQUFsQjtRQUNJLElBQUksV0FBVyxHQUFHLENBQUMsQ0FBQyxrQkFBa0IsQ0FBQyxDQUFDO1FBQ3hDLFdBQVcsQ0FBQyxJQUFJLENBQUMsVUFBQyxLQUFLLEVBQUUsT0FBTztZQUM1QixJQUFJLFFBQVEsR0FBRyxDQUFDLENBQUMsT0FBTyxDQUFDLENBQUM7WUFDMUIsSUFBSSxPQUFPLEdBQUcsUUFBUSxDQUFDLE1BQU0sRUFBRSxDQUFDO1lBQ2hDLElBQUksV0FBVyxHQUFHLFFBQVEsQ0FBQyxJQUFJLENBQUMsYUFBYSxDQUFDLENBQUM7WUFDL0MsSUFBSSxXQUFXLEdBQUcsQ0FBQyxDQUFDLFFBQVEsRUFBRTtnQkFDMUIsT0FBTyxFQUFFLGFBQWE7YUFDekIsQ0FBQyxDQUFDO1lBQ0gsSUFBSSxNQUFNLEdBQUcsQ0FBQyxDQUFDLFVBQVUsRUFBRTtnQkFDdkIsTUFBTSxFQUFFLE1BQU07Z0JBQ2QsT0FBTyxFQUFFLGNBQWM7Z0JBQ3ZCLGFBQWEsRUFBRSxXQUFXO2dCQUMxQixZQUFZLEVBQUUsV0FBVztnQkFDekIsVUFBVSxFQUFFLElBQUk7YUFDbkIsQ0FBQyxDQUFDO1lBQ0gsSUFBSSxLQUFLLEdBQUcsQ0FBQyxDQUFDLFNBQVMsRUFBRTtnQkFDckIsT0FBTyxFQUFFLGlCQUFpQjthQUM3QixDQUFDLENBQUM7WUFDSCxJQUFJLE9BQU8sR0FBRyxDQUFDLENBQUMsV0FBVyxFQUFFO2dCQUN6QixPQUFPLEVBQUUsbUJBQW1CO2dCQUM1QixNQUFNLEVBQUUsUUFBUTtnQkFDaEIsTUFBTSxFQUFFLElBQUksQ0FBQyxPQUFPLENBQUMsbUJBQW1CLENBQUM7YUFDNUMsQ0FBQyxDQUFDO1lBQ0gsUUFBUSxDQUFDLElBQUksRUFBRSxDQUFDO1lBQ2hCLE9BQU8sQ0FBQyxNQUFNLENBQ1YsV0FBVyxDQUFDLE1BQU0sQ0FDZCxNQUFNLENBQ1QsQ0FBQyxNQUFNLENBQ0osS0FBSyxDQUFDLE1BQU0sQ0FDUixPQUFPLENBQ1YsQ0FDSixDQUNKLENBQUM7WUFDRixPQUFPLENBQUMsS0FBSyxDQUFDO2dCQUNWLFFBQVEsQ0FBQyxLQUFLLEVBQUUsQ0FBQztZQUNyQixDQUFDLENBQUMsQ0FBQztZQUNILFFBQVEsQ0FBQyxNQUFNLENBQUM7Z0JBQ1osTUFBTSxDQUFDLEdBQUcsQ0FBQyxRQUFRLENBQUMsR0FBRyxFQUFFLENBQUMsQ0FBQztZQUMvQixDQUFDLENBQUMsQ0FBQztRQUNQLENBQUMsQ0FBQyxDQUFBO0lBQ04sQ0FBQztJQUNMLDBCQUFDO0FBQUQsQ0FBQyxBQTNDRCxJQTJDQztBQUVELENBQUMsQ0FBQztJQUNFLG1CQUFtQixDQUFDLElBQUksRUFBRSxDQUFDO0FBQy9CLENBQUMsQ0FBQyxDQUFDIn0=