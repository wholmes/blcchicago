# BLC Chicago — WordPress Theme

Classic WordPress theme for [blcchicago.com](https://blcchicago.com/). UI mirrors the static prototype in `/design/` (that folder stays intact as the design reference).

## Stack

- **Paid Memberships Pro** — levels, Stripe checkout, renewals
- **Formidable Forms Pro** — contact, connect, profile edit
- **BLC Membership Summary** plugin — `plugin/blc-membership-summary/`

## Install

1. Copy or symlink `theme/blc-chicago/` to `wp-content/themes/blc-chicago/`
2. Copy or symlink `plugin/blc-membership-summary/` to `wp-content/plugins/`
3. Activate theme + plugins in WP admin
4. Install **PMPro** + **Formidable Pro**

## Pages

On theme activation, these pages are created automatically with the correct templates:

| Page slug | Template |
|-----------|----------|
| `/` | Uses `front-page.php` (Settings → Reading → “Your latest posts” or a static front page) |
| `directory` | Public Directory |
| `members/directory` | Members Directory |
| `join-us` | Join Us |
| `contact` | Contact |
| `leadership` | Leadership |
| `corporate-sponsorship` | Corporate Sponsorship |
| `events` | Events |
| `account/paywall` | Paywall |
| `articles` | Posts page (Settings → Reading) |

Leadership board posts and a sample Lakeside Chat event are seeded on activation.

## Import members

**Members → Import** — upload the Wild Apricot CSV export (`data/members-export.csv`). Rows are keyed by User ID; archived contacts are skipped. WP users are created when email is new (random password; PMPro handles login flows later).

**Seed tiers** button on the same screen creates all Wild Apricot membership level taxonomy terms with visibility meta.

## Setup wizard

**Settings → BLC Setup** — checklist, PMPro level → tier mapping, suggested level names.

On theme activation, recommended pages are created automatically (Directory, Events, Leadership, Join, Contact, Paywall, Articles).

## PMPro level → tier map

Map levels under **Settings → BLC Setup** (stored in `blc_pmpro_level_tier_map` option). Filter: `blc_pmpro_level_tier_map`.

## Directories

Public directory deduplicates by organization, excludes archived/internal/provisional tiers, and requires `Active` membership status. Helpers in `inc/member-query.php`.

## Formidable form IDs

**Settings → BLC Forms** — paste Formidable form IDs for:

- Contact
- Connect / introduction
- Profile edit

## Member profiles

Custom post types: **blc_member**, **blc_leader**, **blc_event**  
Taxonomies: **blc_industry**, **blc_membership_tier**

Member meta keys registered in `inc/member-meta.php`. Profile form saves sync via `inc/formidable-sync.php` when the profile form ID is set.

## Development

Static prototype (unchanged):

```bash
cd design && python3 -m http.server 8765
```

Theme assets copied from `/design/css/styles.css` and `/design/js/main.js` at scaffold time. To sync CSS changes from the prototype later, copy manually — do not replace `/design/`.

## Version

0.3.0 — directory query filters + org dedupe, theme activation pages, BLC Setup admin, PMPro tier sync, live homepage events/spotlight.
