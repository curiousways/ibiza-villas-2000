# Maintenance

## Monthly WordPress updates (agent playbook)

The Cursor skill **`ibv-wordpress-updates`** lives outside the repo (same
pattern as Statamic):

`~/.cursor/skills/ibv-wordpress-updates/`

Trigger phrases: “monthly WordPress updates”, “IBV updates”, “WP Rocket”,
“run the IBV playbook”.

| Skill file | Covers |
| --- | --- |
| `SKILL.md` | Principles, anti-patterns |
| `monthly-run.md` | Local → staging → live phases + GF checklist |
| `gf-surface.md` | Theme/mu-plugin Gravity Forms customisations |
| `wp-rocket.md` | Cache policy, exclusions, warm-cache tests |
| `gotchas.md` | Past failures |

Related in-repo docs:

- `docs/deploy/README.md` — Buddy staging/live pipelines
- `docs/content-push-runbook.md` — staging content SoT; no local DB push
- `docs/testing/bob-e2e-test-suite.md` — cache-warm API/price checks
- `docs/briefs/done/feature-cookie-consent.md` — Rocket + cookieconsent

Per-run write-ups: `docs/upgrades/<YYYY-MM>.md` (create on first run).
