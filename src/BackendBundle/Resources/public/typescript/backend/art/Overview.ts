class Overview {
    public static init = () => {
        let overview = document.querySelector('.overview-list');
        if (overview) {
            let getListUrl = overview.getAttribute('data-get-list-url');
            let loadFailureMessage = overview.getAttribute('data-load-failure-message');
            let viewModel = new OverviewViewModel(getListUrl, loadFailureMessage);
            let search = document.querySelector('[data-search=overview]');
            viewModel.search(search.querySelector('input[type=search]').getAttribute('value'));
            ko.cleanNode(overview);
            ko.cleanNode(search);
            ko.applyBindings(viewModel, overview);
            ko.applyBindings(viewModel, search);
            LabelFilter.init();
        }
    }
}

$(function () {
    Overview.init();
});