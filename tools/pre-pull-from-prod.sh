#!/usr/bin/env bash
# Run ON SiteGround pre. Pulls WordPress DB + uploads from USC prod.
# Never overwrites plugin/theme. Never writes to prod.
# Preserves IGFAE Viewer DB options (igfae_db_* / igfae_db_intranet_*).
# Sets blog_public=0 after import (see mu-plugins/igfae-pre-discourage-indexing.php on pre).
#
# Install: copy this file + pre-pull.env to ~/igfae-sync/ on pre.
# Usage:
#   ./pre-pull-from-prod.sh --dry-run
#   ./pre-pull-from-prod.sh --yes --uploads-new
#   ./pre-pull-from-prod.sh --plugins-only --yes   # third-party plugins only (opt-in)
#   ./pre-pull-from-prod.sh --yes --plugins        # DB + uploads + plugins

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
ENV_FILE="${SCRIPT_DIR}/pre-pull.env"

if [[ ! -f "$ENV_FILE" ]]; then
  echo "Missing ${ENV_FILE}" >&2
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
      sed -n '2,12p' "$0"
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
  mkdir -p "${PRE_WP_PATH}/wp-content/plugins"
  local -a rsync_plugins=(rsync -az --partial --human-readable --stats)
  [[ "$DRY_RUN" -eq 1 ]] && rsync_plugins+=(-n)
  rsync_plugins+=("${PLUGIN_RSYNC_EXCLUDES[@]}")
  "${rsync_plugins[@]}" -e "$RSYNC_RSH" \
    "${PROD_REMOTE}:${PROD_WP_PATH}/wp-content/plugins/" \
    "${PRE_WP_PATH}/wp-content/plugins/"
}

: "${PROD_SSH_HOST:?}"
: "${PROD_SSH_PORT:?}"
: "${PROD_SSH_USER:?}"
: "${PROD_SSH_IDENTITY:?}"
: "${PROD_WP_PATH:?}"
: "${PROD_URL:?}"
: "${PRE_WP_PATH:?}"
: "${PRE_URL:?}"

WP_BIN="${WP_BIN:-/usr/local/bin/wp}"

SSH_PROD=(ssh -i "${PROD_SSH_IDENTITY}" -p "${PROD_SSH_PORT}"
  -o IdentitiesOnly=yes -o BatchMode=yes
  -o StrictHostKeyChecking=accept-new
  -o ServerAliveInterval=15 -o ServerAliveCountMax=4 -o ConnectTimeout=20)
RSYNC_RSH="ssh -i ${PROD_SSH_IDENTITY} -p ${PROD_SSH_PORT} -o IdentitiesOnly=yes -o BatchMode=yes -o StrictHostKeyChecking=accept-new -o ServerAliveInterval=15 -o ServerAliveCountMax=4"
PROD_REMOTE="${PROD_SSH_USER}@${PROD_SSH_HOST}"

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

wp_pre() {
  # SiteGround's `wp` is a bash wrapper (do not invoke it with php).
  "${WP_BIN}" --path="${PRE_WP_PATH}" "$@"
}

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
echo "Leaves:  plugins/igfae-content-viewer + theme (GitHub deploy)"
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
test -f "${PRE_WP_PATH}/wp-config.php" && echo "   PRE_OK"

if [[ "$DRY_RUN" -eq 1 ]]; then
  if [[ "$PLUGINS" -eq 1 ]]; then
    echo "→ Dry-run: remote plugins sample…"
    "${SSH_PROD[@]}" "$PROD_REMOTE" "ls '${PROD_WP_PATH}/wp-content/plugins' | head"
    sync_plugins_from_prod
  fi
  if [[ "$PLUGINS_ONLY" -eq 0 ]]; then
  echo "→ Dry-run: prod uploads sample…"
  "${SSH_PROD[@]}" "$PROD_REMOTE" "ls '${PROD_WP_PATH}/wp-content/uploads' | head"
  if [[ "$DB_ONLY" -eq 0 ]]; then
    echo "→ Dry-run rsync uploads prod → pre…"
    mkdir -p "${PRE_WP_PATH}/wp-content/uploads"
    RSYNC_UPLOADS=(rsync -azn --partial --human-readable --stats)
    [[ "$UPLOADS_NEW" -eq 1 ]] && RSYNC_UPLOADS+=(--ignore-existing)
    "${RSYNC_UPLOADS[@]}" -e "$RSYNC_RSH" \
      "${PROD_REMOTE}:${PROD_WP_PATH}/wp-content/uploads/" \
      "${PRE_WP_PATH}/wp-content/uploads/"
  fi
  fi
  echo "Dry-run finished (no DB import)."
  exit 0
fi

if [[ "$PLUGINS_ONLY" -eq 1 ]]; then
  sync_plugins_from_prod
  echo
  echo "Done. Plugins synced (igfae-content-viewer unchanged)."
  exit 0
fi

echo "→ Backing up pre IGFAE Viewer DB settings…"
OPTIONS_BACKUP="${SCRIPT_DIR}/.igfae-options-backup.json"
rm -f "$OPTIONS_BACKUP"
umask 077
touch "$OPTIONS_BACKUP"
chmod 600 "$OPTIONS_BACKUP"
keys_php="$(printf "'%s'," "${IGFAE_DB_OPTION_KEYS[@]}")"
keys_php="${keys_php%,}"
wp_pre eval "
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

