window.addEvent("domready", function () {
    "use strict";

    document.getElements('[href=#top]').addEvent('click', function (event) {
        event.stop();
        new Fx.Scroll(window).toTop();
    });

// load QUI
    require(['qui/QUI'], function (QUI) {
        QUI.addEvent("onError", function (msg, url, linenumber) {
            console.error(msg);
            console.error(url);
            console.error('LineNo: ' + linenumber);
        });
    });

    if (typeof showEffect !== 'undefined') {
        require(['qui/utils/Functions'], function (QUIFunctionUtils) {
            var navBar   = document.getElement('.header-bar-scroll');
            var onScroll = function () {
                navBar.addEvent('click', function () {
                    navBar.addClass('nav-bar-scrolled');
                });

                if (window.getScroll().y > 100) {
                    navBar.addClass('nav-bar-scrolled');
                } else {
                    navBar.removeClass('nav-bar-scrolled');
                }
            };

            window.addEvent(
                'scroll',
                QUIFunctionUtils.debounce(onScroll, 20)
            );
        });
    }
});
