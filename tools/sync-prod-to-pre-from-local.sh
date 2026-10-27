#!/usr/bin/env bash
# Copy WordPress database + uploads from USC production to SiteGround pre.
# Never overwrites plugin/theme (those deploy via GitHub Actions). Never writes to prod.
#
# Usage (runs on the laptop; two SSH hops):
#   ./tools/sync-prod-to-pre-from-local.sh --dry-run
#   ./tools/sync-prod-to-pre-from-local.sh --yes --uploads-new
#   ./tools/sync-prod-to-pre-from-local.sh --plugins-only --yes
#   ./tools/sync-prod-to-pre-from-local.sh --yes --plugins

set -euo pipefail

ROOT_DIR="$(cd "$(dirname "$0")/.." && pwd)"
ENV_FILE="${ROOT_DIR}/tools/usc.env"

if [[ ! -f "$ENV_FILE" ]]; then
  echo "Missing ${ENV_FILE}. Copy tools/usc.env.example to tools/usc.env" >&2
  exit 1
fi

# shellcheck disable=SC1090
source "$ENV_FILE"

YES=0
DB_ONLY=0
UPLOADS_NEW=0
PLUGINS=0
PLUGINS_ONLY=0
DRY_RUN=0

# Monorepo / GitHub — never rsync from prod.
PLUGIN_RSYNC_EXCLUDES=(
  --exclude 'igfae-content-viewer/'
)

while [[ $# -gt 0 ]]; do
  case "$1" in
    --yes|-y) YES=1 ;;
    --db-only) DB_ONLY=1 ;;
    --uploads-new) UPLOADS_NEW=1 ;;
    --plugins) PLUGINS=1 ;;
    --plugins-only) PLUGINS=1; PLUGINS_ONLY=1; DB_ONLY=1 ;;
    --dry-run) DRY_RUN=1 ;;
    -h|--help)
      sed -n '2,10p' "$0"
      exit 0
      ;;
    *) echo "Unknown flag: $1" >&2; exit 1 ;;
  esac
  shift
done

if [[ "$PLUGINS_ONLY" -eq 1 && "$PLUGINS" -ne 1 ]]; then
  echo "Internal error: --plugins-only requires plugins sync." >&2
  exit 1
fi

sync_plugins_prod_to_pre() {
  local dry_note=""
  [[ "$DRY_RUN" -eq 1 ]] && dry_note=" (dry-run)"
  echo "→ rsync plugins prod → pre${dry_note} (excludes igfae-content-viewer)…"
  local cache
  cache="$(mktemp -d -t igfae-plugins-cache)"
  local -a rsync_plugins=(rsync -az --partial --timeout=120 --human-readable --stats)
  [[ "$DRY_RUN" -eq 1 ]] && rsync_plugins+=(-n)
  rsync_plugins+=("${PLUGIN_RSYNC_EXCLUDES[@]}")
  "${rsync_plugins[@]}" -e "$RSYNC_PROD" \
    "${PROD_REMOTE}:${PROD_WP_PATH}/wp-content/plugins/" \
    "${cache}/"
  "${SSH_PRE[@]}" "$PRE_REMOTE" "mkdir -p '${PRE_WP_PATH}/wp-content/plugins'"
  "${rsync_plugins[@]}" -e "$RSYNC_PRE" \
    "${cache}/" \
    "${PRE_REMOTE}:${PRE_WP_PATH}/wp-content/plugins/"
  if [[ "$DRY_RUN" -eq 0 ]]; then
    rm -rf "$cache"
  fi
}

: "${PROD_SSH_HOST:?}"
: "${PROD_SSH_PORT:?}"
: "${PROD_SSH_USER:?}"
: "${PROD_SSH_IDENTITY:?}"
: "${PROD_WP_PATH:?}"
: "${PROD_URL:?}"
: "${PRE_SSH_HOST:?}"
: "${PRE_SSH_PORT:?}"
: "${PRE_SSH_USER:?}"
: "${PRE_SSH_IDENTITY:?}"
: "${PRE_WP_PATH:?}"
: "${PRE_URL:?}"

SSH_OPTS=(-o IdentitiesOnly=yes -o StrictHostKeyChecking=accept-new -o ServerAliveInterval=15 -o ServerAliveCountMax=4 -o ConnectTimeout=20)
SSH_PROD=(ssh -i "${PROD_SSH_IDENTITY}" -p "${PROD_SSH_PORT}" "${SSH_OPTS[@]}")
SSH_PRE=(ssh -i "${PRE_SSH_IDENTITY}" -p "${PRE_SSH_PORT}" "${SSH_OPTS[@]}")
RSYNC_PROD="ssh -i ${PROD_SSH_IDENTITY} -p ${PROD_SSH_PORT} -o IdentitiesOnly=yes -o StrictHostKeyChecking=accept-new -o ServerAliveInterval=15 -o ServerAliveCountMax=4"
RSYNC_PRE="ssh -i ${PRE_SSH_IDENTITY} -p ${PRE_SSH_PORT} -o IdentitiesOnly=yes -o StrictHostKeyChecking=accept-new -o ServerAliveInterval=15 -o ServerAliveCountMax=4"
PROD_REMOTE="${PROD_SSH_USER}@${PROD_SSH_HOST}"
PRE_REMOTE="${PRE_SSH_USER}@${PRE_SSH_HOST}"

