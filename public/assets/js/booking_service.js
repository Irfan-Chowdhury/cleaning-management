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

        function getSavedValue(questionId, savedQuestions) {
            if (!savedQuestions || typeof savedQuestions !== 'object') {
                return null;
            }
            return savedQuestions[questionId] !== undefined ? savedQuestions[questionId] : null;
        }

        function renderSelect(question, savedValue) {
            var html = '<select class="form-control" name="' + fieldName(question) + '"' + (question.required ? ' required' : '') + '>';

            html += '<option value="">Please Choose...</option>';
            $.each(question.options, function (index, option) {
                var selected = (savedValue == option.label) ? ' selected' : '';
                html += '<option value="' + escapeHtml(option.label) + '"' + selected + '>' + escapeHtml(option.label) + '</option>';
            });

            return html + '</select>';
        }

        function renderCheckboxes(question, savedValue) {
            var html = '<div class="row booking-checkbox-grid">';
            var savedArray = Array.isArray(savedValue) ? savedValue : (savedValue ? [savedValue] : []);

            $.each(question.options, function (index, option) {
                var id = 'question_' + question.id + '_option_' + option.id;
                var checked = $.inArray(option.label, savedArray) !== -1 ? ' checked' : '';

                html += '' +
                    '<div class="col-sm-6 col-lg-4">' +
                        '<div class="custom-control custom-checkbox">' +
                            '<input type="checkbox" class="custom-control-input" id="' + id + '" name="' + fieldName(question) + '[]" value="' + escapeHtml(option.label) + '"' + checked + '>' +
                            '<label class="custom-control-label" for="' + id + '">' + escapeHtml(option.label) + '</label>' +
                        '</div>' +
                    '</div>';
            });

            return html + '</div>';
        }

        function renderRadios(question, savedValue) {
            var html = '<div class="row booking-checkbox-grid">';

            $.each(question.options, function (index, option) {
                var id = 'question_' + question.id + '_option_' + option.id;
                var checked = (savedValue == option.label) ? ' checked' : '';

                html += '' +
                    '<div class="col-sm-6 col-lg-4">' +
                        '<div class="custom-control custom-radio">' +
                            '<input type="radio" class="custom-control-input" id="' + id + '" name="' + fieldName(question) + '" value="' + escapeHtml(option.label) + '"' + (question.required ? ' required' : '') + checked + '>' +
                            '<label class="custom-control-label" for="' + id + '">' + escapeHtml(option.label) + '</label>' +
                        '</div>' +
                    '</div>';
            });

            return html + '</div>';
        }

        function renderQuestionField(question, savedValue) {
            var fieldType = (question.field_type || '').toLowerCase();
            var valStr = savedValue ? escapeHtml(savedValue) : '';

            if (fieldType === 'select' || fieldType === 'dropdown') {
                return renderSelect(question, savedValue);
            }

            if (fieldType === 'checkbox') {
                return renderCheckboxes(question, savedValue);
            }

            if (fieldType === 'radio') {
                return renderRadios(question, savedValue);
            }

            if (fieldType === 'textarea') {
                return '<textarea class="form-control" name="' + fieldName(question) + '" rows="3"' + (question.required ? ' required' : '') + '>' + valStr + '</textarea>';
            }

            if (fieldType === 'number') {
                return '<input type="number" class="form-control" name="' + fieldName(question) + '" value="' + valStr + '"' + (question.required ? ' required' : '') + '>';
            }

            if (fieldType === 'date') {
                return '<input type="date" class="form-control" name="' + fieldName(question) + '" value="' + valStr + '"' + (question.required ? ' required' : '') + '>';
            }

            return '<input type="text" class="form-control" name="' + fieldName(question) + '" value="' + valStr + '"' + (question.required ? ' required' : '') + '>';
        }

        function renderQuestionnaire(response) {
            var $container = $('#booking-questionnaire');
            var savedQuestions = $container.data('saved-questions') || {};
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
                var savedValue = getSavedValue(question.id, savedQuestions);
                html += '' +
                    '<div class="booking-question-item">' +
                        '<label>' +
                            '<span class="booking-question-number">' + (question.sort_order || (index + 1)) + '</span>' +
                            escapeHtml(question.title) +
                            (question.required ? ' <span>*</span>' : '') +
                        '</label>' +
                        renderQuestionField(question, savedValue) +
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

        // Trigger on load if a service is already pre-selected
        if ($('#booking-service').val()) {
            $('#booking-service').trigger('change');
        }

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
