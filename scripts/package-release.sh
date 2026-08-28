#!/usr/bin/env bash
# Build installable WordPress zips for local + remote testing.
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
DIST="$ROOT/dist"
VERSION="$(grep -E '^Version:' "$ROOT/theme/blc-chicago/style.css" | awk '{print $2}' | tr -d '\r')"
STAMP="$(date +%Y%m%d)"
BUNDLE="blc-wp-${VERSION}-${STAMP}"

mkdir -p "$DIST"
rm -f "$DIST"/blc-chicago-*.zip "$DIST"/blc-membership-summary-*.zip "$DIST"/blc-wp-*.zip

THEME_ZIP="$DIST/blc-chicago-${VERSION}.zip"
PLUGIN_ZIP="$DIST/blc-membership-summary-1.0.0.zip"
BUNDLE_ZIP="$DIST/${BUNDLE}.zip"

# Theme zip must extract to folder "blc-chicago/"
(
  cd "$ROOT/theme"
  zip -rq "$THEME_ZIP" blc-chicago \
    -x '*/.DS_Store' \
    -x '*/.git/*' \
    -x '*/node_modules/*'
)

# Plugin zip must extract to folder "blc-membership-summary/"
(
  cd "$ROOT/plugin"
  zip -rq "$PLUGIN_ZIP" blc-membership-summary \
    -x '*/.DS_Store' \
    -x '*/.git/*'
)

# Combined bundle for one upload session
(
  cd "$DIST"
  zip -rq "$BUNDLE_ZIP" \
    "blc-chicago-${VERSION}.zip" \
    "blc-membership-summary-1.0.0.zip"
)

cat > "$DIST/MANIFEST.txt" <<EOF
BLC Chicago WordPress package
Theme version:  ${VERSION}
Plugin version: 1.0.0
Built:          $(date -u +%Y-%m-%dT%H:%M:%SZ)

Contents
--------
blc-chicago-${VERSION}.zip              → wp-content/themes/blc-chicago/
blc-membership-summary-1.0.0.zip        → wp-content/plugins/blc-membership-summary/
${BUNDLE}.zip                           → both of the above

Not included (install separately)
---------------------------------
- Paid Memberships Pro
- Formidable Forms Pro
- Member CSV (data/members-export.csv — PII)
- Static prototype (design/)

Install
-------
See docs/DEPLOY.md
EOF

echo "Built:"
ls -lh "$THEME_ZIP" "$PLUGIN_ZIP" "$BUNDLE_ZIP" "$DIST/MANIFEST.txt"
echo
echo "Upload on remote: Appearance → Themes → Add New → Upload"
echo "                 Plugins → Add New → Upload"
echo "Or unzip into wp-content/themes and wp-content/plugins via SFTP."
