# Content and deploy — staging is source of truth

**From 16 Aug 2026: staging is the content source of truth.** The client
edits posts, villas, pages, ACF, options, taxonomies and menus on
staging. Local is for code and isolated testing (including the Gravity
Forms 3.0 upgrade).

**Never push the local database to staging.** A table push replaces
those tables wholesale and would silently wipe client edits.

## What still goes local → staging

Code only, via git push to `staging` (Buddy). Theme, mu-plugin, and
any committed assets. No `wp migratedb push`.

## If local needs a content refresh

Pull **from** staging (never the other way). Exclude Gravity Forms
tables — form definitions and entries stay per-environment.

```bash
URL=$(wp eval '$p=get_option("wpmdb_saved_profiles");$v=json_decode(reset($p)["value"],true);echo $v["connection_info"]["connection_state"]["url"];' 2>/dev/null)
KEY=$(wp eval '$p=get_option("wpmdb_saved_profiles");$v=json_decode(reset($p)["value"],true);echo $v["connection_info"]["connection_state"]["key"];' 2>/dev/null)

wp migratedb pull "$URL" "$KEY" \
  --include-tables=wp_posts,wp_postmeta,wp_options,wp_terms,wp_termmeta,wp_term_taxonomy,wp_term_relationships,wp_redirection_items \
  --preserve-active-plugins \
  --backup=selected
```

Do this only when local is missing content needed for a code task.
Do not make it a habit after every staging edit.

## Deliberately excluded (both directions)

- **`wp_users` / `wp_usermeta`** — would overwrite logins.
- **Gravity Forms tables** — forms and entries are per environment.
  Edit live forms on staging; test GF 3.0 against the local copies.
- Plugin log / audit tables (`wp_wsal_*`, `wp_rg_*`, `wp_check_email_log`).

## Retired: local → staging push

The old command was `wp migratedb push` with the same table list. It
is retired. Do not run it. History of why a full-DB push fails (GF
entry volume, no WP-CLI on staging) is in git history for this file.
