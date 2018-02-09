class Searcher {
    public static init = () => {
        $('[data-table-search=true]').keyup(Searcher.performSearch);
    };

    static performSearch(event) {
        if (currentTable) {
            event.preventDefault();
            let searchTerm = $(this).val().toString();
            currentTable
                .search(searchTerm)
                .draw();
        }
    }
}

Searcher.init();