# BLC static design prototype

**Preserved reference — do not remove or fold into the WP theme.** WordPress lives in `theme/blc-chicago/`; this folder stays as the static mock for design review and the proto bar.

**Stack (production):** WordPress + Paid Memberships Pro + Formidable Pro (+ Stripe). See `.cursor/skills/blc-wp-membership-theme/`.

Open `index.html` in a browser (or serve the folder):

```bash
cd design && python3 -m http.server 5173
```

Then visit http://localhost:5173/

## Pages

| File | Purpose |
|------|---------|
| `index.html` | Home — brand hero, events, spotlight, articles, join |
| `directory.html` | Public member directory |
| `members-directory.html` | Members-only network (glimpse + connect) |
| `member.html` | Full member profile |
| `join.html` | Membership tiers (PMPro checkout placeholders) |
| `paywall.html` | Unpaid / lapsed limited-account state |
| `articles.html` / `article.html` | Blog |
| `contact.html` | Contact BLC — inquiry form + office info (Formidable in WP) |
| `admin-member-summary.html` | Admin membership summary + email subscriptions (Wild Apricot parity) |

Floating **prototype bar** (bottom-right) jumps between screens.

Brand tokens live in `css/styles.css` (logo blue `#306CA8`, coral `#F03C60`, fonts **Inter** headlines + **Albert Sans** body).
