# Deploy BLC Chicago — local + remote

Package the theme and plugin, then install the same build on **local** (Local WP) and **remote** (e.g. DreamHost / sitewing.agency).

## What gets packaged

| Artifact | Installs to |
|----------|-------------|
| `dist/blc-chicago-*.zip` | `wp-content/themes/blc-chicago/` |
| `dist/blc-membership-summary-*.zip` | `wp-content/plugins/blc-membership-summary/` |
| `dist/blc-wp-*.zip` | Both of the above (convenience bundle) |

**Not in the zip:** Paid Memberships Pro, Formidable Pro, member CSV, `/design/` prototype.

## Build the package

From the repo root:

```bash
cd /Users/whittfieldholmes/Downloads/Development/blc
chmod +x scripts/package-release.sh
./scripts/package-release.sh
```

Output lands in `dist/`. Rebuild whenever you change the theme or plugin before uploading to remote.

---

## Local site (symlink — live edits)

Use symlinks so the Local site reads this repo directly (no zip upload needed day to day):

```bash
REPO="/Users/whittfieldholmes/Downloads/Development/blc"
WP_CONTENT="$HOME/Local Sites/blcchicago/app/public/wp-content"

ln -sf "$REPO/theme/blc-chicago" "$WP_CONTENT/themes/blc-chicago"
ln -sf "$REPO/plugin/blc-membership-summary" "$WP_CONTENT/plugins/blc-membership-summary"
```

Then in WP admin: activate **BLC Membership Summary** + **BLC Chicago**.

Full local checklist: [LOCAL-SETUP.md](LOCAL-SETUP.md)

---

## Remote site (DreamHost / sitewing.agency)

### A. Upload via WP admin (simplest)

1. Build zips (`./scripts/package-release.sh`)
2. **Appearance → Themes → Add New → Upload Theme** → `dist/blc-chicago-*.zip` → Install → Activate
3. **Plugins → Add New → Upload Plugin** → `dist/blc-membership-summary-*.zip` → Install → Activate
4. Install **Paid Memberships Pro** + **Formidable Forms Pro** (from wordpress.org / your licenses)
5. **Settings → Permalinks** → Post name → Save
6. **Settings → BLC Setup** → follow checklist
7. **Members → Import** → upload CSV (copy from local; do not commit PII to public git)
8. **Settings → BLC Forms** → paste Formidable form IDs after you create forms

### B. Upload via SFTP

1. Build zips, then unzip locally or on the server:

```bash
# Example after SFTP of the two zips into ~/tmp/
cd ~/tmp
unzip blc-chicago-*.zip
unzip blc-membership-summary-*.zip

# DreamHost paths vary; typical:
cp -R blc-chicago ~/sitewing.agency/wp-content/themes/
cp -R blc-membership-summary ~/sitewing.agency/wp-content/plugins/
```

2. Activate in WP admin (same as above).

### C. Re-deploy after theme changes

1. Run `./scripts/package-release.sh` again  
2. Upload the new theme zip (WP will replace the theme folder)  
   — or SFTP overwrite `wp-content/themes/blc-chicago/`  
3. Hard-refresh the browser; bump in `style.css` / `BLC_THEME_VERSION` busts asset cache

**Do not** copy `/design/` to production. It’s the static reference only.

---

## Keep local and remote in sync

| Approach | Local | Remote |
|----------|-------|--------|
| Day-to-day coding | Symlink to repo | — |
| Ship a test build | — | Upload fresh zips from `dist/` |
| Same content | Import CSV + seed leaders/events on both | Same CSV + activate theme (seeds pages) |

Database content (members, posts, PMPro levels) does **not** travel with the theme zip. Re-import CSV and recreate PMPro levels / Formidable forms on remote, or use a migration plugin later if you need a full DB clone.

---

## Smoke test (both environments)

| URL | Expect |
|-----|--------|
| `/` | Homepage, header logo (white wordmark + blue BLC) |
| `/leadership/` | Old-site hero copy; executive cards visible (no reveal hide) |
| `/directory/` | Org list after import |
| `/events/` | Calendar hero + event rows |
| `/contact/` | Formidable form once ID is set |
| `/join-us/` | Tier grid / PMPro levels |

---

## Remote notes (sitewing.agency)

- Site URL prefers **www** (`https://www.sitewing.agency/`) — apex redirects there
- PHP 8.2+ recommended
- After first theme activate, flush permalinks once
- Stripe: use **test keys** on staging; live keys only on production blcchicago.com later
