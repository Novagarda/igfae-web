# IGFAE Web

Custom code for the **Instituto Galego de Física de Altas Enerxías (IGFAE)** website: WordPress content plugin and theme.

Developed by [Novagarda](https://novagarda.gal).

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

## Deploy

### Pre (SiteGround)

Workflow: [`.github/workflows/deploy-pre.yml`](.github/workflows/deploy-pre.yml).

1. Push to `master` → theme build + `rsync` of plugin and theme to SiteGround
2. Secrets: `PRE_SSH_*`, `PRE_WP_PATH`

### Prod (USC)

Workflow: [`.github/workflows/deploy-prod.yml`](.github/workflows/deploy-prod.yml).

1. Actions → **Deploy prod (USC)** → Run workflow (manual)
2. Secrets: `PROD_SSH_*`, `PROD_WP_PATH` (`/home/igfae/public_html`)
3. Public key on `novagarda@igfae.usc.es`; GitHub Actions IP ranges allowed on firewall

No WP Migrate for code.

## Historical documentation

This code previously lived in two repos:

- https://github.com/Novagarda/igfae-content-viewer
- https://github.com/Novagarda/igfae-theme

Old history remains there; ongoing development is in this monorepo.

## Changelog

See [CHANGELOG.md](CHANGELOG.md).

## License

Project code for IGFAE / Novagarda. Third-party plugins and libraries keep their own licenses.
