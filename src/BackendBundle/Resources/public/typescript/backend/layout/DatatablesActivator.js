var currentTable = null;
var DatatablesActivator = /** @class */ (function () {
    function DatatablesActivator() {
        this.dateTimeFormat = function (data, type, row) {
            // Split timestamp into [ Y, M, D, h, m, s ]
            var timeStamp = data['date'].split(/[- :]/);
            // Apply each element to the Date function
            var date = new Date(Date.UTC(parseInt(timeStamp[0]), parseInt(timeStamp[1]) - 1, parseInt(timeStamp[2]), parseInt(timeStamp[3]), parseInt(timeStamp[4]), parseInt(timeStamp[5])));
            return date.toLocaleString();
        };
    }
    DatatablesActivator.init = function () {
        var $table = $('table[data-tables=true]');
        var $columns = $table.find('thead th');
        var getServiceUrl = $table.data('service-url');
        var columns = [];
        var columnDefs = [];
        var activator = new DatatablesActivator();
        $columns.each(function (idx, element) {
            var $this = $(element);
            columns.push({
                name: $this.data('name'),
                data: $this.data('data'),
                sortable: $this.data('sortable') != false
            });
            if (activator[$this.data('format')]) {
                columnDefs.push({
                    render: activator[$this.data('format')],
                    targets: idx
                });
            }
        });
        var sortBy = columns.indexOf(columns.find(function (value, index, obj) {
            return value['name'] == $table.data('sort-by');
        }));
        currentTable = $table.DataTable({
            dom: 'trip',
            search: {
                search: $table.data('filter'),
                regex: false
            },
            searching: true,
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
    };
    return DatatablesActivator;
}());
$(function () {
    DatatablesActivator.init();
});
//# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiRGF0YXRhYmxlc0FjdGl2YXRvci5qcyIsInNvdXJjZVJvb3QiOiIiLCJzb3VyY2VzIjpbIkRhdGF0YWJsZXNBY3RpdmF0b3IudHMiXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6IkFBQUEsSUFBSSxZQUFZLEdBQW1CLElBQUksQ0FBQztBQUV4QztJQUFBO1FBQ0ksbUJBQWMsR0FBRyxVQUFDLElBQUksRUFBRSxJQUFJLEVBQUUsR0FBRztZQUM3Qiw0Q0FBNEM7WUFDNUMsSUFBSSxTQUFTLEdBQUcsSUFBSSxDQUFDLE1BQU0sQ0FBQyxDQUFDLEtBQUssQ0FBQyxPQUFPLENBQUMsQ0FBQztZQUU1QywwQ0FBMEM7WUFDMUMsSUFBSSxJQUFJLEdBQUcsSUFBSSxJQUFJLENBQUMsSUFBSSxDQUFDLEdBQUcsQ0FBQyxRQUFRLENBQUMsU0FBUyxDQUFDLENBQUMsQ0FBQyxDQUFDLEVBQUUsUUFBUSxDQUFDLFNBQVMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxHQUFHLENBQUMsRUFBRSxRQUFRLENBQUMsU0FBUyxDQUFDLENBQUMsQ0FBQyxDQUFDLEVBQUUsUUFBUSxDQUFDLFNBQVMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxFQUFFLFFBQVEsQ0FBQyxTQUFTLENBQUMsQ0FBQyxDQUFDLENBQUMsRUFBRSxRQUFRLENBQUMsU0FBUyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDO1lBRWxMLE1BQU0sQ0FBQyxJQUFJLENBQUMsY0FBYyxFQUFFLENBQUM7UUFDakMsQ0FBQyxDQUFDO0lBa0ROLENBQUM7SUFoRGlCLHdCQUFJLEdBQUc7UUFDakIsSUFBSSxNQUFNLEdBQUcsQ0FBQyxDQUFDLHlCQUF5QixDQUFDLENBQUM7UUFDMUMsSUFBSSxRQUFRLEdBQUcsTUFBTSxDQUFDLElBQUksQ0FBQyxVQUFVLENBQUMsQ0FBQztRQUN2QyxJQUFJLGFBQWEsR0FBRyxNQUFNLENBQUMsSUFBSSxDQUFDLGFBQWEsQ0FBQyxDQUFDO1FBQy9DLElBQUksT0FBTyxHQUFHLEVBQUUsQ0FBQztRQUNqQixJQUFJLFVBQVUsR0FBRyxFQUFFLENBQUM7UUFDcEIsSUFBSSxTQUFTLEdBQUcsSUFBSSxtQkFBbUIsRUFBRSxDQUFDO1FBRTFDLFFBQVEsQ0FBQyxJQUFJLENBQUMsVUFBQyxHQUFHLEVBQUUsT0FBTztZQUN2QixJQUFJLEtBQUssR0FBRyxDQUFDLENBQUMsT0FBTyxDQUFDLENBQUM7WUFDdkIsT0FBTyxDQUFDLElBQUksQ0FBQztnQkFDVCxJQUFJLEVBQUUsS0FBSyxDQUFDLElBQUksQ0FBQyxNQUFNLENBQUM7Z0JBQ3hCLElBQUksRUFBRSxLQUFLLENBQUMsSUFBSSxDQUFDLE1BQU0sQ0FBQztnQkFDeEIsUUFBUSxFQUFFLEtBQUssQ0FBQyxJQUFJLENBQUMsVUFBVSxDQUFDLElBQUksS0FBSzthQUM1QyxDQUFDLENBQUM7WUFDSCxFQUFFLENBQUMsQ0FBQyxTQUFTLENBQUMsS0FBSyxDQUFDLElBQUksQ0FBQyxRQUFRLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQztnQkFDbEMsVUFBVSxDQUFDLElBQUksQ0FBQztvQkFDWixNQUFNLEVBQUUsU0FBUyxDQUFDLEtBQUssQ0FBQyxJQUFJLENBQUMsUUFBUSxDQUFDLENBQUM7b0JBQ3ZDLE9BQU8sRUFBRSxHQUFHO2lCQUNmLENBQUMsQ0FBQztZQUNQLENBQUM7UUFDTCxDQUFDLENBQUMsQ0FBQztRQUVILElBQUksTUFBTSxHQUFHLE9BQU8sQ0FBQyxPQUFPLENBQUMsT0FBTyxDQUFDLElBQUksQ0FBQyxVQUFDLEtBQUssRUFBRSxLQUFLLEVBQUUsR0FBRztZQUN4RCxNQUFNLENBQUMsS0FBSyxDQUFDLE1BQU0sQ0FBQyxJQUFJLE1BQU0sQ0FBQyxJQUFJLENBQUMsU0FBUyxDQUFDLENBQUE7UUFDbEQsQ0FBQyxDQUFDLENBQUMsQ0FBQztRQUNKLFlBQVksR0FBRyxNQUFNLENBQUMsU0FBUyxDQUFDO1lBQzVCLEdBQUcsRUFBRSxNQUFNO1lBQ1gsTUFBTSxFQUFFO2dCQUNKLE1BQU0sRUFBRSxNQUFNLENBQUMsSUFBSSxDQUFDLFFBQVEsQ0FBQztnQkFDN0IsS0FBSyxFQUFFLEtBQUs7YUFDZjtZQUNELFNBQVMsRUFBRSxJQUFJO1lBQ2YsVUFBVSxFQUFFLElBQUk7WUFDaEIsVUFBVSxFQUFFLElBQUk7WUFDaEIsSUFBSSxFQUFFO2dCQUNGLEdBQUcsRUFBRSxhQUFhO2dCQUNsQixNQUFNLEVBQUUsTUFBTTthQUNqQjtZQUNELE9BQU8sRUFBRSxPQUFPO1lBQ2hCLFVBQVUsRUFBRSxVQUFVO1lBQ3RCLEtBQUssRUFBRSxDQUFDLENBQUMsTUFBTSxFQUFFLE1BQU0sQ0FBQyxJQUFJLENBQUMsWUFBWSxDQUFDLENBQUMsQ0FBQztZQUM1QyxVQUFVLEVBQUUsVUFBVSxHQUFHLEVBQUUsSUFBSSxFQUFFLEtBQUs7Z0JBQ2xDLENBQUMsQ0FBQyxHQUFHLENBQUMsQ0FBQyxJQUFJLENBQUMsU0FBUyxFQUFFLElBQUksQ0FBQyxJQUFJLENBQUMsQ0FBQyxDQUFDO2dCQUNuQyxvQkFBb0IsQ0FBQyxJQUFJLEVBQUUsQ0FBQztZQUNoQyxDQUFDO1NBQ0osQ0FBQyxDQUFDO0lBQ1AsQ0FBQyxDQUFBO0lBQ0wsMEJBQUM7Q0FBQSxBQTNERCxJQTJEQztBQUVELENBQUMsQ0FBQztJQUNFLG1CQUFtQixDQUFDLElBQUksRUFBRSxDQUFDO0FBQy9CLENBQUMsQ0FBQyxDQUFDIn0=