IGFAE_DB_OPTION_KEYS=(
  igfae_db_name
  igfae_db_user
  igfae_db_pass
  igfae_db_host
  igfae_db_intranet_name
  igfae_db_intranet_user
  igfae_db_intranet_pass
  igfae_db_intranet_host
)

OPTIONS_BACKUP=""
DUMP_LOCAL=""

cleanup() {
  rm -f "${OPTIONS_BACKUP:-}" "${DUMP_LOCAL:-}"
}
trap cleanup EXIT

echo "Source:  USC prod  (${PROD_URL})"
echo "Dest:    SiteGround pre  (${PRE_URL})"
echo "WP path: ${PRE_WP_PATH}"
if [[ "$PLUGINS_ONLY" -eq 1 ]]; then
  echo "Pulls:   plugins only (third-party from prod)"
elif [[ "$DB_ONLY" -eq 1 ]]; then
  echo "Pulls:   database only"
else
  uploads_note="uploads"
  [[ "$UPLOADS_NEW" -eq 1 ]] && uploads_note="uploads (new files only)"
  pulls="database + ${uploads_note}"
  [[ "$PLUGINS" -eq 1 ]] && pulls="${pulls} + plugins"
  echo "Pulls:   ${pulls}"
fi
echo "Leaves:  plugins/igfae-content-viewer + themes/igfae_nvgrd_prod (GitHub deploy)"
[[ "$DRY_RUN" -eq 1 ]] && echo "Mode:    DRY RUN (no writes)"
echo

if [[ "$YES" -ne 1 && "$DRY_RUN" -ne 1 ]]; then
  if [[ "$PLUGINS_ONLY" -eq 1 ]]; then
    read -r -p "Sync plugins from prod into pre (never igfae-content-viewer)? [y/N] " answer
  else
    read -r -p "This REPLACES the pre database. Continue? [y/N] " answer
  fi
  if [[ ! "$answer" =~ ^[yY]$ ]]; then
    echo "Aborted."
    exit 0
  fi
fi

echo "→ Checking SSH to prod…"
"${SSH_PROD[@]}" "$PROD_REMOTE" "test -f '${PROD_WP_PATH}/wp-config.php' && echo PROD_OK"

echo "→ Checking SSH to pre…"
"${SSH_PRE[@]}" "$PRE_REMOTE" "test -f '${PRE_WP_PATH}/wp-config.php' && echo PRE_OK"

if [[ "$DRY_RUN" -eq 1 ]]; then
  if [[ "$PLUGINS" -eq 1 ]]; then
    echo "→ Dry-run: remote plugins sample…"
    "${SSH_PROD[@]}" "$PROD_REMOTE" "ls '${PROD_WP_PATH}/wp-content/plugins' | head"
    sync_plugins_prod_to_pre
  fi
  if [[ "$PLUGINS_ONLY" -eq 0 ]]; then
  echo "→ Dry-run: pre uploads dir…"
  "${SSH_PRE[@]}" "$PRE_REMOTE" "ls '${PRE_WP_PATH}/wp-content/uploads' | head || echo '(no uploads yet)'"
  if [[ "$DB_ONLY" -eq 0 ]]; then
    echo "→ Dry-run rsync uploads prod → pre (via this machine)…"
    RSYNC_UPLOADS=(rsync -azn --partial --timeout=120 --human-readable --stats)
    [[ "$UPLOADS_NEW" -eq 1 ]] && RSYNC_UPLOADS+=(--ignore-existing)
    CACHE="${LOCAL_WP_PATH:-}/wp-content/uploads"
    if [[ -n "${LOCAL_WP_PATH:-}" && -d "$CACHE" ]]; then
      echo "   cache: local uploads (${CACHE})"
      "${RSYNC_UPLOADS[@]}" -e "$RSYNC_PROD" \
        "${PROD_REMOTE}:${PROD_WP_PATH}/wp-content/uploads/" \
        "${CACHE}/"
      "${RSYNC_UPLOADS[@]}" -e "$RSYNC_PRE" \
        "${CACHE}/" \
        "${PRE_REMOTE}:${PRE_WP_PATH}/wp-content/uploads/"
    else
      echo "   (no local uploads cache; dry-run lists prod only)"
      "${RSYNC_UPLOADS[@]}" -e "$RSYNC_PROD" \
        "${PROD_REMOTE}:${PROD_WP_PATH}/wp-content/uploads/" \
        /tmp/igfae-prod-to-pre-dryrun/
    fi
  fi
  fi
  echo "Dry-run finished (no DB import)."
  exit 0
