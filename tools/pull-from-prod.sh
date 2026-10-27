#!/usr/bin/env bash
# Pull WordPress database + uploads from USC production into Local WP.
# Never overwrites monorepo plugin/theme (symlinks). Never pushes to prod.
#
# Usage (from anywhere):
#   ./tools/pull-from-prod.sh
#   ./tools/pull-from-prod.sh --yes
#   ./tools/pull-from-prod.sh --db-only
#   ./tools/pull-from-prod.sh --uploads-new
#   ./tools/pull-from-prod.sh --plugins-only --yes   # third-party plugins only (opt-in)
#   ./tools/pull-from-prod.sh --yes --plugins        # DB + uploads + plugins
#   ./tools/pull-from-prod.sh --dry-run

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
      sed -n '2,14p' "$0"
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

sync_plugins_from_prod() {
  local dry_note=""
  [[ "$DRY_RUN" -eq 1 ]] && dry_note=" (dry-run)"
  echo "→ rsync plugins${dry_note} (excludes igfae-content-viewer)…"
  mkdir -p "${LOCAL_WP_PATH}/wp-content/plugins"
  local -a rsync_plugins=(rsync -az --partial --timeout=120 --human-readable --stats)
  [[ "$DRY_RUN" -eq 1 ]] && rsync_plugins+=(-n)
  rsync_plugins+=("${PLUGIN_RSYNC_EXCLUDES[@]}")
  "${rsync_plugins[@]}" -e "$RSYNC_RSH" \
    "${REMOTE}:${PROD_WP_PATH}/wp-content/plugins/" \
    "${LOCAL_WP_PATH}/wp-content/plugins/"
}

: "${PROD_SSH_HOST:?}"
: "${PROD_SSH_PORT:?}"
: "${PROD_SSH_USER:?}"
: "${PROD_SSH_IDENTITY:?}"
: "${PROD_WP_PATH:?}"
: "${PROD_URL:?}"
: "${LOCAL_URL:?}"
: "${LOCAL_WP_PATH:?}"
: "${LOCAL_SITE_ID:?}"

LOCAL_RUN_DIR="${HOME}/Library/Application Support/Local/run/${LOCAL_SITE_ID}"
LOCAL_MYSQL_SOCKET="${LOCAL_RUN_DIR}/mysql/mysqld.sock"

if [[ -z "${LOCAL_WP_CLI:-}" ]]; then
  LOCAL_WP_CLI="/Applications/Local.app/Contents/Resources/extraResources/bin/wp-cli/posix/wp"
fi
if [[ -z "${LOCAL_PHP:-}" ]]; then
  LOCAL_PHP="$(ls -d "${HOME}/Library/Application Support/Local/lightning-services"/php-8.*/bin/darwin-arm64/bin/php 2>/dev/null | sort -V | tail -1 || true)"
fi
if [[ -z "${LOCAL_MYSQL:-}" ]]; then
  LOCAL_MYSQL="$(ls -d "${HOME}/Library/Application Support/Local/lightning-services"/mysql-*/bin/darwin-arm64/bin/mysql 2>/dev/null | sort -V | tail -1 || true)"
fi

SSH=(ssh -i "${PROD_SSH_IDENTITY}" -p "${PROD_SSH_PORT}" -o IdentitiesOnly=yes -o StrictHostKeyChecking=accept-new -o ServerAliveInterval=15 -o ServerAliveCountMax=4 -o ConnectTimeout=20)
RSYNC_RSH="ssh -i ${PROD_SSH_IDENTITY} -p ${PROD_SSH_PORT} -o IdentitiesOnly=yes -o StrictHostKeyChecking=accept-new -o ServerAliveInterval=15 -o ServerAliveCountMax=4"
REMOTE="${PROD_SSH_USER}@${PROD_SSH_HOST}"

wp_local() {
  if [[ ! -x "${LOCAL_PHP}" ]]; then
    echo "Local PHP not found. Start the site in Local or set LOCAL_PHP in usc.env" >&2
    exit 1
  fi
  if [[ ! -x "${LOCAL_WP_CLI}" ]]; then
    echo "WP-CLI not found at ${LOCAL_WP_CLI}" >&2
    exit 1
  fi
  if [[ ! -S "${LOCAL_MYSQL_SOCKET}" ]]; then
    echo "MySQL socket missing (${LOCAL_MYSQL_SOCKET}). Start site igfaegit in Local first." >&2
    exit 1
  fi
  "${LOCAL_PHP}" \
    -d "mysqli.default_socket=${LOCAL_MYSQL_SOCKET}" \
    -d "pdo_mysql.default_socket=${LOCAL_MYSQL_SOCKET}" \
    -d "memory_limit=512M" \
    "${LOCAL_WP_CLI}" --path="${LOCAL_WP_PATH}" --skip-plugins --skip-themes "$@"
}

