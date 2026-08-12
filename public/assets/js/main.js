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
        cleanDesktopState();
        showSuccessToast();
    });
})(jQuery);
