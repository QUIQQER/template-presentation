---
name: quiqqer_template_presentation_landingpage_01
description: Use when creating Landingpage 01 in a QUIQQER project with quiqqer/template-presentation: an enquiry-oriented page for an explanatory service or solution, using a focused Hero, trust signal, benefits, process, solution insight, social proof, FAQ and final CTA in a fixed marketing order.
category: content
---

# Template Presentation Landingpage 01

Apply this recipe only after loading `quiqqer_template_presentation_create_landingpage`. Use it for an explanatory
offer whose primary conversion is an enquiry, consultation or comparable contact action. Do not use it unchanged for
checkout, event registration, recruiting or download funnels.

## Marketing Flow

Build this sequence:

1. promise and primary action
2. immediate trust signal
3. problem and concrete benefits
4. understandable solution process
5. tangible view of the solution or service
6. credible social proof
7. answers to conversion-relevant objections
8. final action

The page should move from promise through trust, understanding and proof to action. Keep one primary conversion goal
throughout the page.

## Page Shell

- Decide navigation and footer through the entry skill before creating the body.
- Apply the design direction selected through the entry skill. A reference page or screenshot defines visual
  language, not content ownership and not permission to overwrite an existing page.
- Prefer a reduced navigation for a standalone campaign page. Reuse an inherited full navigation when the page is
  intentionally part of the regular website journey.
- Reuse an inherited footer when it already supplies the correct brand, contact and legal links. Otherwise create a
  factual footer separately.
- Do not count the final CTA in `footerPrefix` as the footer.

## Required Section Order

| Order | Area | Section | Control |
| --- | --- | --- | --- |
| 1 | `headerSuffix` | Hero | `\QUI\PresentationBricks\Controls\WallpaperText` |
| 2 | `headerSuffix` | Logo/trust strip | `\QUI\Slider\Bricks\ScrollingStrip` |
| 3 | `headerSuffix` | Benefits | `\QUI\Bricks\Controls\MultiLayout` |
| 4 | `headerSuffix` | Process | `\QUI\Bricks\Controls\MultiLayout` |
| 5 | `headerSuffix` | Solution insight | `\QUI\Bricks\Controls\MultiLayout` |
| 6 | `footerPrefix` | Customer proof | `\QUI\Bricks\Controls\Slider\CustomerReviewsFlow` |
| 7 | `footerPrefix` | FAQ | `\QUI\Bricks\Controls\Accordion` |
| 8 | `footerPrefix` | Final CTA | `\QUI\PresentationBricks\Controls\WallpaperText` |

Keep these assignments in this order. Preserve unrelated assignments only when the task explicitly requires the new
landing page to coexist with them; otherwise clarify the intended replacement before writing the complete Area.

## Required Visual Baseline

Use this baseline whenever an accepted reference page or screenshot does not deliberately override it. Treat these
settings as the recipe's default visual construction, not as optional inspiration. Adapt colors and typography to the
target project, but do not fall back to unstyled transparent sections merely because the content is complete.

Choose three harmonious light surface colors from the project's design palette or approved visual direction. Use
them consistently as surface A, B and C across benefits and process steps. Verify readable contrast; do not copy
literal colors from another project.

- **Hero:** Keep `brickFullWidth: false`, `contentMaxWidth: 900` and a minimum height around
  `clamp(30rem, 65vh, 42rem)`. Use a contained, generously rounded surface with hidden overflow and a calm gradient
  or equivalent layered background derived from the project palette. Use scoped custom CSS for the rounded gradient
  treatment when Wallpaper settings alone cannot express it.
- **Benefits:** Set `areaBackgroundEnabled: true`, use `gridGapPreset: normal`,
  `tileMinHeightPreset: standard` and `contentPaddingPreset: normal`. Enable a visible background on every card and
  distribute surfaces A, B and C across them.
- **Process:** Use `gridGapPreset: extraLarge` and one full-width row per step. Enable the matching A, B or C surface
  on each complete row and its text tile, use `contentPaddingPreset: large`, vertically center the content and keep
  the alternating `5/7` and `7/5` desktop proportions. Images should fill their panel with `cover`; use `contain`
  only when a mockup needs breathing room inside the same deliberate colored panel.
