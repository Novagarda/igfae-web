# Sync tools

Runbooks for copying **content** (WordPress database + `uploads/`) between environments.

These scripts are versioned here. They are **not** deployed by GitHub Actions (that workflow only rsyncs plugin + theme into `wp-content`).

| Script | Where it runs | What it does |
|--------|---------------|--------------|
| `pull-from-prod.sh` | Laptop | Prod → local |
| `pre-pull-from-prod.sh` | Pre server | Prod → pre (server to server) |
| `install-pre-sync.sh` | Laptop | Copies `pre-pull-from-prod.sh` onto pre |
| `sync-prod-to-pre.sh` | Laptop | Prod → pre via two SSH hops (fallback) |
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
./tools/pull-from-prod.sh --dry-run
./tools/pull-from-prod.sh --yes
```

## Prod → pre

Source of the script: this folder. Install on pre (outside the web root):

```bash
./tools/install-pre-sync.sh --dry-run
./tools/install-pre-sync.sh
```

Then, **on the pre server**:

```bash
cd ~/igfae-sync
./pre-pull-from-prod.sh --dry-run
./pre-pull-from-prod.sh --yes --uploads-new
```

After changing `pre-pull-from-prod.sh` in git, run `install-pre-sync.sh` again so pre matches the repo.

Laptop fallback if pre cannot SSH to prod: `./tools/sync-prod-to-pre.sh`.
