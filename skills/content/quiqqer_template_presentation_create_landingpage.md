---
name: quiqqer_template_presentation_create_landingpage
description: Use when a user naturally asks to plan, create or rebuild a landing page in a QUIQQER project that uses quiqqer/template-presentation or a child template. Turn a product or project briefing into a complete draft, ask only for essential missing content, media and design decisions, inspect the page context, navigation and footer, then select and apply a numbered landing-page recipe.
category: content
---

# Create A Template Presentation Landing Page

Use this skill as the entry point for every landing-page task. Do not start creating pages or bricks before the
project context, page shell and suitable recipe are known.

Do not require the user to know skill names. When this skill was selected through QUIQQER skill discovery, load its
dependencies and the matching recipe yourself.

## Load Required Skills

- Load `quiqqer_bricks_create_and_edit_blocks` for every brick operation.
- Load `quiqqer_template_presentation_css_classes` for content markup and styling.
- Load the selected numbered recipe completely before changing CMS data.
- Load package-specific skills when a selected control is supplied by another package.
- Use `quiqqer_template_presentation_landingpage_01` only when its conversion goal and content flow fit.

## Establish The Brief

Determine from the request and available project sources:

- target project, language and template
- existing target page or required parent for a new page
- offer, audience and primary conversion action
- one primary CTA and, only when useful, one subordinate secondary CTA
- available factual claims, references, testimonials, logos, media and legal pages
- desired tone and brand constraints

The minimum content brief consists of the offer, its target audience and the primary conversion action. Ask one
compact set of questions when any of these are missing; do not create an empty placeholder page instead. Derive
answers from the user's product description and available project sources where safe, and do not ask the user to
repeat information already supplied.

Create complete, persuasive draft copy from a sufficient brief. Ordinary headlines, benefit-oriented wording,
transitions and CTA copy are editorial work and should not remain generic placeholders. Never invent prices,
figures, guarantees, certificates, real customers, real testimonials, product capabilities or legal statements.
Label unresolved factual claims as draft or ask for the missing fact before using them as proof.

## Inspect The Page Context

1. Confirm that the target uses `quiqqer/template-presentation` or a compatible child template.
2. Load the target page and its complete attributes. For a new page, load the intended parent and project root.
3. Load all brick Areas for the target, its ancestors and any relevant reference page.
4. Distinguish local assignments from inherited assignments. Inspect assignment `customfields`, especially
   `inheritance`, instead of treating an empty local Area as an empty rendered Area.
5. Inspect existing bricks before deciding whether to reuse them. Never modify a global or inherited instance when
   the change is intended only for the landing page.
6. Verify every required control and its current settings schema with `quiqqer_brick_types_list` and
   `quiqqer_brick_types_get`. Do not guess control identifiers, settings, Areas or package availability.

## Establish The Design Direction

Treat the target page and the visual reference as separate decisions. Do not select an arbitrary existing page as a
design reference merely because it belongs to the same project or is the intended parent.

1. Ask whether the landing page should fit the existing website design or use a deliberately independent campaign
   design when the request does not make this clear.
2. For an integrated design, ask for a representative existing page or a screenshot. Help select a suitable page
   only when the user has not supplied one.
3. Use an existing page as a visual reference only when its template or child template is compatible. Inspect its
   rendered desktop and mobile appearance when browser access is available.
4. When a screenshot is supplied, inspect its visible typography, color use, buttons, surfaces, corner treatment,
   spacing, container widths and section rhythm. Reproduce the design language with available template settings and
   controls without copying unrelated content.
5. Inspect the target project's template, child-template additions, design settings, tokens and representative brick
   configurations. Reuse established patterns before adding scoped custom CSS.
6. Do not change global theme settings to make one landing page match a reference. Report design elements that the
   installed template or controls cannot reproduce safely.

Modify an existing page only when the user explicitly identifies or confirms it as the target. Otherwise create a
new inactive page. Never overwrite an existing page merely because it was inspected as a visual reference.

Keep an inactive draft's existing SEO, robots, sitemap and search-index settings unchanged unless the user explicitly
asks to change them. In particular, do not set `noindex,nofollow` merely because a newly created page is inactive;
the activation state already controls whether the draft is publicly available.

## Decide Media And Customer Proof

Ask explicitly whether suitable images, screenshots and logos already exist or whether new images should be created.
When existing media should be used, inspect the target project's media rather than copying foreign media IDs. When
generation is requested and an image-generation capability is available, create suitable assets for the approved
visual direction. Define one art direction for all generated assets on the page, including palette, rendering style,
perspective, lighting and image proportions. If neither existing nor generated media should be used, choose layouts
that work without media; do not leave empty image tiles or large blank sections.

Before deciding the customer-proof section, search the target system for existing brick instances based on
`\QUI\Bricks\Controls\Slider\CustomerReviewsFlow` or another verified customer-review control. Inspect relevant
instances without changing them and ask whether their content should be reused, copied into a dedicated brick or
ignored.

