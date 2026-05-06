# Ibiza Guide: FacetWP filter + pagination

> **For Claude Code:** Implement the brief below. Read the relevant
> codebase files before starting. Push back on anything that doesn't
> make technical sense — ask rather than guess. Approve file writes
> per file. When done, commit with a meaningful message and report
> what you did.

> Before implementing, scrutinise these instructions and raise any
> concerns or suggest alternatives — but only where there is a genuine
> technical reason to do so. Do not flag things for the sake of it.

## Brief type

Plugin integration (greenfield for the project — first FacetWP
work in this codebase). Wires the existing `article-grid` section
into FacetWP for AJAX filtering and pagination on the Ibiza Guide
page. Light modification to the existing section to make it
FacetWP-aware via opt-in arg. Two new facets configured. Page
template updated to render the facets around the grid.

**FacetWP status on staging:** installed (vanilla, no
configuration). The brief covers all configuration from scratch.

**Design reference:** Figma file `3g57x18kqjjNtnJ9SVgTUI`,
"Ibiza Guide" page (frame `04a`). Category filter row sits below
the featured-article block, above the grid. Pagination sits
below the grid.

## Scope

**In:**

1. Configure two FacetWP facets — **category** (auto-derived from
   WP categories, single-select) and **pager** (pagination).
2. Make the existing `sections/article-grid/article-grid.php`
   FacetWP-aware via a new opt-in `facetwp` arg (default `false`).
3. Update `page-ibiza-guide.php` to render the category facet
   above the grid and the pager facet below it.
4. Style the facets to match the design (horizontal underlined
   active state for the category filter; standard pagination
   styling for the pager).

**Out:**

- Any change to `article-card` or `featured-article` components
- Auto-detection of FacetWP on archive pages (we are using a
  custom listing pattern)
- Multi-select categories, AND/OR behaviour, search facets,
  date facets — none specced in design
- Any change to the homepage `ibiza-guide-preview` section
  (not FacetWP-aware; intentionally so — homepage shows three
  static cards, no filtering)

## Standards

- FacetWP configuration code lives at
  `mu-plugins/ibv-core/includes/integrations/facetwp.php` (new
  file). Follows the project's PHP-first convention so config is
  version-controlled, not buried in the WP DB.
- Facet styling lives at
  `mu-plugins/ibv-core/includes/integrations/facetwp.css` (new
  file).
- BEM naming for any custom wrappers; FacetWP's own classes
  (`.facetwp-facet`, `.facetwp-radio`, `.facetwp-pager`, etc.)
  are targeted directly in CSS.
- The `article-grid` section remains generic and reusable — the
  FacetWP integration is opt-in via arg, not assumed.
- No hardcoded category list. Categories auto-derive from WP
  taxonomy.

## Files

```
NEW    mu-plugins/ibv-core/includes/integrations/facetwp.php
NEW    mu-plugins/ibv-core/includes/integrations/facetwp.css

EDIT   mu-plugins/ibv-core/includes/sections/article-grid/article-grid.php
                                          (add 'facetwp' arg + wrap output)
EDIT   themes/ibv/page-ibiza-guide.php   (render facets around the grid)
EDIT   mu-plugins/ibv-core/bootstrap.php (require integration file)
EDIT   mu-plugins/ibv-core/includes/shared-assets.php  (register integration CSS)
```

If during execution the agent finds FacetWP can't be configured
fully via code (e.g. the project has an unsupported FacetWP
version, or a particular setting truly requires the UI), document
the UI steps in `facetwp.php` as a comment block at the top so the
configuration is reproducible. Do not proceed silently with UI-only
config — version-controlled config is the goal.

## Change 1 — Configure FacetWP facets

Verify FacetWP's recommended integration pattern from official
docs before implementing. The expected approach:

