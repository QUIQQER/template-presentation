window.addEvent("domready", function () {
    "use strict";


    // load QUI
    require(['qui/QUI'], function (QUI) {
        QUI.addEvent("onError", function (msg, url, linenumber) {
            console.error(msg);
            console.error(url);
            console.error('LineNo: ' + linenumber);
        });

        /**
         * toTop button
         */
        if (document.getElements('[href=#top]')) {
            var toTop         = document.getElements('[href=#top]'),
                buttonVisible = false;

            // show on load
            if (QUI.getScroll().y > 300) {
                toTop.addClass('toTop__show');
                buttonVisible = true;
            }

            // show button toTop after scrolling down
            QUI.addEvent('scroll', function () {
                if (QUI.getScroll().y > 300) {
                    if (!buttonVisible) {
                        toTop.addClass('toTop__show');
                        buttonVisible = true;
                    }
                    return;
                }

                if (!buttonVisible) {
                    return;
                }
                toTop.removeClass('toTop__show');
                buttonVisible = false;
            });

            // scroll to top
            toTop.addEvent('click', function (event) {
                event.stop();
                new Fx.Scroll(window).toTop();
            });
        }

        /**
         * show nav background after scroll
         * works only if nav is position fixed
         */
        if (typeof navIsFixed !== 'undefined') {

            var headerBar     = document.getElement('.header-bar'),
                navBackground = false;

            // background on load
            if (QUI.getScroll().y > 300) {
                headerBar.addClass('header-bar-scrolled');
                navBackground = true;
            }

            QUI.addEvent('scroll', function () {
                if (QUI.getScroll().y > 50) {
                    if (!navBackground) {
                        headerBar.addClass('header-bar-scrolled');
                        navBackground = true;
                    }
                    return;
                }
                headerBar.removeClass('header-bar-scrolled');
                navBackground = false;

            });
        }
    });

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

    var menu       = document.getElement('.quiqqer-menu-megaMenu').getElement('.hide-on-desktop'),
        openSocial = menu.getElement('.open-social-share');

    if (openSocial) {
        var socialBar   = menu.getElement('.header-bar-social'),
            closeSocial = menu.getElement('.close-social-share');

        openSocial.addEvent('click', function () {
            socialBar.addClass('open');
        });

        closeSocial.addEvent('click', function () {
            socialBar.removeClass('open');
        });
    }
});
