# Local WordPress setup — BLC Chicago

Use this guide while spinning up a local site. The theme and plugin live in this repo; symlink them into WordPress so edits here apply immediately.

## 1. Create a local site

Any of these work:

- **[Local](https://localwp.com/)** — create site **`blcchicago`** (PHP 8.2+, preferred). Finish the wizard and wait until the site opens in the browser once (confirms WordPress is installed).
- **wp-env** — from repo root: `npx @wordpress/env start` (requires Docker)
- **MAMP / DevKinsta / Laravel Valet** — standard WordPress install

Use **Post name** permalinks: Settings → Permalinks → `/%postname%/`

**Verify the path exists** before symlinking (Local site name must match):

```bash
ls "$HOME/Local Sites/blcchicago/app/public/wp-content"
```

If that prints `No such file or directory`, the site is not created yet or uses a different folder name under `~/Local Sites/`.

```bash
ls "$HOME/Local Sites"
```

## 2. Symlink theme + plugin

Replace `WP_ROOT` with your local `wp-content` path, e.g.  
`~/Local Sites/blc-chicago/app/public/wp-content`

```bash
REPO="/Users/whittfieldholmes/Downloads/Development/blc"
WP_CONTENT="$HOME/Local Sites/blcchicago/app/public/wp-content"

ln -sf "$REPO/theme/blc-chicago" "$WP_CONTENT/themes/blc-chicago"
ln -sf "$REPO/plugin/blc-membership-summary" "$WP_CONTENT/plugins/blc-membership-summary"
```

## 3. Activate in WP admin

1. **Plugins** → activate **BLC Membership Summary**
2. **Appearance → Themes** → activate **BLC Chicago**

On activation the theme will:

- Create pages (Directory, Events, Leadership, Join, Contact, etc.)
- Seed membership tier terms
- Seed leadership board + sample Lakeside Chat event
- Show a setup notice → **Settings → BLC Setup**

## 4. Install required plugins

| Plugin | Purpose |
|--------|---------|
| **Paid Memberships Pro** | Membership levels, Stripe checkout |
| **Formidable Forms Pro** | Contact, connect, profile forms |

Free PMPro is enough for local dev; Stripe test mode for checkout.

## 5. Import members

1. **Members → Import**
2. Upload `data/members-export.csv` from this repo (gitignored — use your local copy)
3. Click **Seed tiers** if tier terms are missing

Public directory shows **one card per organization** (deduped), active members only, excluding internal/provisional tiers.

## 6. Map PMPro levels

1. Create PMPro levels matching Wild Apricot tiers (see blueprint on **Settings → BLC Setup**)
2. On the same page, map each PMPro level ID → BLC tier term
3. After checkout, member profiles sync tier terms automatically

## 7. Formidable form IDs

**Settings → BLC Forms** — paste form IDs for contact, connect, and profile edit forms.

Profile saves sync to `blc_member` meta when field keys match the default map (filter: `blc_formidable_profile_field_map`).

## 8. Smoke-test URLs

After activation, visit:

| URL | Expected |
|-----|----------|
| `/` | Homepage with hero, events, spotlight |
| `/directory/` | Public org directory |
| `/members/directory/` | Redirect to login or paywall if not active member |
| `/leadership/` | Board + executive cards |
| `/events/` | Event list |
| `/join-us/` | Tier grid + `[pmpro_levels]` |
| `/contact/` | Contact form placeholder |
| `/articles/` | Posts archive (empty until you publish posts) |

## 9. Static prototype (unchanged)

Design reference stays in `/design/`:

```bash
cd design && python3 -m http.server 8765
```

Do not copy `/design/` into the theme — theme assets were copied at scaffold time; sync CSS manually if the prototype changes.

## Troubleshooting

**404 on member profiles** — Settings → Permalinks → Save (flush rewrite rules).

**Directory empty after import** — Confirm rows are not Archived; status should be `Active` for public listing.

**PMPro map not applying** — Save mapping under Settings → BLC Setup; re-save user membership or re-import.

**Plugin not found** — Verify symlink targets exist: `ls -la wp-content/themes/blc-chicago`
