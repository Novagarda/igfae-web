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

For now: development and SiteGround deploy from `master`.

| Branch | Use |
|--------|-----|
| `master` | Development + deploy to pre (SiteGround) |
| `pre` / `prod` | Reserved for later (USC, etc.) |

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

## Deploy (pre / SiteGround)

Workflow: [`.github/workflows/deploy-pre.yml`](.github/workflows/deploy-pre.yml).

1. Push to `master` → theme build + `rsync` of plugin and theme to SiteGround
2. GitHub secrets (`PRE_SSH_*`, `PRE_WP_PATH`); public key in Site Tools → SSH Keys
3. No WP Migrate for code

(Later: separate `pre` / `prod` branches if needed.)

## Historical documentation

This code previously lived in two repos:

- https://github.com/Novagarda/igfae-content-viewer
- https://github.com/Novagarda/igfae-theme

Old history remains there; ongoing development is in this monorepo.

## Changelog

See [CHANGELOG.md](CHANGELOG.md).

## License

Project code for IGFAE / Novagarda. Third-party plugins and libraries keep their own licenses.