fi

if [[ "$PLUGINS_ONLY" -eq 1 ]]; then
  sync_plugins_prod_to_pre
  echo
  echo "Done. Plugins synced on pre (igfae-content-viewer unchanged)."
  exit 0
fi

echo "→ Backing up pre IGFAE Viewer DB settings…"
OPTIONS_BACKUP="$(mktemp -t igfae-pre-db-options)"
chmod 600 "$OPTIONS_BACKUP"
keys_php="$(printf "'%s'," "${IGFAE_DB_OPTION_KEYS[@]}")"
keys_php="${keys_php%,}"
"${SSH_PRE[@]}" "$PRE_REMOTE" "cd '${PRE_WP_PATH}' && php -d memory_limit=256M -r '
require \"wp-load.php\";
\$keys = array(${keys_php});
\$out = array();
foreach (\$keys as \$k) {
  \$out[\$k] = get_option(\$k, null);
}
echo wp_json_encode(\$out);
'" > "$OPTIONS_BACKUP"
if [[ ! -s "$OPTIONS_BACKUP" ]]; then
  echo "Failed to backup IGFAE DB options on pre." >&2
  exit 1
fi
echo "   saved ${#IGFAE_DB_OPTION_KEYS[@]} option keys → temp file (mode 600)"

DUMP_LOCAL="$(mktemp -t igfae-sync-pre)"
DUMP_LOCAL="${DUMP_LOCAL}.sql.gz"

echo "→ Exporting prod database…"
if "${SSH_PROD[@]}" "$PROD_REMOTE" "command -v wp >/dev/null 2>&1"; then
  "${SSH_PROD[@]}" "$PROD_REMOTE" "cd '${PROD_WP_PATH}' && wp db export - --allow-root 2>/dev/null | gzip" > "$DUMP_LOCAL"
else
  echo "   wp-cli not on prod; using mysqldump…"
  "${SSH_PROD[@]}" "$PROD_REMOTE" "PROD_WP_PATH='${PROD_WP_PATH}' bash -s" <<'EOS' > "$DUMP_LOCAL"
set -euo pipefail
cd "$PROD_WP_PATH"
eval "$(php -r '
  $c = file_get_contents("wp-config.php");
  foreach (["DB_NAME","DB_USER","DB_PASSWORD","DB_HOST"] as $k) {
    if (preg_match("/define\(\s*'\''".$k."'\''\s*,\s*'\''([^'\'']*)'\''/", $c, $m)) {
      echo $k."=".escapeshellarg($m[1]).";\n";
    }
  }
')"
HOST="${DB_HOST%:*}"
PORT_OPT=()
if [[ "$DB_HOST" == *:* ]]; then
  PORT_OPT=(-P "${DB_HOST##*:}")
fi
mysqldump -h "$HOST" "${PORT_OPT[@]}" -u "$DB_USER" -p"$DB_PASSWORD" "$DB_NAME" | gzip
EOS
fi

if [[ ! -s "$DUMP_LOCAL" ]]; then
  echo "Prod dump is empty — aborting." >&2
  exit 1
fi
echo "   dump $(du -h "$DUMP_LOCAL" | awk '{print $1}')"

echo "→ Importing into pre…"
if "${SSH_PRE[@]}" "$PRE_REMOTE" "command -v wp >/dev/null 2>&1"; then
  gzip -dc "$DUMP_LOCAL" | "${SSH_PRE[@]}" "$PRE_REMOTE" "cd '${PRE_WP_PATH}' && wp db import - --allow-root"
else
  echo "   wp-cli not on pre; using mysql from wp-config…"
  gzip -dc "$DUMP_LOCAL" | "${SSH_PRE[@]}" "$PRE_REMOTE" "PRE_WP_PATH='${PRE_WP_PATH}' bash -s" <<'EOS'
set -euo pipefail
cd "$PRE_WP_PATH"
eval "$(php -r '
  $c = file_get_contents("wp-config.php");
  foreach (["DB_NAME","DB_USER","DB_PASSWORD","DB_HOST"] as $k) {
    if (preg_match("/define\(\s*'\''".$k."'\''\s*,\s*'\''([^'\'']*)'\''/", $c, $m)) {
      echo $k."=".escapeshellarg($m[1]).";\n";
    }
  }
')"
HOST="${DB_HOST%:*}"
PORT_OPT=()
if [[ "$DB_HOST" == *:* ]]; then
  PORT_OPT=(-P "${DB_HOST##*:}")
