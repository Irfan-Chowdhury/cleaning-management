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
