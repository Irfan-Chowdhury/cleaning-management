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

        function createIncludedRow(value) {
            var $row = $(
                '<div class="included-feature-row">' +
                    '<input type="text" name="whats_included[]" class="form-control" placeholder="e.g. Dusting and wiping all reachable surfaces">' +
                    '<button type="button" class="included-feature-remove-btn js-remove-included-feature" title="Delete feature">' +
                        '<i class="fas fa-trash" aria-hidden="true"></i>' +
                    '</button>' +
                '</div>'
            );

            if (typeof value === 'string' && value.length > 0) {
                $row.find('input').val(value);
            }

            return $row;
        }

        function populateWhatsIncluded(items) {
            var $container = $('#whats-included-list');
            if (!$container.length) {
                return;
            }

            $container.empty();

            if (Array.isArray(items) && items.length > 0) {
                $.each(items, function (i, item) {
                    $container.append(createIncludedRow(item));
                });
            } else {
                $container.append(createIncludedRow(''));
            }
        }

        function resetServiceForm() {
            if (!$serviceForm.length) {
                return;
            }

            $serviceForm[0].reset();
            $serviceForm.find('.is-invalid').removeClass('is-invalid');
            $serviceForm.find('.invalid-feedback').hide();
            $('#status').val('active');
            populateWhatsIncluded([]);
        }

        // Add Feature button (+) click
        $(document).on('click', '#js-add-included-feature', function (e) {
            e.preventDefault();
            e.stopPropagation();

            var $container = $('#whats-included-list');
            if ($container.length) {
                var $row = createIncludedRow('');
                $container.append($row);
                $row.find('input').focus();
            }
        });

        // Remove Feature button click
        $(document).on('click', '.js-remove-included-feature', function (e) {
            e.preventDefault();
            e.stopPropagation();

            var $container = $('#whats-included-list');
            var count = $container.find('.included-feature-row').length;

            if (count > 1) {
                $(this).closest('.included-feature-row').remove();
            } else {
                $(this).closest('.included-feature-row').find('input').val('');
            }
        });

        // Add Service Modal trigger
        $('.js-service-add').on('click', function () {
            resetServiceForm();
            $('#service-form-modal-title').text('Add Service');
            $serviceForm.attr('action', $(this).data('action'));
            $serviceMethod.prop('disabled', true);
            $serviceSubmit.html('<i class="fas fa-save" aria-hidden="true"></i> Save Service');
        });

        // Edit Service Modal trigger
        $(document).on('click', '.js-service-edit', function () {
            var $button = $(this);

            resetServiceForm();
            $('#service-form-modal-title').text('Edit Service');
            $serviceForm.attr('action', $button.data('action'));
            $serviceMethod.val('PUT').prop('disabled', false);
            $('#name').val($button.data('name') || '');
            $('#description').val($button.data('description') || '');
            $('#status').val($button.data('status') || 'active');

            var rawIncluded = $button.data('whats-included');
            var includedArray = [];
            if (typeof rawIncluded === 'string') {
                try {
                    includedArray = JSON.parse(rawIncluded);
                } catch (e) {
                    includedArray = [];
                }
            } else if (Array.isArray(rawIncluded)) {
                includedArray = rawIncluded;
            }

            populateWhatsIncluded(includedArray);

            $serviceSubmit.html('<i class="fas fa-save" aria-hidden="true"></i> Update Service');
        });

        if ($serviceModal.length && $serviceForm.find('.is-invalid').length) {
            $serviceModal.modal('show');
        }

        if ($.fn.tooltip) {
            $('[data-toggle="tooltip"]').tooltip();
        }
    });
})(jQuery);