# Loads theme + plugins so CPT/plugin rewrite rules (actividad, carreira, seminar…) are registered.
wp_local_full() {
  if [[ ! -x "${LOCAL_PHP}" ]]; then
    echo "Local PHP not found. Start the site in Local or set LOCAL_PHP in usc.env" >&2
    exit 1
  fi
  if [[ ! -x "${LOCAL_WP_CLI}" ]]; then
    echo "WP-CLI not found at ${LOCAL_WP_CLI}" >&2
    exit 1
  fi
  if [[ ! -S "${LOCAL_MYSQL_SOCKET}" ]]; then
    echo "MySQL socket missing (${LOCAL_MYSQL_SOCKET}). Start site igfaegit in Local first." >&2
    exit 1
  fi
  "${LOCAL_PHP}" \
    -d "mysqli.default_socket=${LOCAL_MYSQL_SOCKET}" \
    -d "pdo_mysql.default_socket=${LOCAL_MYSQL_SOCKET}" \
    -d "memory_limit=512M" \
    "${LOCAL_WP_CLI}" --path="${LOCAL_WP_PATH}" "$@"
}

# IGFAE Viewer external DB settings (local ≠ prod). Preserve across DB import.
# Temporary file only; deleted on exit. Later: prefer wp-config constants (option 3).
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

backup_igfae_db_options() {
  OPTIONS_BACKUP="$(mktemp -t igfae-db-options)"
  chmod 600 "$OPTIONS_BACKUP"
  local keys_php
  keys_php="$(printf "'%s'," "${IGFAE_DB_OPTION_KEYS[@]}")"
  keys_php="${keys_php%,}"
  wp_local eval "
    \$keys = array(${keys_php});
    \$out = array();
    foreach (\$keys as \$k) {
      \$out[\$k] = get_option(\$k, null);
    }
    echo wp_json_encode(\$out);
  " > "$OPTIONS_BACKUP"
  if [[ ! -s "$OPTIONS_BACKUP" ]]; then
    echo "Failed to backup IGFAE DB options." >&2
    exit 1
  fi
  echo "   saved ${#IGFAE_DB_OPTION_KEYS[@]} option keys → temp file (mode 600)"
}

restore_igfae_db_options() {
  if [[ -z "${OPTIONS_BACKUP}" || ! -f "$OPTIONS_BACKUP" ]]; then
    echo "No IGFAE options backup to restore." >&2
    return 1
  fi
  wp_local eval "
    \$path = '${OPTIONS_BACKUP}';
    \$data = json_decode(file_get_contents(\$path), true);
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
    echo 'restored ' . count(\$data) . ' IGFAE DB options';
  "
  rm -f "$OPTIONS_BACKUP"
  OPTIONS_BACKUP=""
}

echo "Source:  USC prod  (${PROD_URL})"
echo "Dest:    ${LOCAL_URL}"
echo "WP path: ${LOCAL_WP_PATH}"
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
echo "Leaves:  plugins/igfae-content-viewer + themes/igfae_nvgrd_prod (monorepo / GitHub)"
[[ "$DRY_RUN" -eq 1 ]] && echo "Mode:    DRY RUN (no writes)"
echo

if [[ "$YES" -ne 1 && "$DRY_RUN" -ne 1 ]]; then
  if [[ "$PLUGINS_ONLY" -eq 1 ]]; then
    read -r -p "Sync plugins from prod into Local (never igfae-content-viewer)? [y/N] " answer
  else
    read -r -p "This REPLACES the local database. Continue? [y/N] " answer
  fi
  if [[ ! "$answer" =~ ^[yY]$ ]]; then
    echo "Aborted."
    exit 0
  fi
fi

echo "→ Checking SSH to prod…"
"${SSH[@]}" "$REMOTE" "test -f '${PROD_WP_PATH}/wp-config.php' && echo PROD_OK"

