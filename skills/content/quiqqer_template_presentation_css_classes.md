---
name: quiqqer_template_presentation_css_classes
description: Use when writing or styling HTML markup in a project that uses quiqqer/template-presentation or a child template of it, for example brick content, page content, or custom CSS. Lists the template's utility classes and component classes (buttons, badges, chips, overlines, spacing, typography) to use instead of writing custom CSS.
category: content
---

# Template Presentation CSS Classes

Use this skill when writing or styling HTML markup in a project that uses `quiqqer/template-presentation`
or one of its child templates: brick content, page content, and custom CSS.

Style through these existing classes directly in the markup. Do not write custom CSS for what an existing
class already does. Child templates inherit this entire inventory; load the child template's own skill for
its project-specific additions.

## Utility Classes

- Typography: `.h1`–`.h6` (heading look without changing the heading level), `.display-1`–`.display-4`,
  `.fs-xs`–`.fs-6xl`, `.fs-body`, `.fw-normal`, `.fw-bold`, `.fw-bolder`, `.fw-heading`, `.lh-xs`–`.lh-lg`,
  `.lh-base`, `.lh-heading`, `.lead`.
- Text: `.text-left`, `.text-center`, `.text-right`, `.text-width-xs`–`.text-width-xl` (limits the reading
  width), `.color-heading`.
- Links: `.link` plus color variants such as `.link-primary`, `.link-muted`, `.link-soft`.
- Spacing: the supported utilities are the auto margins `.mx-auto`, `.mt-auto`, `.mb-auto`, `.ml-auto`,
  `.mr-auto` (centering and flex spacers). The numeric `.m-*` / `.p-*` scale (and its axis variants) is
  deprecated — do not add new usages. For real spacing, use the spacing tokens in component CSS
  (`--qui-spacing-*`; see the developer skill `quiqqer_template_presentation_frontend_conventions`).
- Backgrounds: `.bg-<palette>` with tint steps 25–950, for example `.bg-primary-50`. Palette names:
  `primary`, `secondary`, `success`, `warning`, `danger`, `info`, `dark`, `light`, `gray`, `white`.
- Misc: `.list-unstyled`.

## Component Classes

- Buttons: `.btn` with color variants `.btn-primary`, `.btn-secondary`, `.btn-success`, `.btn-warning`,
  `.btn-danger`, `.btn-info`, `.btn-dark`, `.btn-light`, `.btn-white`, each also as `-outline`; sizes
  `.btn-sm`, `.btn-lg`; shapes and layout `.btn-rounded`, `.btn-full`, `.btn-icon`, `.btn-link`,
  `.btn-container` (with `--center`). Pick a variant class instead of styling a button yourself.
- Badges: `.badge` with the same palette (`.badge-primary`, …), `-light` variants, `.badge-pill`,
  `.badge-sm`, `.badge-lg`, `.badge-neutral`.
- Chips: `.chip`.
- Overlines: `.overline` with `.overline-center`, `.overline-primary`, `.overline-neutral`,
  `.overline-reset`; the visual style is controlled by the template setting. The overline already zeroes
  the following heading's top margin, so no spacing class is needed on the heading.

## Usage Rules

- Use semantic headings (`h1`–`h6`) and adjust their look with the typography utilities if needed, instead
  of recreating heading styles with custom CSS.
- Let text inherit its default color where possible; do not set text colors in content markup.
- Express variants (color, size, shape) through the component's variant classes, not through additional
  custom CSS next to the component.
- These lists cover the everyday inventory, not every class. If a needed class seems to be missing, check
  the sibling variants first (the naming is systematic) before falling back to custom CSS.
