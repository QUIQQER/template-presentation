window.addEvent("domready", function () {
    "use strict";

    require(['qui/utils/Functions'], function (QUIFunctionUtils) {
        if (document.getElements('[href=#top]')) {
            var toTop = document.getElements('[href=#top]'),
                hasClass = false;

            // scroll to top
            toTop.addEvent('click', function (event) {
                event.stop();
                new Fx.Scroll(window).toTop();
            });

            /**
             * show button toTop after scrolling down
             */
            var scroll = function () {
                if (window.getScroll().y > 300) {
                    if (!hasClass) {
                        toTop.addClass('toTop__show');
                    }
                    return;
                }
                toTop.removeClass('toTop__show');
                hasClass = false;
            };

            window.addEvents({
                'load'  : QUIFunctionUtils.debounce(scroll, 200),
                'scroll': QUIFunctionUtils.debounce(scroll, 200)
            })

        }
    });

// load QUI
    require(['qui/QUI'], function (QUI) {
        QUI.addEvent("onError", function (msg, url, linenumber) {
            console.error(msg);
            console.error(url);
            console.error('LineNo: ' + linenumber);
        });
    });

    /**
     * show nav background after scroll
     * works only if nav is position fixed
     */
    if (typeof navIsFixed !== 'undefined') {
        require(['qui/utils/Functions'], function (QUIFunctionUtils) {
            var headerBar = document.getElement('.header-bar'),
                hasClass  = false;

            var onScroll = function () {
                if (window.getScroll().y > 100) {
                    if (!hasClass) {
                        headerBar.addClass('header-bar-scrolled');
                        hasClass = true;
                    }
                } else {
                    headerBar.removeClass('header-bar-scrolled');
                    hasClass = false;
                }
            };set


            window.addEvents({
                'scroll': QUIFunctionUtils.debounce(onScroll, 30),
                'load'  : QUIFunctionUtils.debounce(onScroll, 30)
            });
        });
    }

    /**
     * show the search input after clicking on the icon
     */
    if (document.getElement('.header-bar-suggestSearch')) {

        var searchBar   = document.getElement('.header-bar-suggestSearch'),
            searchIcon  = searchBar.getElement('.fa-search'),
            searchInput = searchBar.getElement('input[type="search"]'),
            open        = false;

        searchIcon.addEvent('click', function (event) {
            event.stopPropagation();

            /* open */
            if (!open) {
                searchInput.addEvent('click', function (e) {
                    e.stopPropagation();
                });
                window.addEvent('click', function () {
                    searchBar.removeClass('showSearch');
                    open = false;
                    window.removeEvents('click');
                });

                searchBar.addClass('showSearch');
                searchInput.focus();
                open = true;
                return;
            }

            /* close */
            searchBar.removeClass('showSearch');
            open = false;
            window.removeEvents('click');
        })
    }
});