if [[ "$DRY_RUN" -eq 1 ]]; then
  if [[ "$PLUGINS" -eq 1 ]]; then
    echo "→ Dry-run: remote plugins sample…"
    "${SSH[@]}" "$REMOTE" "ls '${PROD_WP_PATH}/wp-content/plugins' | head"
    sync_plugins_from_prod
  fi
  if [[ "$PLUGINS_ONLY" -eq 0 ]]; then
    echo "→ Dry-run: remote uploads sample…"
    "${SSH[@]}" "$REMOTE" "ls '${PROD_WP_PATH}/wp-content/uploads' | head"
    if [[ "$DB_ONLY" -eq 0 ]]; then
      echo "→ Dry-run rsync uploads…"
      RSYNC_UPLOADS=(rsync -azn --partial --timeout=120 --human-readable --stats)
      [[ "$UPLOADS_NEW" -eq 1 ]] && RSYNC_UPLOADS+=(--ignore-existing)
      "${RSYNC_UPLOADS[@]}" -e "$RSYNC_RSH" \
        "${REMOTE}:${PROD_WP_PATH}/wp-content/uploads/" \
        "${LOCAL_WP_PATH}/wp-content/uploads/"
    fi
  fi
  echo "Dry-run finished."
  exit 0
fi

if [[ "$PLUGINS_ONLY" -eq 1 ]]; then
  sync_plugins_from_prod
  echo
  echo "Done. Plugins synced (igfae-content-viewer unchanged)."
  exit 0
fi

echo "→ Backing up local IGFAE Viewer DB settings…"
backup_igfae_db_options

DUMP_LOCAL="$(mktemp -t igfae-pull-prod)"
DUMP_LOCAL="${DUMP_LOCAL}.sql.gz"

echo "→ Exporting remote database…"
# Prefer WP-CLI on the server; fall back to mysqldump via wp-config credentials.
if "${SSH[@]}" "$REMOTE" "command -v wp >/dev/null 2>&1"; then
  "${SSH[@]}" "$REMOTE" "cd '${PROD_WP_PATH}' && wp db export - --allow-root 2>/dev/null | gzip" > "$DUMP_LOCAL"
else
  echo "   wp-cli not on server; using mysqldump…"
  "${SSH[@]}" "$REMOTE" "PROD_WP_PATH='${PROD_WP_PATH}' bash -s" <<'EOS' > "$DUMP_LOCAL"
set -euo pipefail
cd "$PROD_WP_PATH"
# Read defines without bootstrapping WordPress fully.
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
  echo "Remote dump is empty — aborting." >&2
  exit 1
fi

echo "→ Importing into Local…"
if [[ ! -S "${LOCAL_MYSQL_SOCKET}" ]]; then
  echo "MySQL socket missing. Start igfaegit in Local first." >&2
  exit 1
fi
if [[ -x "${LOCAL_MYSQL}" ]]; then
  gzip -dc "$DUMP_LOCAL" | "${LOCAL_MYSQL}" --socket="${LOCAL_MYSQL_SOCKET}" -u root -proot local
else
  gzip -dc "$DUMP_LOCAL" | wp_local db import -
fi
rm -f "$DUMP_LOCAL"
DUMP_LOCAL=""

echo "→ Restoring local IGFAE Viewer DB settings…"
restore_igfae_db_options

echo "→ search-replace URLs…"
# Normalize (no trailing slash) for search-replace of full origin+path.
PROD_ORIGIN="${PROD_URL%/}"
LOCAL_ORIGIN="${LOCAL_URL%/}"

wp_local search-replace "${PROD_ORIGIN}" "${LOCAL_ORIGIN}" --all-tables --report-changed-only || true
# http twin of the same path
if [[ "$PROD_ORIGIN" == https://* ]]; then
  wp_local search-replace "http://${PROD_ORIGIN#https://}" "${LOCAL_ORIGIN}" --all-tables --report-changed-only || true
fi

if [[ "$DB_ONLY" -eq 0 ]]; then
  echo "→ rsync uploads$([[ "$UPLOADS_NEW" -eq 1 ]] && echo " (skip existing)")…"
  mkdir -p "${LOCAL_WP_PATH}/wp-content/uploads"
  RSYNC_UPLOADS=(rsync -az --partial --timeout=120 --human-readable --stats)
  [[ "$UPLOADS_NEW" -eq 1 ]] && RSYNC_UPLOADS+=(--ignore-existing)
  "${RSYNC_UPLOADS[@]}" -e "$RSYNC_RSH" \
    "${REMOTE}:${PROD_WP_PATH}/wp-content/uploads/" \
    "${LOCAL_WP_PATH}/wp-content/uploads/"
fi

if [[ "$PLUGINS" -eq 1 ]]; then
  sync_plugins_from_prod
fi

wp_local_full rewrite flush || true
wp_local cache flush || true

echo
echo "Done. Local site: ${LOCAL_URL}"
if [[ "$PLUGINS" -eq 1 ]]; then
  echo "Plugins synced from prod (igfae-content-viewer excluded)."
else
  echo "Plugins not synced (use --plugins or --plugins-only when needed)."
fi
echo "Monorepo symlinks not overwritten. IGFAE DB options restored. WP Migrate was not used."
