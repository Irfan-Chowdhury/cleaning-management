(function ($) {
    'use strict';

    $(function () {
        var $notes = $('#booking-notes');
        var $counter = $('#booking-notes-count');

        function updateNotesCounter() {
            $counter.text($notes.val().length);
        }

        $notes.on('input', updateNotesCounter);
        updateNotesCounter();

        $('#continue-to-date-time').on('click', function () {
            $(this).trigger('booking:continue-to-date-time');
        });
    });
})(jQuery);
