var LabelFilter = /** @class */ (function () {
    function LabelFilter() {
    }
    LabelFilter.init = function () {
        $('[data-action=apply-label]').click(function (event) {
            event.preventDefault();
            var $this = $(this);
            OverviewViewModel.CurrentVm.label($this.data('label'));
        });
    };
    return LabelFilter;
}());
//# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiTGFiZWxGaWx0ZXIuanMiLCJzb3VyY2VSb290IjoiIiwic291cmNlcyI6WyJMYWJlbEZpbHRlci50cyJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiQUFBQTtJQUFBO0lBUUEsQ0FBQztJQVBpQixnQkFBSSxHQUFHO1FBQ2pCLENBQUMsQ0FBQywyQkFBMkIsQ0FBQyxDQUFDLEtBQUssQ0FBQyxVQUFVLEtBQUs7WUFDaEQsS0FBSyxDQUFDLGNBQWMsRUFBRSxDQUFDO1lBQ3ZCLElBQUksS0FBSyxHQUFHLENBQUMsQ0FBQyxJQUFJLENBQUMsQ0FBQztZQUNwQixpQkFBaUIsQ0FBQyxTQUFTLENBQUMsS0FBSyxDQUFDLEtBQUssQ0FBQyxJQUFJLENBQUMsT0FBTyxDQUFDLENBQUMsQ0FBQztRQUMzRCxDQUFDLENBQUMsQ0FBQztJQUNQLENBQUMsQ0FBQTtJQUNMLGtCQUFDO0NBQUEsQUFSRCxJQVFDIn0=