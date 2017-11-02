declare let getServiceUrl: string;

class DatatablesActivator {
    public init = () => {
        let $table = $('table[data-tables=true]');
        $table.DataTable({
            dom: 'trip',
            ajax: {
                url: getServiceUrl,
                method: 'POST'
            }
        });
    }
}

$(() => {
    let datatablesActivator = new DatatablesActivator();
    datatablesActivator.init();
});