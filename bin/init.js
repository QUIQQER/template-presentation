whenQuiLoaded().then(() => {    "use strict";

    /**
     * Handle click on a element with #target to perform scroll action
     * @param event
     */
    function handleScrollClick (event) {
        event.preventDefault();

        const href = this.getAttribute('href');

        if (href.length === 1) {
            return;
        }

        const Target = document.querySelector(this.getAttribute('href'));

        if (!Target) {
            return;
        }

        let offset = window.SCROLL_OFFSET ? window.SCROLL_OFFSET : 80;

        if (parseInt(Target.getAttribute('data-qui-offset')) ||
            parseInt(Target.getAttribute('data-qui-offset')) === 0) {
            offset = Target.getAttribute('data-qui-offset');
        }

        new Fx.Scroll(window, {
            offset: {
                y: -offset
            }
        }).toElement(Target);
    }

    // find all scroll links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        if (anchor.get('data-qui-disableTemplateScroll') === '1') {
            return;
        }
        anchor.addEventListener('click', handleScrollClick);
    });

    // scroll to anchor, not jump
    (function () {
        let queryString = window.location.hash;

        if (queryString && queryString.substr(0, 4) === '#go_') {
            const target = decodeURI(queryString.substr(4));
            const Target = document.querySelector('[href="#' + target + '"]');

            if (!Target) {
                return;
            }

            if (!isInViewport(Target)) {
                new Fx.Scroll(window, {
                    duration: 500
                }).toElement(Target);
            }
        }
    })();

    // load QUI
    require(['qui/QUI', 'utils/Controls'], function (QUI, Controls) {
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

        /**
         * social share buttons
         */

        if (social) {
            var SlideOutElm = document.getElement(
                '[data-qui="package/quiqqer/menu/bin/SlideOut"]'
            );

            if (SlideOutElm) {
                Controls.getControlByElement(SlideOutElm).then(function (SlideOutControl) {

                    var Elm = SlideOutControl.getElm();

                    new Element('div', {
                        'class': 'mobile-bar-social hide-on-desktop',
                        html   : socialHTML
                    }).inject(SlideOutElm);
                });
            }
        }
    });

    /**
     * show the search input after clicking on the icon
     */
    if (document.getElement('.header-bar-suggestSearch') &&
        document.getElement('.header-bar-suggestSearch').getElement('.fa-search')) {


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

                (function () {
                    searchInput.focus();
                }).delay(100);

                open = true;
                return;
            }

            /* close */
            searchBar.removeClass('showSearch');
            open = false;
            window.removeEvents('click');
        });
    }

    /**
     * Check if element is in viewport
     * @param Elm
     * @return {boolean}
     */
    function isInViewport(Elm) {
        const rect = Elm.getBoundingClientRect();
        return (
            rect.top >= 0 &&
            rect.left >= 0 &&
            rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
            rect.right <= (window.innerWidth || document.documentElement.clientWidth)
        );
    }
});