DUMP_LOCAL="$(mktemp -t igfae-prod-dump.XXXXXX)"
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
LIVE_PREFIX="$(wp_pre db prefix | tr -d '[:space:]')"
DUMP_PREFIX="wp_"
if [[ "$LIVE_PREFIX" != "$DUMP_PREFIX" ]]; then
  echo "   remapping table prefix ${DUMP_PREFIX} → ${LIVE_PREFIX}"
  gzip -dc "$DUMP_LOCAL" | sed "s/\`${DUMP_PREFIX}/\`${LIVE_PREFIX}/g" | wp_pre db import -
else
  gzip -dc "$DUMP_LOCAL" | wp_pre db import -
fi
rm -f "$DUMP_LOCAL"
DUMP_LOCAL=""

if [[ "$LIVE_PREFIX" != "wp_" ]]; then
  echo "   dropping leftover wp_* tables from a previous import…"
  wp_pre eval '
    global $wpdb;
    $rows = $wpdb->get_col("SHOW TABLES LIKE \"wp_%\"");
    foreach ((array) $rows as $t) {
      if (strpos($t, "wp_") === 0) {
        $wpdb->query("DROP TABLE IF EXISTS `{$t}`");
      }
    }
    echo "dropped " . count((array) $rows) . " leftover wp_* tables\n";
  ' || true

  echo "   remapping wp_* usermeta / user_roles for table prefix ${LIVE_PREFIX}…"
  wp_pre eval '
    global $wpdb;
    $p = $wpdb->prefix;
    $map = array(
      "wp_capabilities" => $p . "capabilities",
      "wp_user_level" => $p . "user_level",
      "wp_dashboard_quick_press_last_post_id" => $p . "dashboard_quick_press_last_post_id",
      "wp_nav_menu_recently_edited" => $p . "nav_menu_recently_edited",
      "wp_user-settings" => $p . "user-settings",
      "wp_user-settings-time" => $p . "user-settings-time",
    );
    $migrated = 0;
    foreach ($map as $old => $new) {
      $rows = $wpdb->get_results($wpdb->prepare(
        "SELECT user_id, meta_value FROM {$wpdb->usermeta} WHERE meta_key = %s",
        $old
      ));
      foreach ((array) $rows as $row) {
        $exists = (int) $wpdb->get_var($wpdb->prepare(
          "SELECT COUNT(*) FROM {$wpdb->usermeta} WHERE user_id = %d AND meta_key = %s",
          (int) $row->user_id,
          $new
        ));
        if ($exists) {
          continue;
        }
        $wpdb->insert($wpdb->usermeta, array(
          "user_id" => (int) $row->user_id,
          "meta_key" => $new,
          "meta_value" => $row->meta_value,
        ));
        $migrated++;
      }
    }
    $roles = get_option("wp_user_roles");
    if (is_array($roles) && ! get_option($p . "user_roles")) {
      update_option($p . "user_roles", $roles);
    }
    echo "remapped {$migrated} usermeta rows\n";
  ' || true
fi

echo "→ Restoring pre IGFAE Viewer DB settings…"
wp_pre eval "
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

echo "→ search-replace URLs…"
PROD_ORIGIN="${PROD_URL%/}"
PRE_ORIGIN="${PRE_URL%/}"
wp_pre search-replace "${PROD_ORIGIN}" "${PRE_ORIGIN}" --all-tables --report-changed-only || true
if [[ "$PROD_ORIGIN" == https://* ]]; then
  wp_pre search-replace "http://${PROD_ORIGIN#https://}" "${PRE_ORIGIN}" --all-tables --report-changed-only || true
fi

echo "→ Discourage search engine indexing (blog_public=0)…"
# wp option update uses get_option(); pre mu-plugin filters blog_public — update DB directly.
OPTS_TABLE="$(wp_pre db prefix | tr -d '[:space:]')options"
wp_pre db query "UPDATE \`${OPTS_TABLE}\` SET option_value = '0' WHERE option_name = 'blog_public'"

echo "→ rewrite flush…"
wp_pre rewrite flush || true
wp_pre cache flush || true

if [[ "$DB_ONLY" -eq 0 ]]; then
  echo "→ rsync uploads$([[ "$UPLOADS_NEW" -eq 1 ]] && echo " (skip existing)")…"
  mkdir -p "${PRE_WP_PATH}/wp-content/uploads"
  RSYNC_UPLOADS=(rsync -az --partial --human-readable --stats)
  [[ "$UPLOADS_NEW" -eq 1 ]] && RSYNC_UPLOADS+=(--ignore-existing)
  "${RSYNC_UPLOADS[@]}" -e "$RSYNC_RSH" \
    "${PROD_REMOTE}:${PROD_WP_PATH}/wp-content/uploads/" \
    "${PRE_WP_PATH}/wp-content/uploads/"
fi

if [[ "$PLUGINS" -eq 1 ]]; then
  sync_plugins_from_prod
fi

echo
echo "Done. Pre site: ${PRE_URL}"
if [[ "$PLUGINS" -eq 1 ]]; then
  echo "Plugins synced from prod (igfae-content-viewer excluded)."
else
  echo "Plugins not synced (use --plugins or --plugins-only when needed)."
fi
echo "Theme not overwritten (GitHub deploy). IGFAE DB options restored."
