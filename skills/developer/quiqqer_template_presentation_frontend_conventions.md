---
name: quiqqer_template_presentation_frontend_conventions
description: Use when building or editing frontend output in quiqqer/template-presentation or a child template of it, including bricks and page content. Provides the template's design token, utility class, and component class inventory, and the rules for using them instead of custom CSS and hardcoded values.
category: developer
---

# Template Presentation Frontend Conventions

Use this skill when implementing or adjusting frontend markup, styling, or brick output for
`quiqqer/template-presentation` or one of its child templates.

Child templates usually only override token values (often through a project-specific layer) and add few
own utilities. The inventory below stays valid for them; load the child template's own skill for its
additions.

## Design System First

- Prefer existing semantic HTML elements before introducing wrapper-heavy custom markup.
- Prefer the utility classes, component classes, and CSS tokens below before writing new CSS.
- Use semantic headings (`h1`–`h6`) instead of recreating heading styles manually, and let text inherit
  its default color where possible.
- If an existing component already solves the UI pattern, reuse it instead of rebuilding it locally.
- Add custom CSS only for gaps that the design system does not cover, and write it against the tokens
  below instead of hardcoded sizes and colors.

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
- Spacing: `--qui-spacer` (with `--desktop`/`--mobile`) and the row spacing scale
  `--qui-row-spacing--extraSmall` to `--qui-row-spacing--extraLarge` (`--base` is the default).
- Buttons: `--qui-btn-<variant>-<property>` hooks per variant and state, for example
  `--qui-btn-primary-bg--hover`, plus shared hooks such as `--qui-btn-borderRadius`.

## Utility Classes

- Typography: `.h1`–`.h6`, `.display-1`–`.display-4`, `.fs-xs`–`.fs-6xl`, `.fs-body`, `.fw-normal`,
  `.fw-bold`, `.fw-bolder`, `.fw-heading`, `.lh-xs`–`.lh-lg`, `.lh-base`, `.lh-heading`, `.lead`.
- Text: `.text-left`, `.text-center`, `.text-right`, `.text-width-xs`–`.text-width-xl` (reading width),
  `.color-heading`.
- Links: `.link` plus color variants such as `.link-primary`, `.link-muted`, `.link-soft`.
- Spacing: margin and padding scales 0–5 as `.m-*`, `.mt-*`, `.mb-*`, `.ml-*`, `.mr-*`, `.p-*`, `.pt-*`,
  `.pb-*`, `.pl-*`, `.pr-*`, plus `auto` variants for margins (for example `.mt-0`, `.mb-3`, `.ml-auto`).
- Backgrounds: `.bg-<palette>` with tint steps 25–950, for example `.bg-primary-50`.
- Misc: `.list-unstyled`.

## Component Classes

- Buttons: `.btn` with color variants `.btn-primary`, `.btn-secondary`, `.btn-success`, `.btn-warning`,
  `.btn-danger`, `.btn-info`, `.btn-dark`, `.btn-light`, `.btn-white`, each also as `-outline`; sizes
  `.btn-sm`, `.btn-lg`; shapes and layout `.btn-rounded`, `.btn-full`, `.btn-icon`, `.btn-link`,
  `.btn-container` (with `--center`). Adjust variants through the `--qui-btn-*` hooks, do not rewrite
  button CSS.
- Badges: `.badge` with the same palette (`.badge-primary`, …), `-light` variants, `.badge-pill`,
  `.badge-sm`, `.badge-lg`, `.badge-neutral`.
- Chips: `.chip`.
- Overlines: `.overline` with `.overline-center`, `.overline-primary`, `.overline-neutral`,
  `.overline-reset`; the visual style is controlled by the template setting (`data-tpl-overline`).
  When an overline sits directly above a heading, give the heading `.mt-0`.

These lists cover the everyday inventory, not every class. The complete and always current definitions
live in the template sources (for code access):

- `bin/css/variables/` — token definitions (colors, typography, buttons, border, shadows, forms, …)
- `bin/css/utility/` — utility classes
- `bin/css/components/` — component classes (buttons, badges, chips, overline, messages)

## Change Scope

- Keep frontend changes small and local.
- Do not introduce a parallel design language inside the template.
- Preserve existing DOM structure, classes, and behavior unless the task requires a change.
- For brick work, follow `quiqqer_bricks_create_and_edit_blocks` (visual references, custom CSS scoping,
  internal links). For configurable control CSS, follow the three-layer pattern from
  `quiqqer_frontend_css_variables`.
