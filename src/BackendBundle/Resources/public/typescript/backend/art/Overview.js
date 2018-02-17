var Overview = /** @class */ (function () {
    function Overview() {
    }
    Overview.init = function () {
        var overview = document.querySelector('.overview-list');
        if (overview) {
            var getListUrl = overview.getAttribute('data-get-list-url');
            var loadFailureMessage = overview.getAttribute('data-load-failure-message');
            var viewModel = new OverviewViewModel(getListUrl, loadFailureMessage);
            var search = document.querySelector('[data-search=overview]');
            viewModel.search(search.querySelector('input[type=search]').getAttribute('value'));
            ko.cleanNode(overview);
            ko.cleanNode(search);
            ko.applyBindings(viewModel, overview);
            ko.applyBindings(viewModel, search);
            LabelFilter.init();
        }
    };
    return Overview;
}());
$(function () {
    Overview.init();
});
//# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiT3ZlcnZpZXcuanMiLCJzb3VyY2VSb290IjoiIiwic291cmNlcyI6WyJPdmVydmlldy50cyJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiQUFBQTtJQUFBO0lBZ0JBLENBQUM7SUFmaUIsYUFBSSxHQUFHO1FBQ2pCLElBQUksUUFBUSxHQUFHLFFBQVEsQ0FBQyxhQUFhLENBQUMsZ0JBQWdCLENBQUMsQ0FBQztRQUN4RCxFQUFFLENBQUMsQ0FBQyxRQUFRLENBQUMsQ0FBQyxDQUFDO1lBQ1gsSUFBSSxVQUFVLEdBQUcsUUFBUSxDQUFDLFlBQVksQ0FBQyxtQkFBbUIsQ0FBQyxDQUFDO1lBQzVELElBQUksa0JBQWtCLEdBQUcsUUFBUSxDQUFDLFlBQVksQ0FBQywyQkFBMkIsQ0FBQyxDQUFDO1lBQzVFLElBQUksU0FBUyxHQUFHLElBQUksaUJBQWlCLENBQUMsVUFBVSxFQUFFLGtCQUFrQixDQUFDLENBQUM7WUFDdEUsSUFBSSxNQUFNLEdBQUcsUUFBUSxDQUFDLGFBQWEsQ0FBQyx3QkFBd0IsQ0FBQyxDQUFDO1lBQzlELFNBQVMsQ0FBQyxNQUFNLENBQUMsTUFBTSxDQUFDLGFBQWEsQ0FBQyxvQkFBb0IsQ0FBQyxDQUFDLFlBQVksQ0FBQyxPQUFPLENBQUMsQ0FBQyxDQUFDO1lBQ25GLEVBQUUsQ0FBQyxTQUFTLENBQUMsUUFBUSxDQUFDLENBQUM7WUFDdkIsRUFBRSxDQUFDLFNBQVMsQ0FBQyxNQUFNLENBQUMsQ0FBQztZQUNyQixFQUFFLENBQUMsYUFBYSxDQUFDLFNBQVMsRUFBRSxRQUFRLENBQUMsQ0FBQztZQUN0QyxFQUFFLENBQUMsYUFBYSxDQUFDLFNBQVMsRUFBRSxNQUFNLENBQUMsQ0FBQztZQUNwQyxXQUFXLENBQUMsSUFBSSxFQUFFLENBQUM7UUFDdkIsQ0FBQztJQUNMLENBQUMsQ0FBQTtJQUNMLGVBQUM7Q0FBQSxBQWhCRCxJQWdCQztBQUVELENBQUMsQ0FBQztJQUNFLFFBQVEsQ0FBQyxJQUFJLEVBQUUsQ0FBQztBQUNwQixDQUFDLENBQUMsQ0FBQyJ9