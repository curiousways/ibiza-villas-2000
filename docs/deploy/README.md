# Deploy (Buddy)

Code reaches servers via git + Buddy only. Staging holds content; never
`wp migratedb push` local → staging (see `docs/content-push-runbook.md`).

## Pipelines

| Pipeline | Branch | Trigger | What gets uploaded |
|----------|--------|---------|-------------------|
| **Deploy to staging** | `staging` | Push | Full repo → `/staging.ibizavillas2000.com` |
| **Deploy to live** | `main` | Push (merge to `main`) | Theme + mu-plugins only (see below) |

Manual **Run** remains available for a re-deploy without a new commit.

YAML snapshots (no passwords): `staging-pipeline.yml`, `live-pipeline.yml`.
Auth stays in Buddy; password placeholders in YAML are not secrets.

## Live scope (important)

Live document root (`/httpdocs`) is client-managed and holds extras we
do not own. Deploy **only**:

1. `wp-content/themes/ibv` → `/httpdocs/wp-content/themes/ibv`
2. `wp-content/mu-plugins` → `/httpdocs/wp-content/mu-plugins`  
   (loader `ibv-core.php` + `ibv-core/`; excludes Migrate DB compat shim)

Two SSH targets (same host/user, different **Target path**). Both
transfers use **deletion disabled** and temporary-file upload.

Do not widen live to a full-repo sync unless the client document-root
layout is re-owned by us.

## Day-to-day flow

1. Commit and push to **`staging`** → staging pipeline deploys.
2. Smoke-test https://staging.ibizavillas2000.com
3. Open PR **`staging` → `main`**, merge when ready.
4. Merge to **`main`** → live pipeline deploys automatically.
5. Spot-check https://ibizavillas2000.com

## Keeping repo YAML in sync with Buddy

**Buddy UI is the running config.** The files here are redacted
snapshots for humans/agents — not imported automatically.

After any change in Buddy (targets, paths, triggers, actions):

1. Open the pipeline → **YAML** tab.
2. Copy the full YAML.
3. Paste over the matching file:
   - Deploy to staging → `staging-pipeline.yml`
   - Deploy to live → `live-pipeline.yml`
4. **Redact auth before commit.** Replace every password value with a
   placeholder:
   - staging → `$STAGING_SSH_PASSWORD`
   - live → `$LIVE_SSH_PASSWORD`  
   Strip both plaintext and `!encrypted …` blobs. Never commit those.
5. Commit the updated snapshot with the related work (or a small
   `docs(deploy): …` commit).

Optional sanity check: diff the redacted paste against the previous
file — only intentional Buddy changes should show up.

Do **not** treat the repo YAML as something to paste back into Buddy
without reviewing: placeholders won’t auth, and target IDs can drift.
