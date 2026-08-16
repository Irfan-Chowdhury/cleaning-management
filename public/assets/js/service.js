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

        var $serviceModal = $('#service-form-modal');
        var $serviceForm = $('#service-modal-form');
        var $serviceMethod = $('#service-form-method');
        var $serviceSubmit = $('#service-modal-submit');

        function resetServiceForm() {
            if (!$serviceForm.length) {
                return;
            }

            $serviceForm[0].reset();
            $serviceForm.find('.is-invalid').removeClass('is-invalid');
            $serviceForm.find('.invalid-feedback').hide();
            $('#status').val('active');
        }

        $('.js-service-add').on('click', function () {
            resetServiceForm();
            $('#service-form-modal-title').text('Add Service');
            $serviceForm.attr('action', $(this).data('action'));
            $serviceMethod.prop('disabled', true);
            $serviceSubmit.html('<i class="fas fa-save" aria-hidden="true"></i> Save Service');
        });

        $(document).on('click', '.js-service-edit', function () {
            var $button = $(this);

            resetServiceForm();
            $('#service-form-modal-title').text('Edit Service');
            $serviceForm.attr('action', $button.data('action'));
            $serviceMethod.val('PUT').prop('disabled', false);
            $('#name').val($button.data('name') || '');
            $('#description').val($button.data('description') || '');
            $('#status').val($button.data('status') || 'active');
            $serviceSubmit.html('<i class="fas fa-save" aria-hidden="true"></i> Update Service');
        });

        if ($serviceModal.length && $serviceForm.find('.is-invalid').length) {
            $serviceModal.modal('show');
        }
    });
})(jQuery);
