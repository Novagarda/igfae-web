#!/usr/bin/env bash
# Install or update the prod→pre pull script on the pre server.
# Copies the versioned script from this repo to ~/igfae-sync/ (outside the web root).
# Does not deploy plugin/theme. Does not run the pull.
#
# Usage:
#   ./tools/install-pre-sync.sh
#   ./tools/install-pre-sync.sh --dry-run

set -euo pipefail

ROOT_DIR="$(cd "$(dirname "$0")/.." && pwd)"
ENV_FILE="${ROOT_DIR}/tools/usc.env"
SCRIPT_SRC="${ROOT_DIR}/tools/pre-pull-from-prod.sh"
EXAMPLE_SRC="${ROOT_DIR}/tools/pre-pull.env.example"
LOCAL_ENV="${ROOT_DIR}/tools/pre-pull.env"
REMOTE_DIR="igfae-sync"

if [[ ! -f "$ENV_FILE" ]]; then
  echo "Missing ${ENV_FILE}. Copy tools/usc.env.example to tools/usc.env" >&2
  exit 1
fi
if [[ ! -f "$SCRIPT_SRC" ]]; then
  echo "Missing ${SCRIPT_SRC}" >&2
  exit 1
fi

# shellcheck disable=SC1090
source "$ENV_FILE"

DRY_RUN=0
while [[ $# -gt 0 ]]; do
  case "$1" in
    --dry-run) DRY_RUN=1 ;;
    -h|--help)
      sed -n '2,11p' "$0"
      exit 0
      ;;
    *) echo "Unknown flag: $1" >&2; exit 1 ;;
  esac
  shift
done

: "${PRE_SSH_HOST:?}"
: "${PRE_SSH_PORT:?}"
: "${PRE_SSH_USER:?}"
: "${PRE_SSH_IDENTITY:?}"

SSH=(ssh -i "${PRE_SSH_IDENTITY}" -p "${PRE_SSH_PORT}"
  -o IdentitiesOnly=yes
  -o StrictHostKeyChecking=accept-new
  -o ServerAliveInterval=15 -o ServerAliveCountMax=4 -o ConnectTimeout=20)
RSYNC_RSH="ssh -i ${PRE_SSH_IDENTITY} -p ${PRE_SSH_PORT} -o IdentitiesOnly=yes -o StrictHostKeyChecking=accept-new"
REMOTE="${PRE_SSH_USER}@${PRE_SSH_HOST}"

echo "Install prod→pre sync tools → ${REMOTE}:~/${REMOTE_DIR}/"
if [[ "$DRY_RUN" -eq 1 ]]; then
  echo "(dry-run) mkdir -p ~/${REMOTE_DIR}"
  echo "(dry-run) rsync ${SCRIPT_SRC} → ~/${REMOTE_DIR}/pre-pull-from-prod.sh"
  if [[ -f "$LOCAL_ENV" ]]; then
    echo "(dry-run) rsync ${LOCAL_ENV} → ~/${REMOTE_DIR}/pre-pull.env"
  else
    echo "(dry-run) no local tools/pre-pull.env; would copy example if remote env missing"
  fi
  exit 0
fi

"${SSH[@]}" "$REMOTE" "mkdir -p ~/${REMOTE_DIR} && chmod 700 ~/${REMOTE_DIR}"

rsync -az -e "$RSYNC_RSH" \
  "$SCRIPT_SRC" \
  "${REMOTE}:~/${REMOTE_DIR}/pre-pull-from-prod.sh"

"${SSH[@]}" "$REMOTE" "chmod 700 ~/${REMOTE_DIR}/pre-pull-from-prod.sh"

if [[ -f "$LOCAL_ENV" ]]; then
  rsync -az -e "$RSYNC_RSH" \
    "$LOCAL_ENV" \
    "${REMOTE}:~/${REMOTE_DIR}/pre-pull.env"
  "${SSH[@]}" "$REMOTE" "chmod 600 ~/${REMOTE_DIR}/pre-pull.env"
else
  "${SSH[@]}" "$REMOTE" "test -f ~/${REMOTE_DIR}/pre-pull.env" || {
    echo "Remote has no pre-pull.env; copying example (fill it on the server)."
    rsync -az -e "$RSYNC_RSH" \
      "$EXAMPLE_SRC" \
      "${REMOTE}:~/${REMOTE_DIR}/pre-pull.env.example"
  }
fi

echo "Installed. On pre:"
echo "  cd ~/${REMOTE_DIR} && ./pre-pull-from-prod.sh --dry-run"
