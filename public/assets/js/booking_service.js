(function ($) {
    'use strict';

    $(function () {
        function bindCounter(fieldSelector, counterSelector) {
            var $field = $(fieldSelector);
            var $counter = $(counterSelector);

            if (!$field.length || !$counter.length) {
                return;
            }

            function updateCounter() {
                $counter.text(($field.val() || '').length);
            }

            $field.on('input', updateCounter);
            updateCounter();
        }

        bindCounter('#booking-notes', '#booking-notes-count');
        bindCounter('#special-instructions', '#special-instructions-count');

        function escapeHtml(value) {
            return $('<div>').text(value || '').html();
        }

        function fieldName(question) {
            return 'questions[' + question.id + ']';
        }

        function renderSelect(question) {
            var html = '<select class="form-control" name="' + fieldName(question) + '"' + (question.required ? ' required' : '') + '>';

            html += '<option value="">Please Choose...</option>';
            $.each(question.options, function (index, option) {
                html += '<option value="' + escapeHtml(option.label) + '">' + escapeHtml(option.label) + '</option>';
            });

            return html + '</select>';
        }

        function renderCheckboxes(question) {
            var html = '<div class="row booking-checkbox-grid">';

            $.each(question.options, function (index, option) {
                var id = 'question_' + question.id + '_option_' + option.id;

                html += '' +
                    '<div class="col-sm-6 col-lg-4">' +
                        '<div class="custom-control custom-checkbox">' +
                            '<input type="checkbox" class="custom-control-input" id="' + id + '" name="' + fieldName(question) + '[]" value="' + escapeHtml(option.label) + '">' +
                            '<label class="custom-control-label" for="' + id + '">' + escapeHtml(option.label) + '</label>' +
                        '</div>' +
                    '</div>';
            });

            return html + '</div>';
        }

        function renderRadios(question) {
            var html = '<div class="row booking-checkbox-grid">';

            $.each(question.options, function (index, option) {
                var id = 'question_' + question.id + '_option_' + option.id;

                html += '' +
                    '<div class="col-sm-6 col-lg-4">' +
                        '<div class="custom-control custom-radio">' +
                            '<input type="radio" class="custom-control-input" id="' + id + '" name="' + fieldName(question) + '" value="' + escapeHtml(option.label) + '"' + (question.required ? ' required' : '') + '>' +
                            '<label class="custom-control-label" for="' + id + '">' + escapeHtml(option.label) + '</label>' +
                        '</div>' +
                    '</div>';
            });

            return html + '</div>';
        }

        function renderQuestionField(question) {
            var fieldType = (question.field_type || '').toLowerCase();

            if (fieldType === 'select' || fieldType === 'dropdown') {
                return renderSelect(question);
            }

            if (fieldType === 'checkbox') {
                return renderCheckboxes(question);
            }

            if (fieldType === 'radio') {
                return renderRadios(question);
            }

            if (fieldType === 'textarea') {
                return '<textarea class="form-control" name="' + fieldName(question) + '" rows="3"' + (question.required ? ' required' : '') + '></textarea>';
            }

            if (fieldType === 'number') {
                return '<input type="number" class="form-control" name="' + fieldName(question) + '"' + (question.required ? ' required' : '') + '>';
            }

            if (fieldType === 'date') {
                return '<input type="date" class="form-control" name="' + fieldName(question) + '"' + (question.required ? ' required' : '') + '>';
            }

            return '<input type="text" class="form-control" name="' + fieldName(question) + '"' + (question.required ? ' required' : '') + '>';
        }

        function renderQuestionnaire(response) {
            var $container = $('#booking-questionnaire');
            var questions = response.questions || [];
            var html = '';

            if (!questions.length) {
                $container.html(
                    '<div class="booking-questionnaire-empty">' +
                        '<i class="far fa-list-alt" aria-hidden="true"></i>' +
                        '<span>No questionnaire configured for this service.</span>' +
                    '</div>'
                );
                return;
            }

            html += '' +
                '<div class="booking-questionnaire-header">' +
                    '<h3>' + escapeHtml(response.service.name) + ' Questions</h3>' +
                    '<span>' + questions.length + ' questions</span>' +
                '</div>';

            $.each(questions, function (index, question) {
                html += '' +
                    '<div class="booking-question-item">' +
                        '<label>' +
                            '<span class="booking-question-number">' + (question.sort_order || (index + 1)) + '</span>' +
                            escapeHtml(question.title) +
                            (question.required ? ' <span>*</span>' : '') +
                        '</label>' +
                        renderQuestionField(question) +
                    '</div>';
            });

            $container.html(html);
        }

        $('#booking-service').on('change', function () {
            var serviceId = $(this).val();
            var endpoint = $(this).data('questionnaire-url');
            var $container = $('#booking-questionnaire');

            if (!serviceId) {
                $container.html(
                    '<div class="booking-questionnaire-empty">' +
                        '<i class="far fa-list-alt" aria-hidden="true"></i>' +
                        '<span>' + escapeHtml($container.data('empty-text')) + '</span>' +
                    '</div>'
                );
                return;
            }

            $container.html(
                '<div class="booking-questionnaire-empty">' +
                    '<i class="fas fa-spinner fa-spin" aria-hidden="true"></i>' +
                    '<span>Loading service questions...</span>' +
                '</div>'
            );

            $.ajax({
                url: endpoint + '/' + serviceId,
                method: 'GET',
                dataType: 'json'
            }).done(renderQuestionnaire).fail(function () {
                $container.html(
                    '<div class="booking-questionnaire-empty booking-questionnaire-error">' +
                        '<i class="fas fa-exclamation-circle" aria-hidden="true"></i>' +
                        '<span>Questions could not be loaded. Please try again.</span>' +
                    '</div>'
                );
            });
        });

        $('.calendar-days button:not(.outside-month)').on('click', function () {
            $('.calendar-days button').removeClass('selected');
            $(this).addClass('selected');
        });

        $('.time-slot').on('click', function () {
            $('.time-slot').removeClass('selected');
            $(this).addClass('selected');
        });

        $('input[name="detail_mode"]').on('change', function () {
            var mode = $(this).val();
            var isAccountMode = mode === 'account';

            $('.detail-mode-card').removeClass('active');
            $(this).closest('.detail-mode-card').addClass('active');
            $('.your-details-form').toggleClass('account-mode', isAccountMode);
            $('.booking-detail-input').prop('readonly', isAccountMode);
        });
    });
})(jQuery);