- **Solution insight:** Set `areaBackgroundEnabled: true`, `gridGapPreset: normal` and
  `tileMinHeightPreset: large`. Keep media and content tiles visibly grouped as one mosaic instead of letting them
  float independently on the page background.
- **Customer proof:** Use a full-width band with an explicit light surface from the selected palette. Choose one or
  two rows from the available testimonial count, but give the band enough density to read as a purposeful section.
- **FAQ and final CTA:** Keep the compact accordion width and generous vertical spacing. End with a strong contained
  CTA surface that has accessible contrast and a clear visual boundary.

Keep all generated images in one coherent art direction: matching palette, rendering style, perspective, lighting
and proportions. Avoid mixing unrelated photo and illustration styles, avoid text rendered inside generated images
and avoid large uncontrolled white margins around otherwise small subjects.

## 1. Hero

Use one overline, one `h1`, one concise lead, one primary CTA and at most one subordinate secondary CTA. State the
audience-relevant outcome before describing implementation details.

Use the following structural profile after verifying the current control schema:

- centered content and CTA group
- content maximum width around `900`
- minimum height around `clamp(30rem, 65vh, 42rem)`
- normal container width rather than an uncontrolled full-viewport block
- a calm brand-compatible background with sufficient text contrast
- a stable anchor ID for Hero links when needed

Use template typography, overline and button classes. Add scoped custom CSS only when the configured Wallpaper
cannot provide the required contained shape or background treatment. Do not copy colors or CSS selectors from a
reference project.

A `BackgroundVideo` control is an allowed alternative only when a relevant video, poster image, readable text layer,
mobile behavior and performance fallback are available. Do not replace the Hero with a message slider by default.

## 2. Logo Or Trust Strip

Use real customer, partner, certification or technology logos only when their use is permitted. Keep the strip
visually secondary to the Hero.

Reference profile:

- one row
- logo height around `40`
- normal speed with restrained motion
- normal-to-large desktop gaps and smaller mobile gaps
- optionally dim items until hover when this remains accessible
- no duplicated or invented logos

Use either a verified media folder or explicit verified entries. Never reuse a media folder ID from another project.
If logos are unavailable, ask whether suitable media should be created, use a verified factual trust signal as an
agreed substitution or leave the section out. Do not create an empty strip.

## 3. Benefits

Introduce the everyday problem and translate it into concrete outcomes before explaining the process. Use a
MultiLayout with equal cards, normally based on `core:3x2-equal` for three meaningful benefits.

- Use one section overline, one `h2` and a short explanatory paragraph.
- Give every card one short `h3` and one concrete benefit statement.
- Stack cards in a single column on mobile.
- Use normal grid gaps and medium vertical spacing.
- Use the template palette or project tokens for card backgrounds.

Three cards are the reference, not a content quota. Select an equivalent installed preset when two or four well
supported benefits fit better. Never add weak claims merely to fill a layout.

## 4. Process

Explain how the customer reaches the promised result. Use a vertical MultiLayout based on
`core:3rows-middle-full`; use one nested `core:2-equal` sublayout for each step and alternate text and media on
desktop. Stack each step in a logical text/media order on mobile.

Reference profile:

- three understandable steps when the real process supports them
- visible step number, `h3`, explanatory text and relevant image per step
- alternating desktop widths around `5/7` and `7/5`
- one-column mobile layout
- generous gap between steps and medium section spacing
- one coherent, subdued background treatment per step

Use the real process and its actual number of useful steps. Select a matching layout instead of inventing steps.
Do not copy reference media IDs. Use existing or newly generated approved media when requested. Otherwise select a
text-led layout instead of leaving empty media columns or inserting unrelated placeholders into publishable content.

## 5. Solution Insight

Make the offer tangible with real screenshots, interface views, deliverables or another approved visual proof. Use a
MultiLayout based on `core:3x2-alternating` with a central explanation, a concrete feature/outcome list and supporting
media tiles.

Reference profile:

