class LabelFilter {
    public static init = () => {
        $('[data-action=apply-label]').click(function (event) {
            event.preventDefault();
            let $this = $(this);
            OverviewViewModel.CurrentVm.label($this.data('label'));
        });
    }
}

$(() => {
    LabelFilter.init();
});