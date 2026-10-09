(function ($) {
    'use strict';

    $(function () {
        $(document).on('click', '.copy-referral-btn', function (e) {
            e.preventDefault();
            var $button = $(this);

            if ($button.prop('disabled')) {
                return;
            }

            var referralLink = $.trim($('#referralLink').text());
            if (!referralLink) {
                return;
            }

            function fallbackCopyTextToClipboard(text) {
                var $tempInput = $('<textarea>');
                $tempInput.css({
                    position: 'absolute',
                    left: '-9999px',
                    top: '0'
                });
                $('body').append($tempInput);
                $tempInput.val(text).select();
                try {
                    document.execCommand('copy');
                } catch (err) {
                    console.error('Fallback: Unable to copy', err);
                }
                $tempInput.remove();
            }

            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(referralLink).then(function () {
                    showCopiedFeedback($button);
                }).catch(function () {
                    fallbackCopyTextToClipboard(referralLink);
                    showCopiedFeedback($button);
                });
            } else {
                fallbackCopyTextToClipboard(referralLink);
                showCopiedFeedback($button);
            }
        });

        function showCopiedFeedback($button) {
            var originalText = $button.text();
            $button.text('Copied!');

            window.setTimeout(function () {
                $button.text(originalText);
            }, 1400);
        }

        $(document).on('submit', '#referralInviteForm', function () {
            var $btn = $('#sendInviteBtn');
            if ($btn.length && !$btn.prop('disabled')) {
                $btn.html('<i class="fas fa-spinner fa-spin mr-1"></i> Sending...');
                $btn.addClass('disabled').css('pointer-events', 'none');
            }
        });
    });
})(jQuery);
