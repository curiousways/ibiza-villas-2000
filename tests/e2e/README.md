# Bob API e2e suite (Playwright)

Automates `docs/testing/bob-e2e-test-suite.md`. First tranche covers
suite **section 1** (hero search + date-range picker) and the
URL-driven parts of **section 4** (booking-confirmation param handling
and tampering). Second tranche covers **section 2** (listing grid) and
**section 3** (enquiry panel) with a mocked Bob API.

> **Tranche 2 status:** `tests/villa-listing-grid.spec.ts` and
> `tests/enquiry-panel.spec.ts` were written from the component source
> (selectors and API shapes verified against `villa-listing-grid.php`/
> `.js`, `villa-card.php`, `enquiry-panel.php`/`.js`,
> `seed-villa-enquiry-form.php`) but have **not yet been executed** —
> they were authored without access to a running WordPress site. Run
> them locally (`npm run test:chromium`) and fix any drift before
> trusting them.

## Running

```bash
cd tests/e2e
npm install
npx playwright install   # browser binaries, first run only

npm run test:chromium    # quick pass, one browser
npm run test:desktop     # chromium + firefox + webkit
npm test                 # full device matrix (5 projects)
npm run report           # open the HTML report of the last run
```

Target environment defaults to the local Herd site
(`http://ibiza-villas-2000.test`). Point at staging with:

```bash
BASE_URL=https://<staging-host> npm test
```

## Environment assumptions

- Post IDs in `helpers/test-data.ts` (Villa Daniel 2782, listing page
  18540) match both local and staging, because staging's
  `wp_posts`/`wp_postmeta` were pushed from this local DB. Override via
  `E2E_VILLA_ID` etc. if they diverge.
- `mobile-safari` is emulated WebKit, not a real device — the manual
  iPhone pass in suite section 7 still stands.

## Conventions

- Every test imports `test`/`expect` from `helpers/fixtures.ts`, which
  auto-fails any test that logged a console error or uncaught page
  error (suite section 0's "zero console errors" criterion). Deliberate
  exclusions go in `IGNORED_CONSOLE_PATTERNS` with a comment.
- No test submits a Gravity Form yet. When form-submitting tests land
  (suite sections 3–6), gate them behind an env flag and use
  identifiable test values, per the suite's forms-safety step.

- Tranche 2 mocks the Bob API endpoint with `page.route()`
  (`helpers/bob-api.ts`), so those specs never hit the live PMS and
  pass criteria are deterministic. The mocked responses mirror the
  shape both scripts parse: `{ success, count, query: { nights },
  villas: [ { villa, available, eur_base_rental, eur_total_price,
  eur_adw_amount, eur_extra_cleaning } ] }`.

## Not yet automated

Section 5 (accommodation forms), the form-submitting parts of
sections 3–4 (RTB submit → confirmation redirect), the cross-page
card-price-vs-panel-quote consistency checks, and the live-API smoke
tests. Planned as the next tranches.
