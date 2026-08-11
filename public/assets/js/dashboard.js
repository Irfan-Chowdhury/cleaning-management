(function ($) {
    'use strict';

    $(function () {
        $('.copy-referral-btn').on('click', function () {
            var $button = $(this);
            var referralLink = $('#referralLink').text();

            if (navigator.clipboard) {
                navigator.clipboard.writeText(referralLink);
            }

            $button.text('Copied');

            window.setTimeout(function () {
                $button.text('Copy');
            }, 1400);
        });
    });
})(jQuery);
