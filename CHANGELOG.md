# Changelog

All notable changes to this project (plugin + theme) are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

Component versions in WordPress headers may differ; this file is the product changelog for **igfae-web**.

## [Unreleased]

### Added

- **Monorepo `igfae-web`** — Single repository for `igfae-content-viewer` and `igfae_nvgrd_prod`, with unified README and changelog. Former repos: `Novagarda/igfae-content-viewer`, `Novagarda/igfae-theme`.
- **Journal in publications list and modal** *(plugin + theme)* — Show `JournalRef` below the article title in list views and at the bottom of the article detail modal. Theme: generic `.list01-meta` styles.

### Fixed

- **Publications fatal on empty or invalid dates** *(plugin)* — Guard `DateTime` creation and formatting in `publications-list-item.php` and `publications-list.php` so a missing or invalid `Date` no longer fatals the publications shortcode.

## Previous history

Releases before the monorepo lived in the separate plugin and theme repositories. See:

- Plugin: https://github.com/Novagarda/igfae-content-viewer/blob/master/CHANGELOG.md
- Theme: https://github.com/Novagarda/igfae-theme/blob/master/CHANGELOG.md

Last component versions at merge into the monorepo: plugin **1.2.3**, theme **1.2.3**.
