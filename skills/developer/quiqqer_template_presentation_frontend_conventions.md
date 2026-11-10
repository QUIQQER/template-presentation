---
name: quiqqer_template_presentation_frontend_conventions
description: Use when developing frontend code for quiqqer/template-presentation or a child template of it, for example template code, controls, or brick types rendered with it. Provides the template's design token inventory (--qui-* CSS variables) and the conventions for using tokens instead of hardcoded values. The markup class inventory lives in the content skill quiqqer_template_presentation_css_classes.
category: developer
---

# Template Presentation Frontend Conventions

Use this skill when developing frontend code that renders with `quiqqer/template-presentation` or one of
its child templates: template code, controls, and brick types. For editorial markup work (brick content,
page content), the class inventory in `quiqqer_template_presentation_css_classes` is usually sufficient.

Child templates usually only override token values (often through a project-specific layer) and add few
own utilities. The inventory below stays valid for them; load the child template's own skill for its
additions.

Load `quiqqer_frontend_css_variables` for CSS decisions: it owns the general selector and cascade rules,
styling ownership, value selection, centralized fallbacks, and the setting-backed three-layer contract.
This skill adds Presentation's concrete tokens and components, not a separate CSS architecture.

## Design System First

- Prefer existing semantic HTML elements before introducing wrapper-heavy custom markup.
- Prefer the existing utility classes, component classes (see `quiqqer_template_presentation_css_classes`),
  and the CSS tokens below before writing new CSS.
- Use semantic headings (`h1`–`h6`) instead of recreating heading styles manually, and let text inherit
  its default color where possible.
- If an existing component already solves the UI pattern, reuse it instead of rebuilding it locally.
- Add custom CSS only for gaps that the design system does not cover. Prefer tokens established for
  the intended use; a variable's existence alone does not make it a recommended convention. Functional
  layout values need not become tokens or variables. Follow Core's value-selection rules rather than
  wrapping every number in a new variable.

## Design Tokens (CSS Variables)

The canonical token prefix is `--qui-`. The most used families:

- Colors: `--qui-colors-<name>` with the palette names `primary`, `secondary`, `success`, `warning`,
  `danger`, `info`, `dark`, `light`, `gray`, `black`, `white`, each with a `-contrast` partner
  (for example `--qui-colors-primary`, `--qui-colors-primary-contrast`).
  The value of `--qui-colors-primary` (and its `-contrast` partner) comes from the QUIQQER project
  settings (main color), so the concrete color is not visible in the CSS sources. Always use the token,
  never a literal value for it.
- Text colors: `--qui-colors-text`, `--qui-colors-text-muted`, `--qui-colors-text-soft`,
  `--qui-colors-heading`, `--qui-colors-link`, `--qui-colors-link--hover`.
- Surfaces: `--qui-colors-background`, `--qui-colors-surface`, `--qui-colors-surface-subtle`,
  `--qui-colors-surface-card`, `--qui-colors-surface-card-subtle`.
- Font sizes: `--qui-fs-xs` to `--qui-fs-6xl` for text, `--qui-fs-1` to `--qui-fs-6` for heading levels,
  `--qui-fs-body`.
- Borders: `--qui-border-color`, `--qui-border-width`, `--qui-border-style`.
- Buttons: `--qui-btn-<variant>-<property>` hooks per variant and state, for example
  `--qui-btn-primary-bg--hover`, plus shared hooks such as `--qui-btn-borderRadius`.

## Popovers

Use `.qui-popover` for a floating surface and add `.qui-popover--menu` for a menu layout.
The appearance is independent of Presentation controls; positioning, opening, keyboard handling,
focus management and ARIA roles remain the caller's responsibility. Native `[popover]` menus stay
hidden until opened. Non-native surfaces need their own visibility handling.

The defaults in `bin/css/variables/popover.css` resolve on each `.qui-popover`, rather than on `:root`,
so local colors and button radii are respected. Override public `--qui-popover-*` properties on a
surface or an ancestor, or use the corresponding `--theme--qui-popover-*` hooks for theme defaults.
Public properties take precedence; `--_popover-*` aliases are internal and must not be overridden.

Available suffixes:

- Surface: `bgColor`, `color`, `borderColor`, `borderWidth`, `borderStyle`, `borderRadius`, `shadow`,
  `padding`, `gap` (spacing between menu entries).
