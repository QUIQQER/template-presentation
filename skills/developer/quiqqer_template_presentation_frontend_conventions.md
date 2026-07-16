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

## Design System First

- Prefer existing semantic HTML elements before introducing wrapper-heavy custom markup.
- Prefer the existing utility classes, component classes (see `quiqqer_template_presentation_css_classes`),
  and the CSS tokens below before writing new CSS.
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

## Markup Class Inventory

The utility and component class inventory (buttons, badges, chips, overlines, spacing, typography, …)
lives in the content skill `quiqqer_template_presentation_css_classes` — load it whenever markup is
written or styled. In template and control CSS, adjust component variants through their CSS variable
hooks (for example `--qui-btn-*`); do not rewrite component CSS.

The complete and always current definitions live in the template sources (for code access):

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
