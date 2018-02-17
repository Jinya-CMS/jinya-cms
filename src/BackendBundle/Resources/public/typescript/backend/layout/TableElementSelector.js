var TableElementSelector = /** @class */ (function () {
    function TableElementSelector() {
    }
    TableElementSelector.trClicked = function () {
        var $this = $(this);
        $("tr." + TableElementSelector.selectedClass)
            .removeClass(TableElementSelector.selectedClass);
        $this.addClass(TableElementSelector.selectedClass);
        $('body').data(TableElementSelector.selectedIdAttribute, $this.data('id'));
        $('.sidebar').removeClass('no-edit');
    };
    TableElementSelector.selectedClass = 'table-primary';
    TableElementSelector.selectedIdAttribute = 'selectedId';
    TableElementSelector.init = function () {
        var $table = $('.table tbody');
        $table.off('click', 'tr[data-id]', TableElementSelector.trClicked);
        $table.on('click', 'tr[data-id]', TableElementSelector.trClicked);
    };
    return TableElementSelector;
}());
TableElementSelector.init();
//# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiVGFibGVFbGVtZW50U2VsZWN0b3IuanMiLCJzb3VyY2VSb290IjoiIiwic291cmNlcyI6WyJUYWJsZUVsZW1lbnRTZWxlY3Rvci50cyJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiQUFBQTtJQUFBO0lBb0JBLENBQUM7SUFUa0IsOEJBQVMsR0FBeEI7UUFDSSxJQUFJLEtBQUssR0FBRyxDQUFDLENBQUMsSUFBSSxDQUFDLENBQUM7UUFDcEIsQ0FBQyxDQUFDLFFBQU0sb0JBQW9CLENBQUMsYUFBZSxDQUFDO2FBQ3hDLFdBQVcsQ0FBQyxvQkFBb0IsQ0FBQyxhQUFhLENBQUMsQ0FBQztRQUNyRCxLQUFLLENBQUMsUUFBUSxDQUFDLG9CQUFvQixDQUFDLGFBQWEsQ0FBQyxDQUFDO1FBRW5ELENBQUMsQ0FBQyxNQUFNLENBQUMsQ0FBQyxJQUFJLENBQUMsb0JBQW9CLENBQUMsbUJBQW1CLEVBQUUsS0FBSyxDQUFDLElBQUksQ0FBQyxJQUFJLENBQUMsQ0FBQyxDQUFDO1FBQzNFLENBQUMsQ0FBQyxVQUFVLENBQUMsQ0FBQyxXQUFXLENBQUMsU0FBUyxDQUFDLENBQUM7SUFDekMsQ0FBQztJQWpCc0Isa0NBQWEsR0FBRyxlQUFlLENBQUM7SUFDaEMsd0NBQW1CLEdBQUcsWUFBWSxDQUFDO0lBRTVDLHlCQUFJLEdBQUc7UUFDakIsSUFBSSxNQUFNLEdBQUcsQ0FBQyxDQUFDLGNBQWMsQ0FBQyxDQUFDO1FBQy9CLE1BQU0sQ0FBQyxHQUFHLENBQUMsT0FBTyxFQUFFLGFBQWEsRUFBRSxvQkFBb0IsQ0FBQyxTQUFTLENBQUMsQ0FBQztRQUNuRSxNQUFNLENBQUMsRUFBRSxDQUFDLE9BQU8sRUFBRSxhQUFhLEVBQUUsb0JBQW9CLENBQUMsU0FBUyxDQUFDLENBQUM7SUFDdEUsQ0FBQyxDQUFDO0lBV04sMkJBQUM7Q0FBQSxBQXBCRCxJQW9CQztBQUVELG9CQUFvQixDQUFDLElBQUksRUFBRSxDQUFDIn0=