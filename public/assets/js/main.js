(function ($) {
    'use strict';

    var sidebarOpenClass = 'sidebar-open';
    var desktopWidth = 992;

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
    });
})(jQuery);
