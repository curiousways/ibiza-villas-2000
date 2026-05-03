# ACF — registration conventions

All ACF field groups are registered in PHP. We do **not** use ACF JSON sync.

## File-per-group

Each ACF group lives in its own file under `includes/acf/`. Filename convention:
`register-{scope}.php` (e.g. `register-options.php`, `register-page-home.php`,
`register-cpt-article.php`).

After adding a new file, `require_once` it from `bootstrap.php` in the ACF
section.

## Hook order

Always hook to `acf/init` — never `init` or `plugins_loaded` — so the ACF
functions are guaranteed to be defined.

## Naming

- Group key:    `group_ibv_{scope}`             e.g. `group_ibv_page_home`
- Field key:    `field_ibv_{scope}_{name}`      e.g. `field_ibv_page_home_intro`
- Field name:   snake_case, no prefix            e.g. `intro`, `featured_items`
- Layout key (for flexible/clone): `layout_ibv_{scope}_{name}`

## Location targeting

Page templates: target by `page_template` so the group only appears when
the page is using that template. Example:

```php
'location' => [
    [
        [ 'param' => 'page_template', 'operator' => '==', 'value' => 'page-home.php' ],
    ],
],
```

CPT singulars: target by `post_type`.

## What goes where

- Site-wide options → `register-options.php`.
- One ACF group per page template → `register-page-{slug}.php`.
- One ACF group per CPT → `register-cpt-{slug}.php`.
- Reusable field groups (e.g. `seo`) used via clone fields → `register-shared-{name}.php`.