If usable customer proof is unavailable, ask whether the user wants to provide real testimonials, use clearly dummy
testimonials or replace/omit the section. Dummy testimonials are allowed when requested, but must be unmistakable
test content, for example Lorem Ipsum with names such as `Musterkunde` and companies such as `Beispielunternehmen`.
Never present generated dummy content as real proof.

## Decide Navigation And Footer

Record one explicit decision for navigation and one for the footer before applying the recipe.

### Navigation

Choose one of these outcomes:

- reuse the existing navigation unchanged
- use a deliberately reduced landing-page navigation
- create the missing navigation for a new project or page tree

The navigation is created by the template's configured menu. `navAction` is only an additional action Area and is
not the menu itself. Inspect the menu configuration, logo, links and optional independent menu ID separately. Do not
simulate a missing menu by filling `navAction`. If the available tools cannot inspect or change the menu safely,
report that limitation and request the missing configuration instead of guessing.

A reduced navigation should normally contain the brand, only useful orientation or anchor links, and the primary
CTA. Reuse a full inherited navigation when the landing page should remain visibly embedded in the website.

### Footer

Choose one of these outcomes:

- reuse an existing inherited footer
- reuse an existing local or project footer
- create a missing footer

Inspect `footerPrefix` and `footer` independently. The final landing-page CTA belongs to `footerPrefix`; it is not the
footer. Do not duplicate inherited footer bricks locally. When creating a footer, use verified project information
for the brand/contact block and existing internal targets for legal links. Set inheritance deliberately if the new
footer should serve descendants. Do not invent legal URLs or pages.

## Select A Recipe

Select by conversion logic, not by visual preference alone. Recipe 01 fits an explanatory offer with one enquiry or
contact goal. If the goal requires a substantially different flow, such as checkout, event registration, recruiting
or a download funnel, do not force Recipe 01. Explain that a new numbered recipe is needed and agree its structure
before building it.

## Handle Section Alternatives

Use each recipe's named control and structure as the default. Substitute a section only when the alternative serves
the same marketing function and the available content justifies it.

For every substitution:

1. State the section's marketing function.
2. Verify the alternative control and settings in the target system.
3. Check its required content, media, allowed Areas, accessibility and responsive behavior.
4. Keep the recipe's information hierarchy and CTA priority.
5. Load the providing package's skill for technical details instead of duplicating its full documentation here.
6. Record the substitution in the handover.

Examples include a video Hero instead of a static Hero when relevant video and a poster image exist, factual metrics
instead of a logo strip when usable customer logos do not exist, or a product video instead of a screenshot grid.
Do not use a slider merely to display several competing Hero messages; focused landing pages need one primary
promise.

## Apply The Recipe Safely

1. Create a new page inactive or keep an explicitly confirmed existing target's activation state until the result is
   ready.
2. Reuse an existing local brick only when its purpose and ownership match. Otherwise create a dedicated instance.
3. Create and populate bricks from top to bottom according to the recipe.
4. Treat every `settings` update as read-modify-write and send the complete merged object.
5. Treat JSON strings inside settings, such as `entries` or MultiLayout documents, as structured data and inspect
   them recursively.
6. Read an Area's complete ordered assignment list immediately before changing it. `quiqqer_site_bricks_set_area`
   replaces the complete list, so preserve unrelated assignments and assignment custom fields.
7. Use new assignment UIDs generated by the system. Never copy page IDs, brick IDs, UIDs, media IDs or project names
   from a reference project.
8. Keep custom CSS scoped to the brick and use the template design system before adding custom rules.
9. Activate newly created content only after the assignments and rendered result are complete and approved.
10. Do not create empty required sections. Resolve missing content through a question, an approved fallback or an
    intentional omission before writing the affected Area.

## Perform A Visual Quality Gate

Treat the rendered result as part of the implementation, not as optional polish.

1. Render the completed page in desktop and mobile view through an available preview or authenticated browser.
2. Compare it with the accepted reference page or screenshot. When no external reference was selected, compare it
   with the recipe's required visual baseline.
3. Check that every major section has a deliberate surface, card, media treatment or other visual anchor. Look for
   excessive empty space, text floating without structure, inconsistent image styles, weak section separation,
   uneven container widths and unbalanced vertical rhythm.
4. Correct control and MultiLayout settings first. Add scoped custom CSS only for visual requirements that the
   verified settings and template design system cannot express.
5. Render again after corrections. Do not call the page visually finished based only on correct Brick order and
   settings data.

If no rendered preview can be inspected, report the visual check as unavailable and do not claim that the page is
visually complete.

## Verify And Report

- Reload every created or changed brick including attributes and full settings.
- Reload all affected Areas and confirm exact order, UIDs and preserved unrelated assignments.
- Confirm whether navigation and footer are local, inherited, reused or newly created.
- Confirm the selected design direction and whether its reference was an existing page, a screenshot or an
  independent campaign design.
- Confirm whether media was reused, generated or intentionally omitted and identify dummy customer proof clearly.
- Record the result of the desktop and mobile visual quality gate or its explicit unavailability.
- Check heading order, one page `h1`, CTA targets, keyboard access, contrast and meaningful alternative text.
- Report the page, recipe, created/reused bricks, Area order, shell decisions, remaining content gaps and unavailable
  checks.
