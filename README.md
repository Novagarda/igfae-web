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

| Rama | Uso |
|------|-----|
| `master` | Desenvolvemento / integración |
| `pre` | O que se desprega en preprodución (SiteGround) |
| `prod` | O que se desprega en producción (USC), tras validar en pre |

Fluxo habitual: traballo en `feature/*` ou `master` → merge a `pre` → deploy a SiteGround → validar → merge a `prod` → deploy a USC.

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

Pendente de configurar SSH + GitHub Action / script. Obxectivo:

1. Push á rama `pre`
2. Despregue automático (ou semi) só de `plugins/igfae-content-viewer` e `themes/igfae_nvgrd_prod` ao staging en SiteGround
3. Sen WP Migrate para código

## Documentación histórica

Antes este código estaba en dous repos:

- https://github.com/Novagarda/igfae-content-viewer
- https://github.com/Novagarda/igfae-theme

O historial antigo permanece alí; a partir de agora o desenvolvemento é neste monorepo.

## Changelog

Véxase [CHANGELOG.md](CHANGELOG.md).

## Licenza

Código do proxecto IGFAE / Novagarda. Plugins e librerías de terceiros manteñen as súas propias licenzas.
