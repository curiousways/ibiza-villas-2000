# Villa Detail — Retire the "Property More Information" fields

> **For Claude Code:** Implement the brief below. Read the relevant
> codebase files before starting. Push back on anything that doesn't make
> technical sense — ask rather than guess. Approve file writes per file.
> When done, commit with a meaningful message and report what you did.

> Before implementing, scrutinise these instructions and raise concerns or
> suggest alternatives — but only where there's a genuine technical reason.
> Do not modify files outside the list below.

---

## Brief type & purpose

**Field retirement (non-destructive cleanup).** Remove the three unused
"Property More Information" ACF field definitions from the villa field
group. They were part of a legacy "More Information" reveal block that the
new build does not render and we are not reinstating.

**Fields to remove** (all in `register-villa-fields.php`):

| Label | Name | Key | Type |
|---|---|---|---|
| Property More Information Title | `property_more_info_title` | `field_5582c2d37ad0b` | text |
| Property More Information Intro | `property_more_info_intro` | `field_5582cbfec8f44` | textarea |
| Property More Information Content | `property_more_info_content` | `field_5582c31c7ad0c` | wysiwyg |

**This is non-destructive.** Removing the field *registrations* stops them
appearing in the editor and being used; it does **not** delete the stored
postmeta. Do not write any code to delete postmeta, run no cleanup query —
just remove the registrations. The data stays in the DB, orphaned and
recoverable.

---

## Pre-flight (verify before editing)

Confirm nothing in the build consumes these before removing them:

```
grep -rn "property_more_info" mu-plugins themes/ibv
```

The only matches should be the three definitions in
`register-villa-fields.php`. **If any template, section, or helper reads
these fields, stop and flag it** — don't remove a field something renders.
(Expected result: no consumers — they're already unrendered in the new
build.)

---

## File to edit

```
EDIT  mu-plugins/ibv-core/includes/acf/register-villa-fields.php
```

---

## What to remove

The three fields are the **last three entries** in the field group's
`fields` array, immediately before the array closes and the `'location'`
key begins (right after the "Property Video" field).

Remove the three complete `array( … ),` field-definition blocks — each
runs from its opening `array(` to its matching `),`. Identify them by the
keys / names in the table above (don't rely on line numbers — they drift).

After removal:

- The field immediately before them ("Property Video", `property_video`)
  becomes the last entry in the `fields` array — its trailing `,` before
  the array's closing `)` is valid, leave it.
- The `'location'`, `'menu_order'`, `'position'`, etc. group config that
  follows the `fields` array is untouched.
- The field group's opening, its other fields, and all group settings
  stay exactly as they are.

Verify the file still parses (`php -l` if available) — the edit is purely
removing three array elements; nothing else changes.

---

## Smoke test

1. Villa edit screen: the "Property More Information Title / Intro /
   Content" fields no longer appear in the Villa field group. Every other
   villa field is still present and in the same order.
2. No PHP errors / warnings on a villa edit screen or a front-end villa
   page.
3. Front-end villa pages render exactly as before (they never rendered
   these fields).
4. ACF doesn't report the group as "out of sync" / broken on the Field
   Groups screen.

---

## Notes

- **Postmeta is preserved** — this only removes the field registrations.
  Any villa that had More Information content keeps that data in the DB
  (orphaned); re-adding the fields later would surface it again.
- **Content check (for David, not the agent):** the legacy site renders a
  per-villa "More Information" reveal only when
  `property_more_info_content` is non-empty. Before launch, confirm none
  of the 15 villas have live content there worth keeping — if any do, fold
  it into the main villa description (`the_content`) rather than losing it.
  This retirement doesn't destroy that data, so it isn't blocking.
- **`global_villa_features` is separate** — the legacy "More Information"
  template also echoed a global features Site Option. That's a different
  field and is **not** part of this retirement; don't touch Site Options.
- If the file differs from what's described, surface it rather than
  reshaping around it.