- Entries: `item-borderRadius`, `item-padding`, `item-gap`, `item-bgColor--hover`,
  `item-fontWeight--selected`.

Surface defaults use the semantic surface/text colors, general border tokens, `--qui-radius-md`
and `--qui-shadow-md`. Entry radii follow `--qui-btn-borderRadius`. Selection uses a check mark and
font weight 600 by default, independently of the primary color. Hover/focus backgrounds use the
semantic subtle surface color, which follows light/dark mode.

For example, a project can customize one popover without changing other instances:

```css
.project-actions {
    --qui-popover-borderRadius: var(--qui-radius-lg);
    --qui-popover-shadow: var(--qui-shadow-sm);
    --qui-popover-item-padding: 0.75rem 1rem;
}
```

Use native buttons or links with `.qui-popover__item`, decorative icons with `.qui-popover__icon`,
and an optional `.qui-popover__indicator` for a selection mark. The indicator reserves its space
and becomes visible for an item with `aria-checked="true"`. Use that state only with appropriate
semantics, such as `role="menuitemradio"`; ordinary action items need neither it nor an indicator.
Selection has no persistent background; hover/focus adds a background, and keyboard focus an outline.

## Spacing

Inspect the relevant template and component conventions before choosing distances. This skill does
not prescribe a spacing token family or scale. When no established token fits the intended use,
purposeful component-owned values such as `1.5rem` are allowed; do not create variables merely to
replace numbers. Existing token usage does not need to be migrated as part of unrelated work.

Solve component spacing in the component's own CSS, not via spacing utility classes. Those classes are an editorial
fallback for markup that has no CSS, are deprecated for spacing, and must not be used as a module building
block.

## Fallbacks For Portability

When a token is available in all supported contexts and no separate configuration is needed, use it
directly, for example `font-size: var(--qui-fs-sm)`. No private alias or public hook is required.

A reusable control may run without Presentation. For a justified component-owned property, define a
suitable fallback once on the control root and consume the private alias, following the Core skill:

```css
.vendor-package-control {
    --_infoFontSize: var(--qui-fs-sm, 0.875rem);
}

.vendor-package-control__info {
    font-size: var(--_infoFontSize);
}
```

Choose a fallback appropriate to the component's supported contexts, using the canonical token
definition as a reference. A fixed value can match a baseline but does not reproduce the token's
responsive scale or theme overrides. Do not promise identical behavior across templates or introduce
arbitrary color fallbacks. Setting-backed values still require the full three-layer contract, and
responsive defaults must retain its theme/config priority.

## Navigation Auto-Hide

The nav supports several position modes (setting "Menüposition"); `autoHide` is the only one with a
scripted transform. It slides the header bar off-screen on scroll down (via an inline `transform` on
`.header-bar`, set in `bin/init.js`) and reveals it on scroll up. Do not set your own `transform` on
`.header-bar` in this mode, it fights the script.

If a child template gives the nav a `box-shadow` or `outline`, it can peek at the top edge while the
nav is hidden. Set `--theme--qui-nav-autoHide-buffer` to roughly the shadow's vertical reach
(offset-y + blur + spread); the nav is then moved that much further off-screen. Default `0`, any CSS
length. Only affects `autoHide`.

## Markup Class Inventory

The utility and component class inventory (buttons, badges, chips, overlines, spacing, typography, …)
lives in the content skill `quiqqer_template_presentation_css_classes` — load it whenever markup is
written or styled. In template and control CSS, adjust component variants through their CSS variable
hooks (for example `--qui-btn-*`); do not rewrite component CSS.

The complete and always current definitions live in the template sources (for code access):

- `bin/css/variables/` — token definitions (colors, typography, spacing, buttons, border, shadows, forms, …)
- `bin/css/utility/` — utility classes
- `bin/css/components/` — component classes (buttons, badges, chips, overline, messages)

## Change Scope

- Keep frontend changes small and local.
- Do not introduce a parallel design language inside the template.
- Preserve existing DOM structure, classes, and behavior unless the task requires a change.
- For brick instances and editorial content, follow `quiqqer_bricks_create_and_edit_blocks` (visual
  references, custom CSS scoping, internal links). For brick type development, follow
  `quiqqer_bricks_develop_brick_types`; for CSS decisions, follow `quiqqer_frontend_css_variables`.
