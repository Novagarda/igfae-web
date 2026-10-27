# IGFAE Web

Custom code for the **Instituto Galego de Física de Altas Enerxías (IGFAE)** website: WordPress content plugin and theme.

Developed by [Novagarda](https://novagarda.gal).

**Current release:** `1.3.0` ([`VERSION`](VERSION)) — see [CHANGELOG.md](CHANGELOG.md).

## Monorepo contents

| Path | What it is |
|------|------------|
| [`plugins/igfae-content-viewer/`](plugins/igfae-content-viewer/) | WordPress plugin (publications, events, staff, intranet…) |
| [`themes/igfae_nvgrd_prod/`](themes/igfae_nvgrd_prod/) | WordPress theme (Timber/Twig, SCSS, JS) |

WordPress core, third-party plugins (ACF, WPML, etc.), and `uploads/` are not versioned here.

## Branches

| Branch | Use |
|--------|-----|
| `master` | Development + auto-deploy to pre (SiteGround) |

Production deploy is **manual** via Actions (`workflow_dispatch`), not on every push.

## Environment checklist

Status as of 2026-10-27. Code = plugin + theme only. Content = WP database + `uploads/`. Hosts, SSH keys, table prefixes, and IGFAE Viewer credentials stay in local env files (gitignored) or server config — not here.

| | Local | Pre | Prod |
|--|-------|-----|------|
| **Code** | Symlinks into this monorepo | Auto-deploy on push to `master` (GitHub Actions) | Manual Actions workflow exists; **not used yet** |
| **Content** | Pull script from prod | Pull script on pre (pre ← prod) | Source of truth |
| **IGFAE Viewer** | Local credentials kept across DB import | Pre credentials kept across DB import | Production credentials |

### Done

- [x] Monorepo (plugin + theme). Local site uses symlinks (Local “Duplicate site” breaks on those).
- [x] Pre deploy: `master` → GitHub Actions (`assets` built in CI, not committed).
- [x] Prod deploy workflow: [`.github/workflows/deploy-prod.yml`](.github/workflows/deploy-prod.yml) (`workflow_dispatch` only).
- [x] Prod → local: DB + uploads; IGFAE Viewer options preserved.
- [x] Prod → pre: server-to-server; IGFAE Viewer options preserved.
- [x] Pre can reach the intranet databases used by the plugin.

### Next

- [ ] First GitHub Actions deploy to prod for **1.3.0** (manual). Back up plugin/theme on the server first. `rsync --delete` can drop uncommitted prod hotfixes.
- [ ] After push: `./tools/install-pre-sync.sh` if the prod→pre script changed.
- [ ] Feature branches per ticket (not a single long-lived `dev`).
- [ ] Later: IGFAE Viewer DB from `wp-config` constants, instead of backup/restore of options.

## Local environment

This repo lives outside the docroot. In Local WP the active paths remain:

- `wp-content/plugins/igfae-content-viewer`
- `wp-content/themes/igfae_nvgrd_prod`

On the `igfaegit` Local site these are symlinks into this monorepo (source of truth).

### Theme — build

```bash
cd themes/igfae_nvgrd_prod
npm install
npm run prod    # → themes/igfae_nvgrd_prod/assets/
```

`assets/` is gitignored; CI builds them on deploy.

### Plugin

No frontend build of its own; needs PHP and the databases configured in WP Admin → IGFAE Viewer.

### Content sync (DB + uploads)

Scripts live in [`tools/`](tools/README.md). Secrets stay in gitignored `tools/*.env`. Pre deploy copies `pre-pull-from-prod.sh` to `~/igfae-sync/` (not into the web root; does not overwrite `pre-pull.env`).

```bash
cp tools/usc.env.example tools/usc.env   # fill from internal docs
./tools/local-pull-from-prod.sh --yes    # prod → local
```

On pre: `~/igfae-sync/pre-pull-from-prod.sh --yes --uploads-new`.

Third-party plugins from prod are **opt-in** (`--plugins` with DB/uploads, or `--plugins-only`). Same exclude: never `igfae-content-viewer`; theme stays on GitHub deploy. IGFAE Viewer options are preserved on import. Laptop fallback: `./tools/sync-prod-to-pre-from-local.sh` (same flags).

## Deploy

### Pre (SiteGround)

Workflow: [`.github/workflows/deploy-pre.yml`](.github/workflows/deploy-pre.yml).

1. Push to `master` → theme build + `rsync` of plugin and theme to SiteGround
2. Same workflow copies `tools/pre-pull-from-prod.sh` to `~/igfae-sync/` (env file on the server is not touched)
3. Secrets: `PRE_SSH_*`, `PRE_WP_PATH`

### Prod (USC)

Workflow: [`.github/workflows/deploy-prod.yml`](.github/workflows/deploy-prod.yml).

1. Actions → **Deploy prod (USC)** → Run workflow (manual)
2. Secrets: `PROD_SSH_*`, `PROD_WP_PATH` (set in the GitHub repo, not in git)

No WP Migrate for code.

## Releases

**Pre** tracks `master` (no version bump per push). **Prod** = numbered release: same day you deploy to USC, the repo must show that version.

1. During development, note changes under `## [Unreleased]` in [CHANGELOG.md](CHANGELOG.md) (optional).
2. When ready for prod, choose semver (`patch` / `minor`), run `./tools/bump-version.sh x.y.z` if the number changed since last release.
3. Move changelog entries into `## [x.y.z] - YYYY-MM-DD`, leave `[Unreleased]` empty, update the **Current release** line in this README.
4. Commit, `git tag vX.Y.Z`, push branch + tag, run **Deploy prod (USC)** on that commit.

Plugin `Version:` and theme `style.css` header always match [`VERSION`](VERSION).

## Changelog

See [CHANGELOG.md](CHANGELOG.md).

## License

Project code for IGFAE / Novagarda. Third-party plugins and libraries keep their own licenses.
