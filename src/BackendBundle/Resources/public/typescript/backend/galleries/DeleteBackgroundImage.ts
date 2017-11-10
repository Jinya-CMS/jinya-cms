class DeleteBackgroundImage {
    public static init() {
        let $ajaxDelete = $('[data-ajax]');
        $ajaxDelete.click(() => {
            event.preventDefault();
            $ajaxDelete.parents('.row').hide();
            $.ajax($ajaxDelete.data('action'), {
                method: $ajaxDelete.data('method')
            }).then(() => {
            }, () => {
                $ajaxDelete.parents('.row').show();
            });
        });
    }
}

$(() => {
    DeleteBackgroundImage.init();
});