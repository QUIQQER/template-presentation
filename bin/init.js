whenQuiLoaded().then(() => {
    "use strict";

    const defaultScrollOffset = window.SCROLL_OFFSET ? window.SCROLL_OFFSET : 0;
    const contentTableScrollEnabled = typeof CONTENT_TABLE_SCROLL !== 'undefined' && CONTENT_TABLE_SCROLL === 1;
    const scrollableMaskTolerance = 1;
    const toTopShowOffset = 300;
    const toTopHideOffsetFromPageEnd = 160;
    let scrollOffset = defaultScrollOffset;
    const reducedMotionMediaQuery = window.matchMedia('(prefers-reduced-motion: reduce)');

    function prefersReducedMotion() {
        return reducedMotionMediaQuery.matches;
    }

    function getCurrentScrollY() {
        return window.scrollY || window.pageYOffset || 0;
    }

    function getScrollBehavior() {
        return prefersReducedMotion() ? 'auto' : 'smooth';
    }

    function getTargetOffset(target) {
        const targetOffset = parseInt(target.getAttribute('data-qui-offset'), 10);

        if (!Number.isNaN(targetOffset) && targetOffset >= 0) {
            return targetOffset;
        }

        return scrollOffset;
    }

    function scrollToPosition(top) {
        window.scrollTo({
            top: Math.max(0, top),
            behavior: getScrollBehavior()
        });
    }

    function scrollToElement(target, offset = scrollOffset) {
        const top = target.getBoundingClientRect().top + getCurrentScrollY() - offset;

        scrollToPosition(top);
    }

    function getScrollTargetByHref(href) {
        if (!href || href === '#') {
            return null;
        }

        if (href === '#top') {
            return document.documentElement;
        }

        const targetId = decodeURIComponent(href.substring(1));

        return document.getElementById(targetId);
    }

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

        const href = event.currentTarget.getAttribute('href');

        const target = getScrollTargetByHref(href);

        if (!target) {
            return;
        }

        scrollToElement(target, getTargetOffset(target));
    }

    // find all scroll links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        if (
            anchor.getAttribute('data-qui-disableTemplateScroll') === '1' ||
            anchor.matches('[data-name="toTop"]')
        ) {
            return;
        }
        anchor.addEventListener('click', handleScrollClick);
    });

    // scroll to anchor, not jump
    (function () {
        let queryString = window.location.hash;

        if (queryString && queryString.substr(0, 4) === '#go_') {
            const target = decodeURI(queryString.substr(4));
            const targetElement = document.getElementById(target);

            if (!targetElement) {
                return;
            }

            if (!isInViewport(targetElement)) {
                scrollToElement(targetElement, getTargetOffset(targetElement));
            }
        }
    })();

    wrapDefaultContentTables();
    setupScrollableMasks();

    /**
     * toTop button
     */
    const toTopBtn = document.querySelector('[data-name="toTop"]');

    if (toTopBtn) {
        let buttonVisible = false;

        const setToTopVisibility = function (visible) {
            toTopBtn.classList.toggle('toTop__show', visible);
            toTopBtn.setAttribute('aria-hidden', visible ? 'false' : 'true');

            if (visible) {
                toTopBtn.removeAttribute('tabindex');
                return;
            }

            toTopBtn.setAttribute('tabindex', '-1');
        };

        const updateToTopVisibility = function () {
            const scrollY = getCurrentScrollY();
            const viewportBottom = scrollY + window.innerHeight;
            const pageEnd = document.documentElement.scrollHeight - toTopHideOffsetFromPageEnd;
            const visible = scrollY > toTopShowOffset && viewportBottom < pageEnd;

            if (visible === buttonVisible) {
                return;
            }

            setToTopVisibility(visible);
            buttonVisible = visible;
        };

        setToTopVisibility(false);
        updateToTopVisibility();

        window.addEventListener('scroll', updateToTopVisibility, {
            passive: true
        });

        toTopBtn.addEventListener('click', function (event) {
            event.preventDefault();
            scrollToPosition(0);
        });
    }

    // load QUI
    require(['qui/QUI', 'utils/Controls'], function (QUI, Controls) {
        QUI.addEvent("onError", function (msg, url, linenumber) {
            console.error(msg);
            console.error(url);
            console.error('LineNo: ' + linenumber);
        });

        /**
         * show nav background after scroll
         * works only if nav is position fixed
         */
        if (typeof NAV_IS_FIXED !== 'undefined') {
            const headerBar = document.querySelector('[data-name="header-bar"]');
            let navBackground = false;

            if (!headerBar) {
                return;
            }

            QUI.addEvent('scroll', function () {
                if (QUI.getScroll().y > HEADER_BAR_SCROLL_CLASS_OFFSET) {
                    if (!navBackground) {
                        headerBar.classList.add('header-bar--scrolled');
                        navBackground = true;
                    }
                    return;
                }

                headerBar.classList.remove('header-bar--scrolled');
                navBackground = false;
            });
        }

        /**
         * social share buttons
         */
        if (SHOW_SOCIAL_IN_MENU) {
            const slideOutElm = document.querySelector(
                '[data-qui="package/quiqqer/menu/bin/SlideOut"]'
            );

            if (slideOutElm) {
                slideOutElm.insertAdjacentHTML('beforeend', SOCIAL_MENU_HTML);
            }
        }
    });

    /**
     * show the search input after clicking on the button
     */
    const searchForm = document.querySelector('[data-name="headerBarSearchForm"]');
    const searchInput = document.querySelector('[data-name="headerBarSearchForm-input"]');
    const showBtn = document.querySelector('[data-name="headerBarSearchForm-showInputBtn"]');

    if (searchForm && searchInput && showBtn) {
        let open = false;

        const hideInput = () => {
            searchForm.setAttribute('data-show-input', '0');
            open = false;
            window.removeEventListener('click', hideInput);
        };

        showBtn.addEventListener('click', event => {
            event.stopPropagation();

            if (!open) {
                const formInput = searchForm.querySelector('input');

                if (formInput) {
                    formInput.addEventListener('click', e => {
                        e.stopPropagation();
                    });
                }

                searchForm.setAttribute('data-show-input', '1');
                window.addEventListener('click', hideInput);

                window.setTimeout(() => {
                    searchInput.focus();
                }, 100);

                open = true;
                return;
            }

            if (searchInput.value.length === 0) {
                searchInput.focus();

                return;
            }

            searchForm.submit();
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
