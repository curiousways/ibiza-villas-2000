# Bob API e2e suite (Playwright)

Automates `docs/testing/bob-e2e-test-suite.md`. First tranche covers
suite **section 1** (hero search + date-range picker) and the
URL-driven parts of **section 4** (booking-confirmation param handling
and tampering).

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

## Not yet automated

Sections 2 (listing grid), 3 (enquiry panel, incl. mocked Bob API
responses for unavailable/error/race cases), 5 (accommodation forms),
and the live-API smoke tests. Planned as the next tranches.