fi
mysql -h "$HOST" "${PORT_OPT[@]}" -u "$DB_USER" -p"$DB_PASSWORD" "$DB_NAME"
EOS
fi
rm -f "$DUMP_LOCAL"
DUMP_LOCAL=""

echo "→ Restoring pre IGFAE Viewer DB settings…"
"${SSH_PRE[@]}" "$PRE_REMOTE" "cd '${PRE_WP_PATH}' && php -d memory_limit=256M -r '
require \"wp-load.php\";
\$data = json_decode(stream_get_contents(STDIN), true);
if (!is_array(\$data)) {
  fwrite(STDERR, \"Invalid IGFAE options backup JSON\\n\");
  exit(1);
}
foreach (\$data as \$k => \$v) {
  if (\$v === null) {
    delete_option(\$k);
  } else {
    update_option(\$k, \$v);
  }
}
echo \"restored \" . count(\$data) . \" IGFAE DB options\\n\";
'" < "$OPTIONS_BACKUP"
rm -f "$OPTIONS_BACKUP"
OPTIONS_BACKUP=""

echo "→ search-replace URLs on pre…"
PROD_ORIGIN="${PROD_URL%/}"
PRE_ORIGIN="${PRE_URL%/}"
if "${SSH_PRE[@]}" "$PRE_REMOTE" "command -v wp >/dev/null 2>&1"; then
  "${SSH_PRE[@]}" "$PRE_REMOTE" "cd '${PRE_WP_PATH}' && wp search-replace '${PROD_ORIGIN}' '${PRE_ORIGIN}' --all-tables --report-changed-only --allow-root" || true
  if [[ "$PROD_ORIGIN" == https://* ]]; then
    "${SSH_PRE[@]}" "$PRE_REMOTE" "cd '${PRE_WP_PATH}' && wp search-replace 'http://${PROD_ORIGIN#https://}' '${PRE_ORIGIN}' --all-tables --report-changed-only --allow-root" || true
  fi
  echo "→ Discourage search engine indexing on pre (blog_public=0)…"
  "${SSH_PRE[@]}" "$PRE_REMOTE" bash -s -- "${PRE_WP_PATH}" <<'EOS'
set -euo pipefail
cd "$1"
PREFIX="$(wp db prefix --allow-root | tr -d '[:space:]')"
wp db query "UPDATE \`${PREFIX}options\` SET option_value = '0' WHERE option_name = 'blog_public'" --allow-root
EOS
  "${SSH_PRE[@]}" "$PRE_REMOTE" "cd '${PRE_WP_PATH}' && wp rewrite flush --allow-root && wp cache flush --allow-root" || true
else
  echo "   wp-cli missing on pre — search-replace skipped. Run it from WP-CLI or WP Migrate will not be used; install wp or tell me." >&2
  exit 1
fi

if [[ "$DB_ONLY" -eq 0 ]]; then
  echo "→ rsync uploads prod → pre$([[ "$UPLOADS_NEW" -eq 1 ]] && echo " (skip existing)")…"
  CACHE="${LOCAL_WP_PATH:-}/wp-content/uploads"
  if [[ -z "${LOCAL_WP_PATH:-}" || ! -d "$CACHE" ]]; then
    CACHE="$(mktemp -d -t igfae-uploads-cache)"
    echo "   cache: ${CACHE}"
  else
    echo "   cache: local uploads"
  fi
  mkdir -p "$CACHE"
  RSYNC_UPLOADS=(rsync -az --partial --timeout=120 --human-readable --stats)
  [[ "$UPLOADS_NEW" -eq 1 ]] && RSYNC_UPLOADS+=(--ignore-existing)
  "${RSYNC_UPLOADS[@]}" -e "$RSYNC_PROD" \
    "${PROD_REMOTE}:${PROD_WP_PATH}/wp-content/uploads/" \
    "${CACHE}/"
  "${SSH_PRE[@]}" "$PRE_REMOTE" "mkdir -p '${PRE_WP_PATH}/wp-content/uploads'"
  "${RSYNC_UPLOADS[@]}" -e "$RSYNC_PRE" \
    "${CACHE}/" \
    "${PRE_REMOTE}:${PRE_WP_PATH}/wp-content/uploads/"
fi

if [[ "$PLUGINS" -eq 1 ]]; then
  sync_plugins_prod_to_pre
fi

echo
echo "Done. Pre site: ${PRE_URL}"
if [[ "$PLUGINS" -eq 1 ]]; then
  echo "Plugins synced from prod (igfae-content-viewer excluded)."
else
  echo "Plugins not synced (use --plugins or --plugins-only when needed)."
fi
echo "Theme not overwritten (GitHub deploy). IGFAE DB options restored. WP Migrate was not used."