- section overline, `h2` and short introduction
- normal grid gaps
- large tile minimum height where the media needs it
- one central explanation tile
- one concise list of real capabilities or outcomes
- only as many media tiles as useful approved visuals exist
- mobile order that presents the explanation before supporting details

Do not publish labels such as “Screenshot 1” as substitutes for missing media. Ask whether suitable screenshots or
images exist or should be created. Use a verified product video as an agreed alternative when it serves the same
proof function; use a text-led proof format when media is intentionally unavailable.

## 6. Customer Proof

Before creating this section, search the target system for existing `CustomerReviewsFlow` or other verified
customer-review bricks. Inspect relevant instances and ask whether their content should be reused, copied into a
dedicated landing-page brick or ignored. Never modify an existing global or inherited review brick for this page.

Use `CustomerReviewsFlow` with real, approved testimonials or with explicitly requested, unmistakable dummy content.
Add a short heading that explains what the reviews demonstrate. For dummy content, use obvious labels such as
`Musterkunde` and `Beispielunternehmen` plus Lorem Ipsum text, and mark the section as test content.

Reference profile:

- two rows only when enough testimonials exist
- normal speed, alternating direction and restrained gaps
- resume delay around `6000`
- full-width visual band with a subtle theme background
- medium top and base bottom spacing

Do not invent realistic names, roles, companies, portraits or quotes that could be mistaken for real proof. When
there is not enough credible material for a moving flow, ask whether to use dummy testimonials, use a smaller static
proof format, omit the section or keep the page inactive until proof is supplied. Never create an empty review flow.

## 7. FAQ

Answer objections that could prevent the primary action. Use one column, a right-positioned plus icon and a maximum
list width around `900`.

Reference profile:

- `boxFillAccentOpen` template when available
- `stayOpen: false`
- `openFirst: true`
- large vertical spacing
- FAQ structured data only when every visible question and answer is factual and suitable for it

Use only useful questions supported by the offer. Do not create a fixed count merely to fill the page.

## 8. Final CTA

Repeat the primary action after resolving the main objections. Use a visually distinct Wallpaper with one overline,
one `h2`, a concise reassurance and one primary CTA.

Reference profile:

- centered content
- maximum content width around `760`
- minimum height around `500px`
- strong theme-compatible background and accessible contrast
- no competing secondary action
- CTA target identical to, or fully consistent with, the Hero's primary action

## Content And Implementation Rules

- Use exactly one `h1`; continue sections with semantic `h2` and card/step titles with `h3`.
- Use meaningful anchors and verified internal QUIQQER links.
- Generate complete draft headlines, body copy and CTA wording from the supplied offer, audience and conversion goal.
  Keep content specific and persuasive; avoid generic filler in a publishable page.
- Treat repeat counts as content-driven. Preserve the section function and responsive layout instead of forcing an
  artificial number of cards, steps, screenshots, reviews or questions.
- Treat control settings returned by the target system as authoritative. Build complete MultiLayout documents and
  complete `entries` values; never send partial serialized structures.
- Create dedicated brick instances for this landing page unless reuse is explicitly intended.
- Do not store reference project names, page IDs, brick IDs, assignment UIDs or media IDs in the implementation.
- Do not silently omit an unsupported required section and do not create it empty. Report the missing material and
  obtain a decision about existing content, generation, dummy content, substitution or omission before changing the
  approved recipe.

## Completion Criteria

- `headerSuffix` and `footerPrefix` contain the intended sections in the required order.
- Navigation and footer origin are documented and no inherited footer is duplicated.
- All CTAs resolve to the intended action.
- The page has no invented factual claims or proof that could be mistaken for real. Any dummy testimonials are
  unmistakably labelled as test content.
- Every media section uses approved existing or generated media, or a deliberately media-free layout.
- The required visual baseline or the accepted external reference is visibly reflected in surfaces, cards, media
  treatment, section separation and vertical rhythm.
- Desktop and mobile output preserve heading hierarchy, reading order, contrast and keyboard usability.
- Every created brick and affected Area has been reloaded and compared with the intended recipe.
