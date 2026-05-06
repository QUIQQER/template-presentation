whenQuiLoaded().then(() => {
    "use strict";

    const defaultScrollOffset = window.SCROLL_OFFSET ? window.SCROLL_OFFSET : 80;
    const contentTableScrollEnabled = typeof CONTENT_TABLE_SCROLL !== 'undefined' && CONTENT_TABLE_SCROLL === 1;
    const scrollableMaskTolerance = 1;
    let scrollOffset = defaultScrollOffset;

    function wrapDefaultContentTables() {
        if (!contentTableScrollEnabled) {
            return;
        }

        document.querySelectorAll('.default-content table').forEach(table => {
            if (
                table.classList.contains('no-table-scroll') ||
                table.getAttribute('data-qui-disable-table-scroll') === '1' ||
                table.closest('.template-table-scroll')
            ) {
                return;
            }

            const wrapper = document.createElement('div');

            wrapper.className = 'template-table-scroll';

            table.parentNode.insertBefore(wrapper, table);
            wrapper.appendChild(table);
        });
    }

    function updateScrollableMask(wrapper) {
        const hasOverflow = wrapper.scrollWidth > wrapper.clientWidth + scrollableMaskTolerance;
        const isAtStart = wrapper.scrollLeft <= scrollableMaskTolerance;
        const isAtEnd = wrapper.scrollLeft + wrapper.clientWidth >=
            wrapper.scrollWidth - scrollableMaskTolerance;

        wrapper.style.setProperty('--scrollable-mask-start', hasOverflow && !isAtStart ? 'var(--scrollable-mask-size)' : '0px');
        wrapper.style.setProperty('--scrollable-mask-end', hasOverflow && !isAtEnd ? 'var(--scrollable-mask-size)' : '0px');
    }

    function setupScrollableMasks() {
        if (!contentTableScrollEnabled) {
            return;
        }

        const wrappers = document.querySelectorAll('.default-content .template-table-scroll');
        const resizeObserver = 'ResizeObserver' in window ? new ResizeObserver(entries => {
            entries.forEach(entry => {
                if (entry.target.classList.contains('template-table-scroll')) {
                    updateScrollableMask(entry.target);
                    return;
                }

                if (
                    entry.target.parentElement &&
                    entry.target.parentElement.classList.contains('template-table-scroll')
                ) {
                    updateScrollableMask(entry.target.parentElement);
                }
            });
        }) : null;

        wrappers.forEach(wrapper => {
            updateScrollableMask(wrapper);

            wrapper.addEventListener('scroll', function () {
                updateScrollableMask(wrapper);
            }, {
                passive: true
            });

            if (resizeObserver) {
                resizeObserver.observe(wrapper);

                if (wrapper.firstElementChild) {
                    resizeObserver.observe(wrapper.firstElementChild);
                }
            }
        });

        window.addEventListener('resize', function () {
            wrappers.forEach(wrapper => {
                updateScrollableMask(wrapper);
            });
        });
    }

    /**
     * Handle click on a element with #target to perform scroll action
     * @param event
     */
    function handleScrollClick(event) {
        event.preventDefault();

        const href = this.getAttribute('href');

        if (href.length === 1) {
            return;
        }

        const Target = document.querySelector(this.getAttribute('href'));

        if (!Target) {
            return;
        }

        let offset = scrollOffset;

        if (parseInt(Target.getAttribute('data-qui-offset')) ||
            parseInt(Target.getAttribute('data-qui-offset')) >= 0) {
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
            const Target = document.getElementById(target);

            if (!Target) {
                return;
            }

            let offset = scrollOffset;

            if (parseInt(Target.getAttribute('data-qui-offset')) ||
                parseInt(Target.getAttribute('data-qui-offset')) >= 0) {
                offset = Target.getAttribute('data-qui-offset');
            }

            if (!isInViewport(Target)) {
                new Fx.Scroll(window, {
                    duration: 500,
                    offset: {
                        y: -offset
                    }
                }).toElement(Target);
            }
        }
    })();

    wrapDefaultContentTables();
    setupScrollableMasks();

    // load QUI
    require(['qui/QUI', 'utils/Controls'], function (QUI, Controls) {
        QUI.addEvent("onError", function (msg, url, linenumber) {
            console.error(msg);
            console.error(url);
            console.error('LineNo: ' + linenumber);
        });

        const HeaderBar = document.querySelector('.header-bar');

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
        if (typeof NAV_IS_FIXED !== 'undefined') {
            var headerBar     = document.getElement('[data-name="header-bar"]'),
                navBackground = false;

            if (!headerBar) {
                return;
            }

            QUI.addEvent('scroll', function () {
                if (QUI.getScroll().y > HEADER_BAR_SCROLL_CLASS_OFFSET) {
                    if (!navBackground) {
                        headerBar.addClass('header-bar--scrolled');
                        navBackground = true;
                    }
                    return;
                }

                headerBar.removeClass('header-bar--scrolled');
                navBackground = false;
            });
        }

        /**
         * social share buttons
         */
        if (SHOW_SOCIAL_IN_MENU) {
            var SlideOutElm = document.getElement(
                '[data-qui="package/quiqqer/menu/bin/SlideOut"]'
            );

            if (SlideOutElm) {
                SlideOutElm.insertAdjacentHTML('beforeend', SOCIAL_MENU_HTML);
            }
        }
    });

    /**
     * show the search input after clicking on the button
     */
    const SearchForm = document.getElement('[data-name="headerBarSearchForm"]');
    const SearchInput = document.getElement('[data-name="headerBarSearchForm-input"]');
    const ShowBtn = document.getElement('[data-name="headerBarSearchForm-showInputBtn"]');
    if (SearchForm && SearchInput && ShowBtn) {
        let open        = false;

        const hideInput = function () {
            SearchForm.setAttribute('data-show-input', '0');
            open = false;
            window.removeEventListener('click', hideInput);
        };

        ShowBtn.addEvent('click', function (event) {
            event.stopPropagation();

            if (!open) {
                SearchForm.querySelector('input').addEvent('click', function (e) {
                    e.stopPropagation();
                });

                SearchForm.setAttribute('data-show-input', '1');
                window.addEventListener('click', hideInput);

                (function () {
                    SearchInput.focus();
                }).delay(100);

                open = true;
                return;
            }

            if (SearchInput.value.length === 0) {
                SearchInput.focus();

                return;
            }

            SearchForm.submit();
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
