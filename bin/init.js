/**
 * Visitor color mode switch (template/colorModeSwitch.html).
 *
 * index.html applies a stored choice before rendering. This handler changes
 * the mode, stores it per project and keeps every switch instance in sync.
 * It runs without waiting for QUI so the switches react immediately.
 */
(function () {
    "use strict";

    const root = document.documentElement;
    const storageKey = root.getAttribute('data-presentation-mode-storage');

    if (root.getAttribute('data-presentation-allow-visitor') !== '1' || !storageKey) {
        return;
    }

    const modes = ['system', 'light', 'dark'];
    const colorSchemeMeta = document.querySelector('meta[name="color-scheme"]');

    /**
     * @todo ANCHOR-POSITIONING-FALLBACK (review 2027-11): positions the dropdown
     *       menu when CSS anchor positioning is missing (see the @supports block in
     *       bin/css/components/colorModeSwitch.css). Check
     *       https://caniuse.com/css-anchor-positioning; once position-area and
     *       position-try-fallbacks are supported everywhere, remove
     *       supportsAnchorPositioning, menuGap, viewportPadding, positionMenu and
     *       their use in initDropdown (onViewportChange and its listeners).
     */
    const supportsAnchorPositioning = CSS.supports('position-area: block-end');
    const menuGap = 6; // px, matches margin-block of the anchored menu
    const viewportPadding = 8; // px, minimum distance to the viewport edge

    function positionMenu(Toggle, Menu) {
        const anchor = Toggle.getBoundingClientRect();
        const width = Menu.offsetWidth;
        const height = Menu.offsetHeight;
        let top = anchor.bottom + menuGap;

        if (top + height > window.innerHeight - viewportPadding && anchor.top - menuGap - height >= viewportPadding) {
            top = anchor.top - menuGap - height;
        }

        const maxLeft = window.innerWidth - width - viewportPadding;
        const left = Math.max(viewportPadding, Math.min(anchor.right - width, maxLeft));

        Menu.style.top = top + 'px';
        Menu.style.left = left + 'px';
    }

    function getMode() {
        const mode = root.getAttribute('data-presentation-mode');

        return modes.includes(mode) ? mode : 'system';
    }

    function setLabel(Element, mode) {
        const label = Element.getAttribute('data-label-' + mode);

        Element.setAttribute('aria-label', label);
        Element.setAttribute('title', label);
    }

    function syncSwitches(mode) {
        document.querySelectorAll('[data-name="tpl-colorModeSwitch"]').forEach((Switch) => {
            const view = Switch.getAttribute('data-view');

            if (view === 'compact') {
                setLabel(Switch, mode);
                return;
            }

            if (view === 'dropdown') {
                setLabel(Switch.querySelector('[data-name="toggle"]'), mode);
            }

            const stateAttribute = view === 'dropdown' ? 'aria-checked' : 'aria-pressed';

            Switch.querySelectorAll('[data-name="option"]').forEach((Option) => {
                Option.setAttribute(stateAttribute, Option.getAttribute('data-mode') === mode ? 'true' : 'false');
            });
        });
    }

    function applyMode(mode) {
        const colorScheme = mode === 'system' ? 'light dark' : mode;

        root.setAttribute('data-presentation-mode', mode);
        root.style.colorScheme = colorScheme;

        if (colorSchemeMeta) {
            colorSchemeMeta.setAttribute('content', colorScheme);
        }

        syncSwitches(mode);
    }

    function setMode(mode) {
        applyMode(mode);

        try {
            localStorage.setItem(storageKey, mode);
        } catch (e) {
            // storage unavailable: the choice applies to the current page only
        }
    }

    /**
     * Dropdown: the toggle opens the menu as a popover (top layer, light dismiss,
     * Escape and focus return by the browser). Keyboard handling follows the
     * WAI-ARIA menu button pattern.
     */
    function initDropdown(Switch, index) {
        const Toggle = Switch.querySelector('[data-name="toggle"]');
        const Menu = Switch.querySelector('[data-name="menu"]');

        if (!Toggle || !Menu || typeof Menu.showPopover !== 'function') {
            return;
        }

        const items = Array.from(Menu.querySelectorAll('[data-name="option"]'));
        const anchorName = '--tpl-colorModeSwitch-' + index;
        let focusOnOpen = 'checked';

        Menu.id = 'tpl-colorModeSwitch-menu-' + index;
        Toggle.setAttribute('aria-controls', Menu.id);
        Toggle.popoverTargetElement = Menu;
        Toggle.style.setProperty('anchor-name', anchorName);
        Menu.style.setProperty('position-anchor', anchorName);

        // without preventScroll the browser may scroll the page to the sticky navigation
        function focusItem(item) {
            if (item) {
                item.focus({preventScroll: true});
            }
        }

        // the auto-hide navigation stays visible while aria-expanded is true
        function onViewportChange() {
            positionMenu(Toggle, Menu);
        }

        /**
         * Return focus to the toggle before the menu hides. The browser only restores
         * focus that existed before opening, and Safari does not focus buttons on
         * click. beforetoggle fires synchronously, so Tab moves on from the toggle.
         */
        Menu.addEventListener('beforetoggle', (event) => {
            if (event.newState === 'closed' && Menu.contains(document.activeElement)) {
                Toggle.focus();
            }
        });

        Menu.addEventListener('toggle', (event) => {
            const isOpen = event.newState === 'open';

            Toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');

            if (!isOpen) {
                window.removeEventListener('scroll', onViewportChange);
                window.removeEventListener('resize', onViewportChange);
                focusOnOpen = 'checked';
                return;
            }

            if (!supportsAnchorPositioning) {
                positionMenu(Toggle, Menu);
                window.addEventListener('scroll', onViewportChange, {passive: true});
                window.addEventListener('resize', onViewportChange, {passive: true});
            }

            if (focusOnOpen === 'first') {
                focusItem(items[0]);
            } else if (focusOnOpen === 'last') {
                focusItem(items[items.length - 1]);
            } else {
                focusItem(items.find((item) => item.getAttribute('aria-checked') === 'true') || items[0]);
            }
        });

        Toggle.addEventListener('keydown', (event) => {
            if (event.key !== 'ArrowDown' && event.key !== 'ArrowUp') {
                return;
            }

            event.preventDefault();
            focusOnOpen = event.key === 'ArrowDown' ? 'first' : 'last';

            if (Menu.matches(':popover-open')) {
                focusItem(focusOnOpen === 'first' ? items[0] : items[items.length - 1]);
                return;
            }

            Menu.showPopover();
        });

        Menu.addEventListener('keydown', (event) => {
            const current = items.indexOf(document.activeElement);
            const targets = {
                ArrowDown: items[(current + 1) % items.length],
                ArrowUp: items[(current - 1 + items.length) % items.length],
                Home: items[0],
                End: items[items.length - 1]
            };

            if (event.key === 'Tab') {
                // closing returns focus to the toggle, Tab then moves on from there
                Menu.hidePopover();
                return;
            }

            if (targets[event.key]) {
                event.preventDefault();
                focusItem(targets[event.key]);
            }
        });
    }

    document.addEventListener('click', (event) => {
        const Switch = event.target.closest('[data-name="tpl-colorModeSwitch"]');

        if (!Switch) {
            return;
        }

        if (Switch.getAttribute('data-view') === 'compact') {
            setMode(modes[(modes.indexOf(getMode()) + 1) % modes.length]);
            return;
        }

        const mode = event.target.closest('[data-name="option"]')?.getAttribute('data-mode');

        if (!modes.includes(mode)) {
            return;
        }

        setMode(mode);
        Switch.querySelector('[data-name="menu"]')?.hidePopover?.();
    });

    // choice changed in another tab of the same project
    window.addEventListener('storage', (event) => {
        if (event.key === storageKey && modes.includes(event.newValue)) {
            applyMode(event.newValue);
        }
    });

    // page restored from the back/forward cache after a change on another page
    window.addEventListener('pageshow', (event) => {
        if (!event.persisted) {
            return;
        }

        try {
            const mode = localStorage.getItem(storageKey);

            if (modes.includes(mode)) {
                applyMode(mode);
            }
        } catch (e) {
            // storage unavailable: keep the current mode
        }
    });

    document.querySelectorAll('[data-name="tpl-colorModeSwitch"][data-view="dropdown"]').forEach(initDropdown);

    // labels and selected states were rendered for the operator default
    syncSwitches(getMode());
})();

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

    function focusScrollTarget(target) {
        if (typeof target.focus !== 'function') {
            return;
        }

        target.focus({preventScroll: true});

        if (document.activeElement === target || target.hasAttribute('tabindex')) {
            return;
        }

        // Headings and sections need a temporary tabindex for anchor navigation.
        target.setAttribute('tabindex', '-1');
        target.addEventListener('blur', function () {
            if (target.getAttribute('tabindex') === '-1') {
                target.removeAttribute('tabindex');
            }
        }, {once: true});
        target.focus({preventScroll: true});
    }

    function getScrollTargetByHref(href) {
        if (!href || href === '#') {
            return null;
        }

        try {
            const url = new URL(href, document.baseURI);

            if (
                url.origin !== window.location.origin ||
                url.pathname !== window.location.pathname ||
                url.search !== window.location.search ||
                !url.hash
            ) {
                return null;
            }

            if (url.hash === '#top') {
                return document.documentElement;
            }

            const targetId = decodeURIComponent(url.hash.substring(1));

            return document.getElementById(targetId);
        } catch (error) {
            return null;
        }
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
        if (
            event.defaultPrevented ||
            event.button !== 0 ||
            event.ctrlKey ||
            event.metaKey ||
            event.shiftKey ||
            event.altKey
        ) {
            return;
        }

        const anchor = event.target.closest?.('a[href*="#"]');

        if (
            !anchor ||
            anchor.getAttribute('data-qui-disableTemplateScroll') === '1' ||
            anchor.matches('[data-name="toTop"], [data-name="toTopBar"]')
        ) {
            return;
        }

        const browsingTarget = anchor.getAttribute('target') ||
            document.querySelector('base[target]')?.getAttribute('target');

        if (anchor.hasAttribute('download') || (browsingTarget && browsingTarget.toLowerCase() !== '_self')) {
            return;
        }

        const target = getScrollTargetByHref(anchor.getAttribute('href'));

        if (!target) {
            return;
        }

        const url = new URL(anchor.getAttribute('href'), document.baseURI);

        if (url.hash !== window.location.hash) {
            window.history.pushState(null, '', url.href);
        }

        event.preventDefault();
        focusScrollTarget(target);
        scrollToElement(target, getTargetOffset(target));
    }

    // Delegation also handles links inserted after initialization.
    document.addEventListener('click', handleScrollClick);

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

                    if (prefersReducedMotion()) {
                        // Keep the pinned nav visible without scroll-driven movement.
                        downPivotY = currentScrollY;
                        headerBar.classList.toggle(
                            'header-bar--scrolled', scrolledPastPin > HEADER_BAR_SCROLL_OFFSET
                        );
                        applyTransform(0, false);
                        return;
                    }

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

                reducedMotionMediaQuery.addEventListener('change', function () {
                    // Reset scroll history in both directions, including pending frames.
                    lastScrollY = getCurrentScrollY();
                    downPivotY = lastScrollY;
                    updateAutoHideState();
                });

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
