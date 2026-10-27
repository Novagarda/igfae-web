# Sync tools

Runbooks for copying **content** (WordPress database + `uploads/`) between environments.

These scripts are versioned here. GitHub Actions **pre** deploy also copies `pre-pull-from-prod.sh` to `~/igfae-sync/` on the pre server (outside the web root, env file left as-is). It does not rsync `tools/` into `wp-content`. Prod deploy does not install these scripts.

| Script | Where it runs | What it does |
|--------|---------------|--------------|
| `local-pull-from-prod.sh` | Laptop | Prod → local |
| `pre-pull-from-prod.sh` | Pre server | Prod → pre (server to server) |
| `install-pre-sync.sh` | Laptop | Fallback: copies `pre-pull-from-prod.sh` onto pre |
| `sync-prod-to-pre-from-local.sh` | Laptop | Prod → pre via two SSH hops (fallback) |
| `bump-version.sh` | Laptop | Set `VERSION` + plugin/theme headers (`x.y.z`) |

Never overwrite plugin/theme. Never write to prod. IGFAE Viewer DB options are backed up and restored on import.

## Secrets

```bash
cp tools/usc.env.example tools/usc.env
# fill SSH hosts, users, paths, URLs
```

On pre, `pre-pull.env` lives next to the installed script (`~/igfae-sync/`). Copy from `pre-pull.env.example`. Both `*.env` files are gitignored.

## Prod → local

Local site must be running. SSH to prod must work.

```bash
./tools/local-pull-from-prod.sh --dry-run
./tools/local-pull-from-prod.sh --yes
```

## Prod → pre

Push to `master` updates the script on pre (`~/igfae-sync/pre-pull-from-prod.sh`). Then, **on the pre server**:

```bash
cd ~/igfae-sync
./pre-pull-from-prod.sh --dry-run
./pre-pull-from-prod.sh --yes --uploads-new
```

Laptop fallback if Actions cannot write the home dir: `./tools/install-pre-sync.sh`. If pre cannot SSH to prod: `./tools/sync-prod-to-pre-from-local.sh`.
