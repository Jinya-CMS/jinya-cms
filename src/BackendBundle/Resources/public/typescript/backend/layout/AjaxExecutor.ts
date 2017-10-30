class AjaxExecutor {
    public init = () => {
        let $items = $('[data-ajax=true]');
        $items.click(this.callAjax);
    };

    callAjax() {
        event.preventDefault();
        let $this = $(this);
        let msg: any = Messenger();
        msg.run({
            progressMessage: $this.data('ajax-progress-message'),
            successMessage: $this.data('ajax-success-message'),
            errorMessage: $this.data('ajax-error-message')
        }, {
            url: $this.attr('href'),
            method: $this.data('ajax-method')
        });
    };
}

let ajaxExecutor = new AjaxExecutor();
ajaxExecutor.init();