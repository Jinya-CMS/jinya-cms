class TableElementSelector {

    public static readonly selectedClass = 'table-primary';
    public static readonly selectedIdAttribute = 'selectedId';

    public static init() {
        $('tr[data-id]').click(this.trClicked);
    }

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