- Two facets registered: `category` (type appropriate for
  single-select horizontal filter — likely "Radio" or similar) and
  `paginator` (FacetWP's pager type).
- Category facet sources from `tax/category` so terms auto-derive.
- Single-select behaviour. The FacetWP-rendered "All" / cleared
  state shows everything.

Programmatic registration is preferred. FacetWP's filter or hook
for facet registration should be used — the agent should verify
the exact name from FacetWP's current docs. If programmatic
registration isn't supported on the installed version, fall back to
UI configuration with the steps documented as a comment header in
`facetwp.php`.

The integration file should also handle anything else FacetWP needs
to recognise the listing — e.g. a listing template registration if
required by the chosen integration mode.

## Change 2 — Make `article-grid` FacetWP-aware

Add a new arg to `ibv_core_section_article_grid()`:

```php
$defaults = [
    'exclude'        => [],
    'posts_per_page' => 9,
    'surface'        => 'bg',
    'facetwp'        => false,   // ← new
];
```

When `facetwp => true`:

1. Add `'facetwp' => true` to the `WP_Query` args so FacetWP
   intercepts the query.
2. Remove `'no_found_rows' => true` from the query args (FacetWP
   needs the count for pagination metadata).
3. Wrap the loop output in `<div class="facetwp-template">…</div>`
   so FacetWP can target it for AJAX updates.

When `facetwp => false` (default), behaviour is unchanged from
brief 02 — same query, same output.

Read the current `article-grid.php` first to confirm the exact
shape of the existing query before modifying. The Step 11 read
matters here — brief 02 just shipped, the file is fresh.

## Change 3 — Page template

Update `themes/ibv/page-ibiza-guide.php` to render the facets:

```php
// Above the grid:
echo facetwp_display( 'facet', 'category' );

// The grid call gains the facetwp flag:
ibv_core_section_article_grid( [
    'exclude' => $featured_id ? [ $featured_id ] : [],
    'facetwp' => true,
] );

// Below the grid:
echo facetwp_display( 'facet', 'paginator' );
```

The exact facet names (`category`, `paginator`) must match the
slugs registered in `facetwp.php`. Use whatever slug the agent
chooses there; just keep them aligned.

Wrap the facet output in a project-conventional container so the
section system spacing applies. Don't let FacetWP markup dictate
page spacing — wrap it in our own `<section class="ibv-section
ibv-section--surface-{X}">` so the rhythm system stays consistent.

The category facet section likely sits on the same surface as the
grid (`bg`) — verify against the design. Adjacent same-surface
sections will collapse padding correctly per the section rhythm
system.

Remove the `TODO brief 03` comment that brief 02 left in the
template.

## Change 4 — Styling

The category facet in the design is a horizontal row of category
labels with the active option underlined and slightly bolder.
FacetWP's default radio rendering is vertical; we override.

In `facetwp.css`:

- Target `.facetwp-facet[data-name="category"]` (or whichever slug
  is used) with `display: flex` / horizontal layout
- Hide FacetWP's default radio button input; style the labels as
  the click targets
- Active state: underline + bold (or weight bump). Use existing
  tokens — gold accent or sage rule per the design's existing
  patterns
- Hover state: subtle (opacity or underline preview)

For the pager:

- Target `.facetwp-pager` with the project's spacing tokens
- Active page indicator styled with the project's accent colour
- Prev/next link styling matching the project's link conventions

If FacetWP's class names differ from what's described above, the
agent uses whatever is correct — these are recommendations, not
prescriptions.

## Verification

1. **Manual admin / data setup:**
   - Confirm FacetWP is active and licensed
   - Confirm at least two WP categories with multiple posts each
     so filtering has visible effect
   - Confirm 10+ posts total so pagination has at least two pages
     when `posts_per_page` is 9

2. **Frontend smoke test on the Ibiza Guide page:**
   - Page loads with hero + featured + filter row + grid + pager
     + newsletter
   - Filter row shows all categories from the taxonomy, in
     alphabetical (or term order) — no hardcoded list
   - Click a category → grid filters to posts in that category
     (AJAX, no full page reload)
   - URL updates to reflect facet state (FacetWP's default
     behaviour — verify this is on)
   - Browser back button restores previous filter state
   - Click "All" or unselect → grid shows all posts again
   - Pagination: click page 2 → grid shows next page (AJAX)
   - Featured article does **not** appear in any filtered or
     paginated state (the `exclude` arg still works)
   - Mobile: filter row scrolls horizontally if it overflows;
     does not break layout

3. **Visual match to design:**
   - Filter row matches design: horizontal labels, active state
     underlined + bolder, hover state subtle
   - Pager matches the project's other pagination patterns
     (verify there are no other paginators in the codebase first;
     if there are, this one matches their style)

4. **Regression test on the homepage:**
   - The `ibiza-guide-preview` section still renders three
     static cards with no FacetWP markup or behaviour.
   - The article-card component is unchanged.

5. **Token + system compliance:**
   - All custom CSS uses existing tokens
   - Section rhythm system is intact (no broken padding-block
     between adjacent sections)

## Notes

- **Why programmatic facet registration over UI.** The IBZ002
  codebase is PHP-first: ACF in PHP, sections registered in code,
  no admin UI clicking. Facet config in code keeps the convention
  and makes the configuration version-controlled. Falling back to
  UI is acceptable only if the installed FacetWP version doesn't
  support code registration; document the UI steps if so.
- **Why `facetwp` is opt-in on the section.** The article-grid
  section is intentionally generic — usable on any page that wants
  a list of recent posts. Forcing FacetWP integration on every
  caller would couple the section to a plugin most callers won't
  use. Opt-in arg keeps the section reusable.
- **Why no auto-detection.** FacetWP can auto-detect main queries
  on archive pages, but the Ibiza Guide page is a custom page
  template, not an archive. Custom listing integration is the
  correct pattern.
- **Pagination + dedupe interaction.** `post__not_in` with the
  featured ID still works under FacetWP — FacetWP filters
  *additionally* on top of the base query, it doesn't replace it.
  Verify this in testing: pagination should never include the
  featured post on any page.
- **No JS in this brief.** FacetWP handles all the AJAX,
  URL state, and interactivity. The work is config + CSS + light
  PHP wrapping, nothing else.
- **Step 12 reminder.** If small in-scope improvements surface
  during this brief — e.g. a missing escape, a stray hardcoded
  spacing value in the article-grid CSS — fix them in the same
  commit and note in the message. Larger or out-of-scope
  observations: surface, don't act.
