whenQuiLoaded().then(() => {
    "use strict";

    const defaultScrollOffset = window.SCROLL_OFFSET ? window.SCROLL_OFFSET : 0;
    const contentTableScrollEnabled = typeof CONTENT_TABLE_SCROLL !== 'undefined' && CONTENT_TABLE_SCROLL === 1;
    const scrollableMaskTolerance = 1;
    const toTopShowOffset = 300;
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
    const toTopBar = document.querySelector('[data-name="toTopBar"]');

    const scrollToTop = function (event) {
        event.preventDefault();
        scrollToPosition(0);
    };

    if (toTopBar) {
        toTopBar.addEventListener('click', scrollToTop);
    }

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
            // Hide the floating button as soon as the in-flow toTopBar scrolls
            // into view, so the two controls never overlap and there is never a
            // gap where neither is reachable.
            const barTop = toTopBar
                ? toTopBar.getBoundingClientRect().top + scrollY
                : document.documentElement.scrollHeight;
            const visible = scrollY > toTopShowOffset && viewportBottom < barTop;

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

        toTopBtn.addEventListener('click', scrollToTop);
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

            if (headerBar) {
                let navBackground = false;
                let scrollTicking = false;

                const updateHeaderBarScrollState = function () {
                    const isScrolled = getCurrentScrollY() > HEADER_BAR_SCROLL_OFFSET;

                    scrollTicking = false;

                    if (isScrolled === navBackground) {
                        return;
                    }

                    headerBar.classList.toggle('header-bar--scrolled', isScrolled);
                    navBackground = isScrolled;
                };

                updateHeaderBarScrollState();

                window.addEventListener('scroll', function () {
                    if (scrollTicking) {
                        return;
                    }

                    scrollTicking = true;
                    window.requestAnimationFrame(updateHeaderBarScrollState);
                }, {
                    passive: true
                });
            }
        }

        /**
         * auto-hide nav: follows the scroll off-screen on the way down (like a
         * normally positioned menu) and snaps back in on a deliberate scroll up.
         * The solid background is only applied once the nav has left the visible
         * area, so an initially transparent nav never flashes its background.
         * Only active in the autoHide position mode; plain fixed nav is untouched.
         */
        if (typeof NAV_AUTO_HIDE !== 'undefined') {
            const headerBar = document.querySelector('[data-name="header-bar"]');

            if (headerBar && headerBar.parentNode) {
                let lastScrollY = getCurrentScrollY();
                let downPivotY = lastScrollY;
                let hiddenPx = 0;
                let autoHideTicking = false;

                const navShouldStayVisible = function () {
                    return headerBar.contains(document.activeElement) ||
                        headerBar.querySelector('[aria-expanded="true"]') !== null;
                };

                /**
                 * A zero-size sentinel at the nav's natural top position gives the
                 * distance scrolled past the point where the nav gets pinned. As
                 * the sentinel is not sticky, -getBoundingClientRect().top equals
                 * that distance regardless of a topBanner height, unlike the sticky
                 * nav's own offsetTop/rect which stay at the top once pinned.
                 */
                const sentinel = document.createElement('div');
                sentinel.setAttribute('aria-hidden', 'true');
                sentinel.style.height = '0';
                sentinel.style.width = '0';
                sentinel.style.visibility = 'hidden';
                headerBar.parentNode.insertBefore(sentinel, headerBar);

                // extra distance so a downward box-shadow / outline added by a
                // theme also leaves the viewport when the nav is hidden. Resolved
                // from the themeable --qui-nav-autoHide-buffer (0 by default).
                let hideBuffer = 0;
                const measureHideBuffer = function () {
                    const probe = document.createElement('div');
                    probe.style.cssText = 'position:absolute;visibility:hidden;' +
                        'height:var(--qui-nav-autoHide-buffer, 0px);';
                    headerBar.appendChild(probe);
                    hideBuffer = probe.offsetHeight;
                    probe.remove();
                };

                measureHideBuffer();
                window.addEventListener('resize', measureHideBuffer, {
                    passive: true
                });

                // translateZ(0) keeps the Chrome anti-jump fix (see .header-bar css)
                const applyTransform = function (px, animate) {
                    headerBar.style.transition = animate ? '' : 'none';
                    headerBar.style.transform = 'translateY(-' + px + 'px) translateZ(0)';
                    hiddenPx = px;
                };

                const updateAutoHideState = function () {
                    autoHideTicking = false;

                    const currentScrollY = getCurrentScrollY();
                    const navHeight = headerBar.offsetHeight;
                    const scrolledPastPin = -sentinel.getBoundingClientRect().top;
                    const delta = currentScrollY - lastScrollY;

                    lastScrollY = currentScrollY;

                    // solid background only once the nav sits its own height below
                    // the pin, i.e. while it is out of the visible area
                    headerBar.classList.toggle('header-bar--scrolled', scrolledPastPin > navHeight);

                    if (scrolledPastPin <= 0 || navShouldStayVisible()) {
                        // at the very top or a menu/focus is active: fully visible
                        downPivotY = currentScrollY;
                        applyTransform(0, hiddenPx !== 0);

                        return;
                    }

                    if (delta > 0) {
                        // scrolling down: follow the scroll off-screen 1:1, no anim
                        downPivotY = currentScrollY;
                        applyTransform(Math.min(hiddenPx + delta, navHeight + hideBuffer), false);
                    } else if (delta < 0 && downPivotY - currentScrollY > navHeight / 2) {
                        // deliberate scroll up past half the nav height: snap back in
                        applyTransform(0, true);
                    }
                };

                updateAutoHideState();

                window.addEventListener('scroll', function () {
                    if (autoHideTicking) {
                        return;
                    }

                    autoHideTicking = true;
                    window.requestAnimationFrame(updateAutoHideState);
                }, {
                    passive: true
                });
            }
        }

        /**
         * social share buttons
         */
        if (SHOW_SOCIAL_IN_MENU) {
            const slideOutElm = document.querySelector(
                '[data-slideOut="mobileMenu-SlideOut"], ' +
                '[data-slideout="mobileMenu-SlideOut"]'
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
