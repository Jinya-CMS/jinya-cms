var DeleteBackgroundImage = /** @class */ (function () {
    function DeleteBackgroundImage() {
    }
    DeleteBackgroundImage.init = function () {
        var $ajaxDelete = $('[data-ajax]');
        $ajaxDelete.click(function () {
            event.preventDefault();
            $ajaxDelete.parents('.row').hide();
            $.ajax($ajaxDelete.data('action'), {
                method: $ajaxDelete.data('method')
            }).then(function () {
            }, function () {
                $ajaxDelete.parents('.row').show();
            });
        });
    };
    return DeleteBackgroundImage;
}());
$(function () {
    DeleteBackgroundImage.init();
});
//# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiRGVsZXRlQmFja2dyb3VuZEltYWdlLmpzIiwic291cmNlUm9vdCI6IiIsInNvdXJjZXMiOlsiRGVsZXRlQmFja2dyb3VuZEltYWdlLnRzIl0sIm5hbWVzIjpbXSwibWFwcGluZ3MiOiJBQUFBO0lBQUE7SUFjQSxDQUFDO0lBYmlCLDBCQUFJLEdBQWxCO1FBQ0ksSUFBSSxXQUFXLEdBQUcsQ0FBQyxDQUFDLGFBQWEsQ0FBQyxDQUFDO1FBQ25DLFdBQVcsQ0FBQyxLQUFLLENBQUM7WUFDZCxLQUFLLENBQUMsY0FBYyxFQUFFLENBQUM7WUFDdkIsV0FBVyxDQUFDLE9BQU8sQ0FBQyxNQUFNLENBQUMsQ0FBQyxJQUFJLEVBQUUsQ0FBQztZQUNuQyxDQUFDLENBQUMsSUFBSSxDQUFDLFdBQVcsQ0FBQyxJQUFJLENBQUMsUUFBUSxDQUFDLEVBQUU7Z0JBQy9CLE1BQU0sRUFBRSxXQUFXLENBQUMsSUFBSSxDQUFDLFFBQVEsQ0FBQzthQUNyQyxDQUFDLENBQUMsSUFBSSxDQUFDO1lBQ1IsQ0FBQyxFQUFFO2dCQUNDLFdBQVcsQ0FBQyxPQUFPLENBQUMsTUFBTSxDQUFDLENBQUMsSUFBSSxFQUFFLENBQUM7WUFDdkMsQ0FBQyxDQUFDLENBQUM7UUFDUCxDQUFDLENBQUMsQ0FBQztJQUNQLENBQUM7SUFDTCw0QkFBQztBQUFELENBQUMsQUFkRCxJQWNDO0FBRUQsQ0FBQyxDQUFDO0lBQ0UscUJBQXFCLENBQUMsSUFBSSxFQUFFLENBQUM7QUFDakMsQ0FBQyxDQUFDLENBQUMifQ==