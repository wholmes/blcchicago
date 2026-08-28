# Business Leadership Council (BLC Chicago)

WordPress membership site for [blcchicago.com](https://blcchicago.com/).

## Repository layout

| Path | Purpose |
|------|---------|
| **`design/`** | Static HTML prototype — **preserved as design reference** (do not delete) |
| **`theme/blc-chicago/`** | WordPress theme (mirrors `/design/` UI) |
| **`plugin/blc-membership-summary/`** | Admin membership summary + email reports |
| **`data/members-export.csv`** | Wild Apricot export (PII — gitignored) |
| **`brand/blc-logo.png`** | Official wordmark |
| **`.cursor/skills/blc-wp-membership-theme/`** | Build skill + stack docs |

## Stack

WordPress + **Paid Memberships Pro** + **Formidable Pro** + Stripe

## Quick start — static prototype

```bash
cd design && python3 -m http.server 8765
```

Open http://localhost:8765/

## Quick start — WordPress theme

See [theme/blc-chicago/README.md](theme/blc-chicago/README.md) and **[docs/LOCAL-SETUP.md](docs/LOCAL-SETUP.md)** for local install + symlinks.

## Package for local + remote

Build installable zips (theme + membership summary plugin):

```bash
./scripts/package-release.sh
```

Artifacts land in `dist/`. Deploy steps for Local WP and DreamHost/remote: **[docs/DEPLOY.md](docs/DEPLOY.md)**.
