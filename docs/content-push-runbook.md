# Content push runbook — local → staging

**Workflow rule: the local DB is the content source of truth.** All content
editing (posts, villas, pages, ACF fields, options-page content, taxonomies,
menus) happens locally, then gets pushed to staging. Nobody edits content on
staging — every push replaces the pushed tables wholesale, so any staging-side
edit is silently lost on the next push.

## The push command

```bash
URL=$(wp eval '$p=get_option("wpmdb_saved_profiles");$v=json_decode(reset($p)["value"],true);echo $v["connection_info"]["connection_state"]["url"];' 2>/dev/null)
KEY=$(wp eval '$p=get_option("wpmdb_saved_profiles");$v=json_decode(reset($p)["value"],true);echo $v["connection_info"]["connection_state"]["key"];' 2>/dev/null)

wp migratedb push "$URL" "$KEY" \
  --include-tables=wp_posts,wp_postmeta,wp_options,wp_terms,wp_termmeta,wp_term_taxonomy,wp_term_relationships,wp_redirection_items \
  --preserve-active-plugins \
  --backup=selected
```

For media (files in `wp-content/uploads`), add:

- `--media=all` — first run, or if unsure.
- `--media=since-date --media-date=YYYY-MM-DD` — subsequent runs, faster.

## Why these tables

| Table(s) | Carries |
| --- | --- |
| `wp_posts`, `wp_postmeta` | Posts, pages, villas, media library records, nav menu items, all ACF field values on posts |
| `wp_options` | ACF options-page content (testimonials, shared content), theme mods incl. menu locations, widgets |
| `wp_terms`, `wp_termmeta`, `wp_term_taxonomy`, `wp_term_relationships` | Property Location and other taxonomies, menu structure |
| `wp_redirection_items` | Redirection plugin rules. Added 14 Aug 2026 after the batch-2 content pass fixed rules that hijacked live villa permalinks (see `docs/briefs/done/villa-content/RUN-REPORT-BATCH2.md`) — redirects are edited locally like all other content. The plugin's groups/404/log tables stay staging-side. |

## Deliberately excluded

- **`wp_users` / `wp_usermeta`** — pushing would overwrite staging logins.
- **`wp_comments` / `wp_commentmeta`** — not used for content.
- **Gravity Forms tables** — they don't exist in the local DB at all; form
  definitions and entries live only on staging. Edit forms in the staging
  admin. (Staging also has legacy `wp_rg_*`, `wp_wsal_*` and other
  plugin tables local doesn't have — never push those.)

## Safety notes on pushing `wp_options`

- WP Migrate auto-preserves environment-critical rows: `blog_public`
  (search-engine visibility), its own connection settings, upload paths.
- URL and file-path references are find-replaced automatically.
- Transients are excluded by default.
- `--preserve-active-plugins` keeps staging's plugin activation state.
- After the first `wp_options` push, spot-check staging: search visibility,
  menus assigned to locations, mail still sending (Gravity Forms
  notifications use staging-side settings).

## History

- Never do a full-DB push: it times out on the ~150 MB of Gravity Forms
  entries and audit logs (`gf_entry_meta`, `wp_wsal_metadata`,
  `wp_rg_lead_detail`, `wp_check_email_log`) and dies server-side at ~91%.
  The GUI push has the same failure; always use the CLI with
  `--include-tables`.
- Staging has no WP-CLI, so nothing can be fixed server-side from the
  command line — get the push right from local.
