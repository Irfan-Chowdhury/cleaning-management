(function ($) {
    'use strict';

    var sidebarOpenClass = 'sidebar-open';
    var desktopWidth = 992;
    var successToastShown = false;

    function openSidebar() {
        $('body').addClass(sidebarOpenClass);
    }

    function closeSidebar() {
        $('body').removeClass(sidebarOpenClass);
    }

    function cleanDesktopState() {
        if (window.innerWidth >= desktopWidth) {
            closeSidebar();
        }
    }

    function showSuccessToast() {
        var $message = $('#global-success-message');
        var message = window.AppFlash && window.AppFlash.success ? window.AppFlash.success : $message.attr('data-message');

        if (successToastShown || !message) {
            return;
        }

        if (typeof Swal === 'undefined') {
            window.setTimeout(showSuccessToast, 150);
            return;
        }

        successToastShown = true;

        Swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: function (toast) {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        }).fire({
            icon: "success",
            title: message
        });
    }

    function bindDeleteConfirmation() {
        $(document).on('click', '.js-delete-confirm', function () {
            var action = $(this).data('action');
            var $deleteForm = $('#global-delete-form');

            if (!action || !$deleteForm.length) {
                return;
            }

            if (typeof Swal === 'undefined') {
                $deleteForm.attr('action', action).trigger('submit');
                return;
            }

            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!"
            }).then(function (result) {
                if (result.isConfirmed) {
                    $deleteForm.attr('action', action);
                    $deleteForm.trigger('submit');
                }
            });
        });
    }

    window.showSuccessToast = showSuccessToast;

    $(function () {
        $('.sidebar-toggle').on('click', openSidebar);
        $('.sidebar-close, .sidebar-overlay').on('click', closeSidebar);

        $(document).on('keyup', function (event) {
            if (event.key === 'Escape') {
                closeSidebar();
            }
        });

        $(window).on('resize', cleanDesktopState);
        bindDeleteConfirmation();
        cleanDesktopState();
        showSuccessToast();
    });
})(jQuery);
