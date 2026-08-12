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

        $('.delete-service-btn').on('click', function () {
            var action = $(this).data('action');
            var serviceName = $(this).data('name') || 'Selected service';

            $('#delete-service-form').attr('action', action);
            $('#delete-service-name').text(serviceName);
            $('#deleteServiceModal').modal('show');
        });
    });
})(jQuery);
