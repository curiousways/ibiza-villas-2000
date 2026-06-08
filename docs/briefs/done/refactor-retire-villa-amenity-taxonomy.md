# Retire the orphaned `villa_amenity` taxonomy

> **For Claude Code:** Implement the brief below. Read the relevant
> codebase files before starting. Push back on anything that doesn't make
> technical sense — ask rather than guess. Approve file writes per file.
> When done, commit with a meaningful message and report what you did.

> Before implementing, scrutinise these instructions and raise any concerns
> — but only where there is a genuine technical reason to do so.

---

## Brief type

**Refactor / removal.** No design reference. Front-end is a **visual
no-op** — nothing on the public site renders `villa_amenity`. The only
visible change is in wp-admin: the "Amenities" taxonomy menu and the
villa-list "Amenities" column disappear.

---

## Why

`amenity-ticks` now reads its pills from the `property_features` ("Property
Facts") ACF repeater, not the `villa_amenity` taxonomy. That leaves
`villa_amenity` registered and visible in admin but rendered nowhere —
a duplicate "amenities" surface an editor could mistakenly populate
instead of Property Facts. Retire it.

---

## Precondition — verify before removing

Confirm nothing in the **active `ibv` build** still reads the taxonomy
before deleting it. Grep `villa_amenity` across `mu-plugins/ibv-core` and
`themes/ibv`. Expected matches after the recent amenity-ticks change:

- `includes/taxonomies/villa-amenity.php` (the registration — being removed)
- `includes/admin/admin-columns-villa.php` (the admin column — being removed)
- `includes/acf/register-villa-fields.php` (a stale code comment — being fixed)

If `amenity-ticks.php` (or any other front-end renderer) still references
`villa_amenity`, **stop and flag it** — the data-source switch didn't land
and removing the taxonomy would break the pills. (Matches in the legacy
`themes/ibiza-villas-2000` theme are expected and irrelevant — that theme
is not active. Do not touch it.)

---

## Changes

**1. Delete the registration file**

```
DELETE  mu-plugins/ibv-core/includes/taxonomies/villa-amenity.php
```

**2. Remove the bootstrap require** — `mu-plugins/ibv-core/bootstrap.php`

Delete this line (currently ~line 29), leaving the other three taxonomy
requires intact:

```php
require_once IBV_CORE_PATH . 'includes/taxonomies/villa-amenity.php';
```

**3. Remove the admin column** — `mu-plugins/ibv-core/includes/admin/admin-columns-villa.php`

In `ibv_villa_admin_columns()`, delete the `villa_amenity` line from the
`$taxonomies` array (keep `property_location` and `villa_poi`):

```php
$taxonomies = array(
	'taxonomy-property_location' => __( 'Location', 'ibv' ),
	'taxonomy-villa_poi'         => __( 'POIs', 'ibv' ),
);
```

(The column was rendered natively by WP from the taxonomy registration;
there's no entry in the `..._custom_column` render switch or the sortable
map to remove. Confirm by reading the file.)

**4. Fix the stale comment** — `mu-plugins/ibv-core/includes/acf/register-villa-fields.php`

The comment above the rating field (~line 786) still claims amenity ticks
use the taxonomy. Update it to reflect reality, e.g.:

```php
// Pass 3c-detail — villa detail / listing support. Amenity ticks render
// from the property_features repeater above; villa_amenity taxonomy retired.
```

---

## Smoke test

1. WP admin → Villas: no "Amenities" submenu under the Villas CPT; the
   villa list table no longer shows an "Amenities" column. "Location" and
   "POIs" columns remain.
2. Public villa detail page: amenity pills still render from Property Facts
   exactly as after the overview redesign — unchanged.
3. No PHP notices/warnings on the villa list screen or a villa page.

---

## Notes

- **Orphaned terms:** any `villa_amenity` terms already created sit
  harmlessly in the DB once the taxonomy is unregistered (just
  inaccessible). Not worth a migration to purge; ignore unless you spot a
  reason otherwise.
- **Alternative if reversibility is wanted (not the default here):** instead
  of deleting, the taxonomy could be hidden by setting `show_ui`,
  `show_in_menu` and `show_admin_column` to `false` while leaving it
  registered — preserving the structure if amenities-as-taxonomy (for
  filtering or schema) is ever revived. We chose full removal because it's
  genuinely unused and git history preserves the file. Flag if you think
  hiding is the safer call.
