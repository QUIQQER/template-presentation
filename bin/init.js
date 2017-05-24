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

    // show nav background after scroll
    // only, if nav is position fixed
    if (typeof navIsFixed !== 'undefined') {
        require(['qui/utils/Functions'], function (QUIFunctionUtils) {
            var navBar   = document.getElement('.header-bar-scroll'),
                hasClass = false;

            var onScroll = function () {

                if (window.getScroll().y > 100) {
                    if (!hasClass) {
                        navBar.addClass('nav-bar-scrolled');
                        hasClass = true;
                    }
                } else {
                    navBar.removeClass('nav-bar-scrolled');
                    hasClass = false;
                }
            };

            window.addEvents({
                    'scroll' : QUIFunctionUtils.debounce(onScroll, 30),
                    'load' : QUIFunctionUtils.debounce(onScroll, 30)
            });
        });
    }
});
