(function ($) {
    'use strict';

    $(function () {
        var $servicesTable = $('#services-table');

        if ($servicesTable.length && $.fn.DataTable && !$.fn.DataTable.isDataTable($servicesTable)) {
            $servicesTable.DataTable({
                pageLength: 10,
                lengthChange: true,
                searching: true,
                ordering: true,
                responsive: true,
                autoWidth: false,
                columnDefs: [
                    { orderable: false, targets: -1 }
                ],
                language: {
                    search: '',
                    searchPlaceholder: 'Search services...'
                }
            });
        }

    });
})(jQuery);
