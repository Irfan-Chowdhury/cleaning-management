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

        function updateServiceGuideCard(serviceId) {
            var $card = $('#service-guide-card');
            if (!$card.length) return;

            var servicesData = window.bookingServicesData || [];
            var selectedService = null;

            if (serviceId) {
                for (var i = 0; i < servicesData.length; i++) {
                    if (String(servicesData[i].id) === String(serviceId)) {
                        selectedService = servicesData[i];
                        break;
                    }
                }
            }

            var illustrationSvg = '' +
                '<svg width="120" height="110" viewBox="0 0 140 130" fill="none" xmlns="http://www.w3.org/2000/svg">' +
                    '<circle cx="70" cy="65" r="55" fill="#F0F7FF"/>' +
                    '<path d="M50 40H90C93.3137 40 96 42.6863 96 46V84C96 87.3137 93.3137 90 90 90H50C46.6863 90 44 87.3137 44 84V46C44 42.6863 46.6863 40 50 40Z" fill="#E1EFFE" stroke="#A4CAFE" stroke-width="2"/>' +
                    '<path d="M54 48H86V72H54V48Z" fill="#FFFFFF"/>' +
                    '<path d="M60 48L76 72" stroke="#D0E1FD" stroke-width="2" stroke-linecap="round"/>' +
                    '<path d="M72 48L84 66" stroke="#D0E1FD" stroke-width="2" stroke-linecap="round"/>' +
                    '<path d="M85 70L80 98H100L95 70H85Z" fill="#0866E8"/>' +
                    '<rect x="83" y="65" width="14" height="6" rx="3" fill="#0052CC"/>' +
                    '<path d="M90 65V55C90 52 94 52 94 55V65" stroke="#0052CC" stroke-width="3" stroke-linecap="round"/>' +
                    '<path d="M40 35L42 41L48 43L42 45L40 51L38 45L32 43L38 41L40 35Z" fill="#F59E0B"/>' +
                    '<path d="M102 30L103.5 34.5L108 36L103.5 37.5L102 42L100.5 37.5L96 36L100.5 34.5L102 30Z" fill="#60A5FA"/>' +
                    '<path d="M35 75L36.5 79.5L41 81L36.5 82.5L35 87L33.5 82.5L29 81L33.5 79.5L35 75Z" fill="#10B981"/>' +
                '</svg>';

            var infoBoxHtml = '' +
                '<div class="service-guide-info-box">' +
                    '<span class="info-box-shield-icon">' +
                        '<i class="fas fa-shield-alt" aria-hidden="true"></i>' +
                    '</span>' +
                    '<span>Customised cleaning plans available to suit your business needs.</span>' +
                '</div>';

            var html = '';

            if (selectedService) {
                html += '' +
                    '<div class="service-guide-header">' +
                        '<div class="service-guide-header-left">' +
                            '<span class="service-guide-icon-circle">' +
                                '<i class="fas fa-info-circle" aria-hidden="true"></i>' +
                            '</span>' +
                            '<h2 class="service-guide-title">About ' + escapeHtml(selectedService.name) + '</h2>' +
                        '</div>' +
                        '<span class="service-guide-badge">Step 1 of 4</span>' +
                    '</div>';

                if (selectedService.description) {
                    html += '<p class="service-guide-description">' + escapeHtml(selectedService.description) + '</p>';
                } else {
                    html += '<p class="service-guide-description text-muted">Professional cleaning service tailored to your requirements.</p>';
                }

                html += '<hr class="service-guide-divider">';

                html += '<div class="service-guide-content-area">';
                html += '<div class="whats-included-section">';
                html += '<h3 class="whats-included-heading">What\'s included</h3>';

                var items = selectedService.whats_included;
                if (typeof items === 'string') {
                    try { items = JSON.parse(items); } catch(e) { items = []; }
                }

                if (Array.isArray(items) && items.length > 0) {
                    html += '<ul class="whats-included-list">';
                    $.each(items, function(idx, item) {
                        html += '' +
                            '<li>' +
                                '<span class="check-icon-circle">' +
                                    '<i class="fas fa-check" aria-hidden="true"></i>' +
                                '</span>' +
                                '<span class="whats-included-text">' + escapeHtml(item) + '</span>' +
                            '</li>';
                    });
                    html += '</ul>';
                } else {
                    html += '<p class="text-muted small">No specific inclusions listed for this service.</p>';
                }
                html += '</div>';

                html += '<div class="service-guide-illustration">' + illustrationSvg + '</div>';
                html += '</div>';

                html += infoBoxHtml;
            } else {
                html += '' +
                    '<div class="service-guide-header">' +
                        '<div class="service-guide-header-left">' +
                            '<span class="service-guide-icon-circle">' +
                                '<i class="far fa-lightbulb" aria-hidden="true"></i>' +
                            '</span>' +
                            '<h2 class="service-guide-title">Service Guide</h2>' +
                        '</div>' +
                        '<span class="service-guide-badge">Step 1 of 4</span>' +
                    '</div>' +

                    '<div class="service-guide-default-intro">' +
                        '<h3>Not sure which cleaning service is right for you?</h3>' +
                        '<p>Select a service from the dropdown to view details, what\'s included, and package coverage.</p>' +
                    '</div>' +

                    '<hr class="service-guide-divider">' +

                    '<div class="service-guide-content-area">' +
                        '<div class="whats-included-section">' +
                            '<ul class="whats-included-list default-features-list">' +
                                '<li>' +
                                    '<span class="check-icon-circle">' +
                                        '<i class="fas fa-check" aria-hidden="true"></i>' +
                                    '</span>' +
                                    '<span class="whats-included-text">Tailored to your needs</span>' +
                                '</li>' +
                                '<li>' +
                                    '<span class="check-icon-circle">' +
                                        '<i class="fas fa-check" aria-hidden="true"></i>' +
                                    '</span>' +
                                    '<span class="whats-included-text">Upfront pricing</span>' +
                                '</li>' +
                                '<li>' +
                                    '<span class="check-icon-circle">' +
                                        '<i class="fas fa-check" aria-hidden="true"></i>' +
                                    '</span>' +
                                    '<span class="whats-included-text">Professional cleaners</span>' +
                                '</li>' +
                            '</ul>' +
                        '</div>' +
                        '<div class="service-guide-illustration">' + illustrationSvg + '</div>' +
                    '</div>' +

                    infoBoxHtml;
            }

            $card.html(html);
        }

        $('#booking-service').on('change', function () {
            var serviceId = $(this).val();
            var endpoint = $(this).data('questionnaire-url');
            var $container = $('#booking-questionnaire');

            updateServiceGuideCard(serviceId);

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

        if ($('#booking-service').val()) {
            $('#booking-service').trigger('change');
        }

        /* ==========================================================================
           Step 2: Interactive Calendar & Time Slots
           ========================================================================== */
        var $calendar = $('#interactive-calendar');
        if ($calendar.length) {
            var holidays = $calendar.data('holidays') || [];
            var slotsUrl = $calendar.data('slots-url');
            var todayStr = $calendar.data('today');
            var selectedDateStr = $calendar.data('selected-date') || todayStr;

            var todayObj = new Date(todayStr + 'T00:00:00');
            var currentYear = todayObj.getFullYear();
            var currentMonth = todayObj.getMonth(); // 0-indexed

            var selectedObj = new Date(selectedDateStr + 'T00:00:00');
            var viewYear = selectedObj.getFullYear();
            var viewMonth = selectedObj.getMonth();

            var monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                              'July', 'August', 'September', 'October', 'November', 'December'];

            function formatDateString(year, month, day) {
                var m = (month + 1 < 10 ? '0' : '') + (month + 1);
                var d = (day < 10 ? '0' : '') + day;
                return year + '-' + m + '-' + d;
            }

            function isHoliday(dateStr) {
                for (var i = 0; i < holidays.length; i++) {
                    var h = holidays[i];
                    if (dateStr >= h.start_date && dateStr <= h.end_date) {
                        return h.title;
                    }
                }
                return false;
            }

            function renderCalendar() {
                $('#calendar-month-year').text(monthNames[viewMonth] + ' ' + viewYear);

                // Disable prev month arrow if viewing current month/year
                var isCurrentMonthView = (viewYear === currentYear && viewMonth === currentMonth);
                $('#prev-month').prop('disabled', isCurrentMonthView);

                var $daysContainer = $('#calendar-days-container');
                $daysContainer.empty();

                // Get first day of month and total days
                var firstDay = new Date(viewYear, viewMonth, 1);
                var lastDay = new Date(viewYear, viewMonth + 1, 0);
                var totalDays = lastDay.getDate();

                // Monday-based day index (0 = Mon, ..., 6 = Sun)
                var startDayIndex = firstDay.getDay() - 1;
                if (startDayIndex < 0) startDayIndex = 6;

                // Previous month trailing days
                var prevMonthLastDay = new Date(viewYear, viewMonth, 0).getDate();
                for (var p = startDayIndex - 1; p >= 0; p--) {
                    var prevDayNum = prevMonthLastDay - p;
                    $daysContainer.append('<button type="button" class="outside-month" disabled>' + prevDayNum + '</button>');
                }

                // Current month days
                for (var day = 1; day <= totalDays; day++) {
                    var dateStr = formatDateString(viewYear, viewMonth, day);
                    var $btn = $('<button type="button"></button>').text(day).attr('data-date', dateStr);

                    var holidayTitle = isHoliday(dateStr);
                    var isPast = (dateStr < todayStr);

                    if (holidayTitle) {
                        $btn.addClass('holiday-date')
                            .attr('disabled', true)
                            .attr('title', 'Holiday: ' + holidayTitle)
                            .attr('data-toggle', 'tooltip');
                    } else if (isPast) {
                        $btn.addClass('disabled').attr('disabled', true).attr('title', 'Past date');
                    } else {
                        if (dateStr === selectedDateStr) {
                            $btn.addClass('selected');
                        }
                    }

                    $daysContainer.append($btn);
                }

                // Next month leading days to complete grid row
                var totalGridCells = startDayIndex + totalDays;
                var remainingCells = (7 - (totalGridCells % 7)) % 7;
                for (var n = 1; n <= remainingCells; n++) {
                    $daysContainer.append('<button type="button" class="outside-month" disabled>' + n + '</button>');
                }

                // Initialize tooltips if Bootstrap tooltip plugin available
                if ($.fn.tooltip) {
                    $('[data-toggle="tooltip"]').tooltip();
                }
            }

            function fetchTimeSlots(dateStr) {
                var $container = $('#time-slots-container');
                $container.html('<div class="text-muted p-3 text-center"><i class="fas fa-spinner fa-spin mr-1"></i> Loading available times...</div>');

                var currentSelectedSlot = $('#selected-start-time').val();

                $.ajax({
                    url: slotsUrl,
                    method: 'GET',
                    data: { date: dateStr },
                    dataType: 'json'
                }).done(function (res) {
                    $container.empty();

                    if (res.is_holiday) {
                        $container.html('<div class="alert alert-warning text-center small mb-0"><i class="fas fa-umbrella-beach mr-1"></i> Selected date is a holiday (' + escapeHtml(res.holiday_title) + '). No slots available.</div>');
                        $('#calendar-info-text').text('Holiday: ' + res.holiday_title);
                        return;
                    }

                    if (!res.is_day_active) {
                        $container.html('<div class="alert alert-secondary text-center small mb-0"><i class="fas fa-calendar-times mr-1"></i> Cleaning services are not available on ' + escapeHtml(res.day_of_week) + 's.</div>');
                        $('#calendar-info-text').text('Service unavailable on ' + res.day_of_week + 's');
                        return;
                    }

                    if (!res.slots || !res.slots.length) {
                        $container.html('<div class="alert alert-secondary text-center small mb-0"><i class="fas fa-clock mr-1"></i> No time slots configured for ' + escapeHtml(res.day_of_week) + 's.</div>');
                        $('#calendar-info-text').text('No slots configured for ' + res.day_of_week);
                        return;
                    }

                    $('#calendar-info-text').text('Showing available dates for ' + res.day_of_week);

                    $.each(res.slots, function (idx, slot) {
                        var $slotBtn = $('<button type="button" class="time-slot"></button>');
                        $slotBtn.attr('data-start-time', slot.start_time)
                                .attr('data-end-time', slot.end_time || '')
                                .attr('data-display-time', slot.display_time);

                        if (slot.is_booked) {
                            $slotBtn.addClass('booked')
                                    .attr('disabled', true)
                                    .attr('title', 'Already Booked')
                                    .html(escapeHtml(slot.display_time) + ' <small>(Booked)</small>');
                        } else {
                            $slotBtn.text(slot.display_time);
                            if (slot.start_time === currentSelectedSlot) {
                                $slotBtn.addClass('selected');
                            }
                        }

                        $container.append($slotBtn);
                    });
                }).fail(function () {
                    $container.html('<div class="alert alert-danger text-center small mb-0"><i class="fas fa-exclamation-triangle mr-1"></i> Failed to load time slots. Please try again.</div>');
                });
            }

            // Month navigation events
            $('#prev-month').on('click', function () {
                if (viewYear > currentYear || (viewYear === currentYear && viewMonth > currentMonth)) {
                    viewMonth--;
                    if (viewMonth < 0) {
                        viewMonth = 11;
                        viewYear--;
                    }
                    renderCalendar();
                }
            });

            $('#next-month').on('click', function () {
                viewMonth++;
                if (viewMonth > 11) {
                    viewMonth = 0;
                    viewYear++;
                }
                renderCalendar();
            });

            // Date selection click handler
            $(document).on('click', '#calendar-days-container button:not(.outside-month):not(:disabled):not(.holiday-date):not(.disabled)', function () {
                var clickedDate = $(this).attr('data-date');
                if (!clickedDate) return;

                selectedDateStr = clickedDate;
                $('#selected-booking-date').val(selectedDateStr);
                $('#selected-start-time').val('');
                $('#selected-end-time').val('');

                $('#calendar-days-container button').removeClass('selected');
                $(this).addClass('selected');

                fetchTimeSlots(selectedDateStr);
            });

            // Time slot selection click handler
            $(document).on('click', '.time-slot:not(:disabled):not(.booked)', function () {
                $('.time-slot').removeClass('selected');
                $(this).addClass('selected');

                var startTime = $(this).attr('data-start-time');
                var endTime = $(this).attr('data-end-time');

                $('#selected-start-time').val(startTime);
                $('#selected-end-time').val(endTime);
            });

            // Initial render and fetch
            renderCalendar();
            fetchTimeSlots(selectedDateStr);
        }

        $('input[name="detail_mode"]').on('change', function () {
            var mode = $(this).val();
            var isAccountMode = mode === 'account';
            var $form = $('.your-details-form');

            $('.detail-mode-card').removeClass('active');
            $(this).closest('.detail-mode-card').addClass('active');
            $form.toggleClass('account-mode', isAccountMode);

            if (isAccountMode) {
                // Populate inputs with current logged-in user data
                $('#full-name').val($form.attr('data-user-name') || '');
                $('#email-address').val($form.attr('data-user-email') || '');
                $('#phone-number').val($form.attr('data-user-phone') || '');
                $('#service-address').val($form.attr('data-user-address') || '');
                $('#unit-suite').val($form.attr('data-user-unit') || '');
                $('#suburb').val($form.attr('data-user-suburb') || '');
                $('#postcode').val($form.attr('data-user-postcode') || '');
                $('.booking-detail-input').prop('readonly', true);
                $('.saved-details-panel').slideDown(200);
            } else {
                // Clear all input fields to blank state for new details insertion
                $('#full-name').val('');
                $('#email-address').val('');
                $('#phone-number').val('');
                $('#service-address').val('');
                $('#unit-suite').val('');
                $('#suburb').val('');
                $('#postcode').val('');
                $('.booking-detail-input').prop('readonly', false);
                $('.saved-details-panel').slideUp(200);
            }
        });
    });
})(jQuery);
