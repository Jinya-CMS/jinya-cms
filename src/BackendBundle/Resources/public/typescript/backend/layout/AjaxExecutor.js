var AjaxExecutor = /** @class */ (function () {
    function AjaxExecutor() {
    }
    AjaxExecutor.callAjax = function (event) {
        event.preventDefault();
        var $this = $(this);
        var msg = Messenger();
        msg.run({
            progressMessage: $this.data('ajax-progress-message'),
            successMessage: $this.data('ajax-success-message'),
            errorMessage: $this.data('ajax-error-message')
        }, {
            url: $this.attr('href'),
            method: $this.data('ajax-method'),
            success: function () {
                if ($this.data('redirect-target')) {
                    NavigationSwitcher.navigate($this.data('redirect-target'), $($this.data('redirect-active-selector')));
                }
            }
        });
    };
    ;
    AjaxExecutor.init = function () {
        var $items = $('[data-ajax=true]');
        $items.off('click', AjaxExecutor.callAjax);
        $items.click(AjaxExecutor.callAjax);
    };
    return AjaxExecutor;
}());
AjaxExecutor.init();
//# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiQWpheEV4ZWN1dG9yLmpzIiwic291cmNlUm9vdCI6IiIsInNvdXJjZXMiOlsiQWpheEV4ZWN1dG9yLnRzIl0sIm5hbWVzIjpbXSwibWFwcGluZ3MiOiJBQUFBO0lBQUE7SUF5QkEsQ0FBQztJQWxCa0IscUJBQVEsR0FBdkIsVUFBd0IsS0FBSztRQUN6QixLQUFLLENBQUMsY0FBYyxFQUFFLENBQUM7UUFDdkIsSUFBSSxLQUFLLEdBQUcsQ0FBQyxDQUFDLElBQUksQ0FBQyxDQUFDO1FBQ3BCLElBQUksR0FBRyxHQUFRLFNBQVMsRUFBRSxDQUFDO1FBQzNCLEdBQUcsQ0FBQyxHQUFHLENBQUM7WUFDSixlQUFlLEVBQUUsS0FBSyxDQUFDLElBQUksQ0FBQyx1QkFBdUIsQ0FBQztZQUNwRCxjQUFjLEVBQUUsS0FBSyxDQUFDLElBQUksQ0FBQyxzQkFBc0IsQ0FBQztZQUNsRCxZQUFZLEVBQUUsS0FBSyxDQUFDLElBQUksQ0FBQyxvQkFBb0IsQ0FBQztTQUNqRCxFQUFFO1lBQ0MsR0FBRyxFQUFFLEtBQUssQ0FBQyxJQUFJLENBQUMsTUFBTSxDQUFDO1lBQ3ZCLE1BQU0sRUFBRSxLQUFLLENBQUMsSUFBSSxDQUFDLGFBQWEsQ0FBQztZQUNqQyxPQUFPLEVBQUU7Z0JBQ0wsRUFBRSxDQUFDLENBQUMsS0FBSyxDQUFDLElBQUksQ0FBQyxpQkFBaUIsQ0FBQyxDQUFDLENBQUMsQ0FBQztvQkFDaEMsa0JBQWtCLENBQUMsUUFBUSxDQUFDLEtBQUssQ0FBQyxJQUFJLENBQUMsaUJBQWlCLENBQUMsRUFBRSxDQUFDLENBQUMsS0FBSyxDQUFDLElBQUksQ0FBQywwQkFBMEIsQ0FBQyxDQUFDLENBQUMsQ0FBQztnQkFDMUcsQ0FBQztZQUNMLENBQUM7U0FDSixDQUFDLENBQUM7SUFDUCxDQUFDO0lBQUEsQ0FBQztJQXZCWSxpQkFBSSxHQUFHO1FBQ2pCLElBQUksTUFBTSxHQUFHLENBQUMsQ0FBQyxrQkFBa0IsQ0FBQyxDQUFDO1FBQ25DLE1BQU0sQ0FBQyxHQUFHLENBQUMsT0FBTyxFQUFFLFlBQVksQ0FBQyxRQUFRLENBQUMsQ0FBQztRQUMzQyxNQUFNLENBQUMsS0FBSyxDQUFDLFlBQVksQ0FBQyxRQUFRLENBQUMsQ0FBQztJQUN4QyxDQUFDLENBQUM7SUFvQk4sbUJBQUM7Q0FBQSxBQXpCRCxJQXlCQztBQUVELFlBQVksQ0FBQyxJQUFJLEVBQUUsQ0FBQyJ9