class Overview {
    public static init = () => {
        let overview = document.querySelector('.galleries');
        if (overview) {
            let getListUrl = overview.getAttribute('data-get-list-url');
            let loadFailureMessage = overview.getAttribute('data-load-failure-message');
            ko.applyBindings(new OverviewViewModel(getListUrl, loadFailureMessage), overview);
        }
    }
}

$(function () {
    Overview.init();
});