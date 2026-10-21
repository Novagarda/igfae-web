# IGFAE Web

Código propio da web do **Instituto Galego de Física de Altas Enerxías (IGFAE)**: plugin de contido e tema WordPress.

Desenvolvido por [Novagarda](https://novagarda.gal).

## Contido do monorepo

| Ruta | Que é |
|------|--------|
| [`plugins/igfae-content-viewer/`](plugins/igfae-content-viewer/) | Plugin WordPress (publicacións, eventos, persoal, intranet…) |
| [`themes/igfae_nvgrd_prod/`](themes/igfae_nvgrd_prod/) | Tema WordPress (Timber/Twig, SCSS, JS) |

Non se versiona o núcleo de WordPress, plugins de terceiros (ACF, WPML, etc.), nin `uploads/`.

## Ramas

Por agora: traballo e deploy a SiteGround desde `master`.

| Rama | Uso |
|------|-----|
| `master` | Desenvolvemento + deploy a pre (SiteGround) |
| `pre` / `prod` | Reservadas para máis adiante (USC, etc.) |

## Contorno local

Este repo vive fóra do docroot. En Local WP as carpetas activas seguen sendo:

- `wp-content/plugins/igfae-content-viewer`
- `wp-content/themes/igfae_nvgrd_prod`

Sincroniza cambios entre o monorepo e Local cando desenvolvas (copiar/rsync ou abrir o monorepo como fonte de verdade e copiar ao sitio).

### Tema — build

```bash
cd themes/igfae_nvgrd_prod
npm install
npm run prod    # → themes/igfae_nvgrd_prod/assets/
```

### Plugin

Sen build frontend propio; require PHP e conexión ás BBDD configuradas en WP Admin → IGFAE Viewer.

## Despregue (pre / SiteGround)

Workflow: [`.github/workflows/deploy-pre.yml`](.github/workflows/deploy-pre.yml).

1. Push a `master` → build do tema + `rsync` de plugin e tema a SiteGround
2. Secrets en GitHub (`PRE_SSH_*`, `PRE_WP_PATH`); clave pública en Site Tools → SSH Keys
3. Sen WP Migrate para código

(Máis adiante: ramas `pre` / `prod` se fai falta.)

## Documentación histórica

Antes este código estaba en dous repos:

- https://github.com/Novagarda/igfae-content-viewer
- https://github.com/Novagarda/igfae-theme

O historial antigo permanece alí; a partir de agora o desenvolvemento é neste monorepo.

## Changelog

Véxase [CHANGELOG.md](CHANGELOG.md).

## Licenza

Código do proxecto IGFAE / Novagarda. Plugins e librerías de terceiros manteñen as súas propias licenzas.
