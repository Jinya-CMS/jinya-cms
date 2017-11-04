class AjaxExecutor {
    public static init = () => {
        let $items = $('[data-ajax=true]');
        $items.off('click', AjaxExecutor.callAjax);
        $items.click(AjaxExecutor.callAjax);
    };

    private static callAjax() {
        event.preventDefault();
        let $this = $(this);
        let msg: any = Messenger();
        msg.run({
            progressMessage: $this.data('ajax-progress-message'),
            successMessage: $this.data('ajax-success-message'),
            errorMessage: $this.data('ajax-error-message')
        }, {
            url: $this.attr('href'),
            method: $this.data('ajax-method'),
            success: () => {
                if ($this.data('redirect-target')) {
                    NavigationSwitcher.navigate($this.data('redirect-target'), $($this.data('redirect-active-selector')));
                }
            }
        });
    };
}

AjaxExecutor.init();