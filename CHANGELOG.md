# Changelog

All notable changes to this project (plugin + theme) are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

The canonical version is in [`VERSION`](VERSION) at the repo root; plugin and theme WordPress headers stay in sync (see `tools/bump-version.sh`).

## [Unreleased]

### Changed

- **Pre deploy** — GitHub Actions also installs `tools/pre-pull-from-prod.sh` to `~/igfae-sync/` on pre (outside the web root; does not overwrite `pre-pull.env` or run the pull). Prod deploy unchanged.
- **Laptop prod→pre fallback** — Renamed `sync-prod-to-pre.sh` to `sync-prod-to-pre-from-local.sh`.
- **Laptop prod→local pull** — Renamed `pull-from-prod.sh` to `local-pull-from-prod.sh`.

### Fixed

- **Pre DB import usermeta** — After remapping table prefix `wp_` → live prefix, copy `wp_capabilities` / `wp_user_roles` (and related keys) so WP-CLI and admin still see roles.

## [1.3.0] - 2026-10-27

### Added

- **Monorepo `igfae-web`** — Single repository for `igfae-content-viewer` and `igfae_nvgrd_prod`, with unified README and changelog. Former repos: `Novagarda/igfae-content-viewer`, `Novagarda/igfae-theme`.
- **`tools/` content-sync scripts** — Versioned prod→local (`pull-from-prod.sh`) and prod→pre (`pre-pull-from-prod.sh`, `sync-prod-to-pre.sh`) runbooks, plus `install-pre-sync.sh` to install the pre script outside the web root (`~/igfae-sync/`). Example env files (`usc.env.example`, `pre-pull.env.example`); real `tools/*.env` and SQL dumps stay gitignored.
- **Journal in publications list and modal** *(plugin + theme)* — Show `JournalRef` below the article title in list views and at the bottom of the article detail modal. Theme: generic `.list01-meta` styles.
- **Cookie consent enforcement** *(theme)* — `cookie-consent-head.twig` sets Google Consent Mode defaults (deny until choice). `cookie-consent-enforcement.twig` listens for `gdprCookiesEnabled`, syncs WP Consent API / `gtag`, purges non-essential cookies by category, activates `script[type="text/plain"][data-cookie-consent]`, and toggles `html` classes for analytics/marketing.
- **Cookie banner: reject + locales** *(theme)* — Extended inline `gdpr-cookie-notice` (bar + modal templates) with “reject non-essential” actions; Catalan (`ca`) copy; privacy policy link from `cookie_statement_url` (ACF or WP privacy page).
- **ACF Local JSON: Opcións** *(theme)* — Version `group_663b2069c02fe` (including `cookies_check`, `privacy_page`) for sync across environments.
- **Release tooling** — Root `VERSION` file and `tools/bump-version.sh` to keep plugin/theme headers aligned.

### Changed

- **README** — Environment checklist (local / pre / prod for code vs content), content-sync how-to pointing at `tools/`, prod release checklist, and trimmed prod deploy secrets detail (values live in GitHub, not git).
- **Cookie banner UI** *(theme)* — Reworked `_cookies.scss` for IGFAE layout and corporate blue; bar/modal z-index and responsive stacking.
- **Cookie notice loading** *(theme)* — Banner runs from `partials/cookie-notice.twig` only; removed unused `src/js/modules/cookies.js` and its import from `main.js` (old stub domain and dead init).
- **Timber context** *(theme)* — `igfae_privacy_policy_url()`; register `mytheme_timber_context` on both `timber_context` and `timber/context` for Timber 1.x / 2.x.

### Fixed

- **Publications fatal on empty or invalid dates** *(plugin)* — Guard `DateTime` creation and formatting in `publications-list-item.php` and `publications-list.php` so a missing or invalid `Date` no longer fatals the publications shortcode.
- **Event detail URLs returned HTTP 404** *(plugin)* — Virtual `/{type}/{id}` routes are not WP pages; core 404 broke Swup preloads. `pre_handle_404` + `status_header(200)` for valid event routes; shared `igfae_request_is_event_route()` helper.

## Previous history

Releases before the monorepo lived in the separate plugin and theme repositories. See:

- Plugin: https://github.com/Novagarda/igfae-content-viewer/blob/master/CHANGELOG.md
- Theme: https://github.com/Novagarda/igfae-theme/blob/master/CHANGELOG.md

Last versions in those repos before merge: plugin **1.2.3**, theme **1.2.3**.
