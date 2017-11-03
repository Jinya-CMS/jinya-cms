class DatatablesActivator {
    dateTimeFormat = (data, type, row) => {
        // Split timestamp into [ Y, M, D, h, m, s ]
        let timeStamp = data['date'].split(/[- :]/);

        // Apply each element to the Date function
        let date = new Date(Date.UTC(parseInt(timeStamp[0]), parseInt(timeStamp[1]) - 1, parseInt(timeStamp[2]), parseInt(timeStamp[3]), parseInt(timeStamp[4]), parseInt(timeStamp[5])));

        return date.toLocaleString();
    };

    public init = () => {
        let $table = $('table[data-tables=true]');
        let $columns = $table.find('thead th');
        let getServiceUrl = $table.data('service-url');
        let columns = [];
        let columnDefs = [];

        $columns.each((idx, element) => {
            let $this = $(element);
            columns.push({
                name: $this.data('name'),
                data: $this.data('data')
            });
            if (this[$this.data('format')]) {
                columnDefs.push({
                    render: this[$this.data('format')],
                    targets: idx
                });
            }
        });

        let sortBy = columns.indexOf(columns.find((value, index, obj) => {
            return value['name'] == $table.data('sort-by')
        }));
        $table.DataTable({
            dom: 'trip',
            processing: true,
            serverSide: true,
            ajax: {
                url: getServiceUrl,
                method: 'POST'
            },
            columns: columns,
            columnDefs: columnDefs,
            order: [[sortBy, $table.data('sort-order')]],
            createdRow: function (row, data, index) {
                $(row).attr('data-id', data['id']);
                TableElementSelector.init();
            }
        });
    }
}

$(() => {
    let datatablesActivator = new DatatablesActivator();
    datatablesActivator.init();
});