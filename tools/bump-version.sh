#!/usr/bin/env bash
# Sync monorepo version to VERSION, WordPress plugin/theme headers, and README "Current release".
# Does not edit CHANGELOG — add the release section and date when cutting a prod release.
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
NEW="${1:-}"

if [[ -z "$NEW" ]]; then
  echo "Usage: $0 <x.y.z>   (current: $(cat "$ROOT/VERSION" 2>/dev/null || echo '?'))" >&2
  exit 1
fi

if [[ ! "$NEW" =~ ^[0-9]+\.[0-9]+\.[0-9]+$ ]]; then
  echo "Version must be semver x.y.z (e.g. 1.3.1)" >&2
  exit 1
fi

PLUGIN="$ROOT/plugins/igfae-content-viewer/igfae-content-viewer.php"
THEME="$ROOT/themes/igfae_nvgrd_prod/style.css"
README="$ROOT/README.md"

printf '%s\n' "$NEW" > "$ROOT/VERSION"

replace_line() {
  local file="$1" regex="$2"
  local tmp
  tmp="$(mktemp)"
  sed "$regex" "$file" > "$tmp"
  mv "$tmp" "$file"
}

replace_line "$PLUGIN" "s/^Version: .*/Version: $NEW/"
replace_line "$THEME" "s/^ \* Version: .*/ * Version: $NEW/"
replace_line "$README" "s/^\*\*Current release:\*\* \`[^\`]*\`/**Current release:** \`$NEW\`/"

echo "Version set to $NEW in VERSION, plugin header, theme header, and README."
echo "Next: update CHANGELOG.md (release section + date), commit, git tag v$NEW, push, Deploy prod (USC)."
