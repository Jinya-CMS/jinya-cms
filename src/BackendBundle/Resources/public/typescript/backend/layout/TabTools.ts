class TabTools {
    public static init() {
        let $tabs = $('.nav[role=tablist]');
        let hash = location.hash;
        if (hash) {
            $(`[href="${hash}"]`).tab('show');
        } else {
            $('.nav[role=tablist] a:first').tab('show');
        }
        $('a[data-toggle=tab]').on('shown.bs.tab', (e) => {
            location.hash = $(e.target).attr('href');
        });
    }
}

$(() => {
    TabTools.init();
});