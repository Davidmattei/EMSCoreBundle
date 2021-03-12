import dt from 'datatables.net-bs';
global.$.DataTable = dt;

export default class Table {
    constructor() {
        $.fn.dataTable.render.test = function () {
            return function ( data, type, row ) {
                // console.debug(data);
                // console.debug(data);
                // console.debug(data);

                return 'coool';
            };
        }
    }

    dataTable(target) {
        const config = {};

        // let ajaxUrl = $(target).data('ajax-url');
        //
        // if (typeof ajaxUrl !== 'undefined') {
        //     config.ajax = {
        //         'url': ajaxUrl
        //     }
        // }

        console.debug(config);

        $(target).DataTable({});
    }
}