var Searcher = /** @class */ (function () {
    function Searcher() {
    }
    Searcher.performSearch = function (event) {
        if (currentTable) {
            event.preventDefault();
            var searchTerm = $(this).val().toString();
            currentTable
                .search(searchTerm)
                .draw();
        }
    };
    Searcher.init = function () {
        $('[data-table-search=true]').keyup(Searcher.performSearch);
    };
    return Searcher;
}());
Searcher.init();
//# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiU2VhcmNoZXIuanMiLCJzb3VyY2VSb290IjoiIiwic291cmNlcyI6WyJTZWFyY2hlci50cyJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiQUFBQTtJQUFBO0lBY0EsQ0FBQztJQVRVLHNCQUFhLEdBQXBCLFVBQXFCLEtBQUs7UUFDdEIsRUFBRSxDQUFDLENBQUMsWUFBWSxDQUFDLENBQUMsQ0FBQztZQUNmLEtBQUssQ0FBQyxjQUFjLEVBQUUsQ0FBQztZQUN2QixJQUFJLFVBQVUsR0FBRyxDQUFDLENBQUMsSUFBSSxDQUFDLENBQUMsR0FBRyxFQUFFLENBQUMsUUFBUSxFQUFFLENBQUM7WUFDMUMsWUFBWTtpQkFDUCxNQUFNLENBQUMsVUFBVSxDQUFDO2lCQUNsQixJQUFJLEVBQUUsQ0FBQztRQUNoQixDQUFDO0lBQ0wsQ0FBQztJQVphLGFBQUksR0FBRztRQUNqQixDQUFDLENBQUMsMEJBQTBCLENBQUMsQ0FBQyxLQUFLLENBQUMsUUFBUSxDQUFDLGFBQWEsQ0FBQyxDQUFDO0lBQ2hFLENBQUMsQ0FBQztJQVdOLGVBQUM7Q0FBQSxBQWRELElBY0M7QUFFRCxRQUFRLENBQUMsSUFBSSxFQUFFLENBQUMifQ==