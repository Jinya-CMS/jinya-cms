class TableElementSelector {

    public static readonly selectedClass = 'table-primary';
    public static readonly selectedIdAttribute = 'selectedId';

    public static init = () => {
        let $table = $('.table tbody');
        $table.off('click', 'tr[data-id]', TableElementSelector.trClicked);
        $table.on('click', 'tr[data-id]', TableElementSelector.trClicked);
    };

    private static trClicked() {
        let $this = $(this);
        $(`tr.${TableElementSelector.selectedClass}`)
            .removeClass(TableElementSelector.selectedClass);
        $this.addClass(TableElementSelector.selectedClass);

        $('body').data(TableElementSelector.selectedIdAttribute, $this.data('id'));
        $('.sidebar').removeClass('no-edit');
    }
}

TableElementSelector.init();