#!/usr/bin/env python3
"""Generate static member profile drill-down pages from the WA export."""

from __future__ import annotations

import csv
import html
import re
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
CSV = ROOT / "data" / "members-export.csv"
DESIGN = ROOT / "design"
OUT = DESIGN / "members"

# First five rows on members-directory.html (alphabetical active list).
TOP_PROFILES: list[tuple[str, str, str, str]] = [
    ("aarp-illinois", "AARP Illinois", "Chartay", "T. Robinson"),
    ("abc7-chicago", "ABC 7 Chicago", "Samantha", "Chatman"),
    ("advocate-health-tequilla-lopez", "Advocate Health", "Tequilla", "Lopez"),
    ("advocate-health-dia-nichols", "Advocate Health", "Dia", "Nichols"),
    ("advocate-health-gwendolyn-olgesby-odom", "Advocate Health", "Gwendolyn", "Olgesby-Odom"),
]


def clean(s: str | None) -> str:
    return (s or "").strip()


def esc(s: str) -> str:
    return html.escape(clean(s), quote=True)


def industry_primary(raw: str | None) -> str:
    raw = clean(raw)
    if not raw:
        return ""
    first = raw.split(",")[0].strip()
    if first == "Communications/Marketing":
        return "Communications"
    if first == "Construction Related Professional Services":
        return "Construction"
    return first


def tier_clean(level: str | None) -> str:
    return clean(level).rstrip()


def badge_class(tier: str) -> str:
    if "vanguard" in tier.lower():
        return "tier-badge tier-badge--vanguard"
    return "tier-badge"


def logo_label(org: str) -> str:
    org = clean(org)
    if not org:
        return "?"
    words = re.findall(r"[A-Za-z0-9]+", org)
    if not words:
        return org[:4].upper()
    if len(words) == 1:
        w = words[0]
        return (w[:4] if len(w) > 4 else w).upper() if len(w) <= 6 else w[:3].upper()
    return "".join(w[0] for w in words[:3]).upper()


def domain_label(url: str) -> str:
    url = clean(url)
    if not url:
        return ""
    u = re.sub(r"^https?://", "", url, flags=re.I).split("/")[0]
    return u.replace("www.", "")


def href_url(url: str) -> str:
    url = clean(url)
    if not url:
        return ""
    return url if re.match(r"^https?://", url, re.I) else f"https://{url}"


def split_lines(text: str | None, limit: int = 6) -> list[str]:
    if not clean(text):
        return []
    items: list[str] = []
    for line in re.split(r"[\n\r]+", clean(text)):
        for part in re.split(r"[;,]", line):
            part = part.strip()
            if part and part not in items:
                items.append(part)
            if len(items) >= limit:
                return items
    return items


def find_member(
    rows: list[dict[str, str]], org: str, first: str, last: str
) -> dict[str, str]:
    org_key = org.lower()
    matches = [
        r
        for r in rows
        if clean(r.get("Organization")).lower() == org_key
        and clean(r.get("First name")).lower() == first.lower()
        and clean(r.get("Last name")).lower() == last.lower()
        and clean(r.get("Membership status")) == "Active"
    ]
    if not matches:
        raise SystemExit(f"No CSV row for {org} / {first} {last}")
    return matches[0]


def tag_list(items: list[str]) -> str:
    if not items:
        return '<p style="margin:0;color:var(--blc-muted)">Not listed.</p>'
    return (
        '<ul class="tag-list">\n'
        + "".join(f"              <li>{esc(i)}</li>\n" for i in items)
        + "            </ul>"
    )


def render_profile(slug: str, row: dict[str, str]) -> str:
    org = clean(row.get("Organization"))
    first = clean(row.get("First name"))
    last = clean(row.get("Last name"))
    person = f"{first} {last}".strip()
    title = clean(row.get("Position/Title"))
    tier = tier_clean(row.get("Membership level"))
    industry = industry_primary(row.get("Industry"))
    logo = logo_label(org)
    web = href_url(row.get("Website"))
    web_label = domain_label(row.get("Website")) or "Website"
    bio = clean(row.get("Bio"))
    products = split_lines(row.get("Products or Services"))
    open_to = split_lines(row.get("Contact Me Regarding")) or split_lines(
        row.get("Curated Collaboration")
    )
    glimpse = (
        clean(row.get("Curated Collaboration"))
        or clean(row.get("Intentions"))
        or clean(row.get("Why BLC?"))
        or clean(row.get("Objectives"))
        or "Active BLC member."
    )
    glimpse_title = glimpse.split(".")[0][:80] if glimpse else "Member update"
    address = clean(row.get("Mailing Address")).replace("\n", "<br>\n              ")
    phone = clean(row.get("Company Phone"))
    email = clean(row.get("Company Email"))
    page_title = f"{org} — Member Profile · BLC"
    person_line = f"{person} · {title}" if title else person
    meta_ind = (
        f'\n              <span style="color:rgba(255,255,255,0.65)">{esc(industry)}</span>'
        if industry
        else ""
    )

    public_contact = []
    if address:
        public_contact.append(address)
    if phone:
        public_contact.append(esc(phone))
    if email:
        public_contact.append(f'<a href="mailto:{esc(email)}">{esc(email)}</a>')
    public_contact_html = "<br><br>\n              ".join(public_contact) or "Contact on file."

    connect_buttons = ['<a class="btn btn--coral" href="contact.html">Request introduction</a>']
    if email:
        connect_buttons.append(
            f'<a class="btn btn--outline" href="mailto:{esc(email)}">Email {esc(org)}</a>'
        )
    if web:
        connect_buttons.append(
            f'<a class="btn btn--outline" href="{esc(web)}" target="_blank" rel="noopener">{esc(web_label)}</a>'
        )
    connect_html = "\n              ".join(connect_buttons)

    social = []
    if web:
        social.append(
            f'<li><a href="{esc(web)}" target="_blank" rel="noopener">{esc(web_label)}</a></li>'
        )
    social_html = (
        '<ul class="social-list">\n'
        + "".join(f"              {s}\n" for s in social)
        + "            </ul>"
        if social
        else '<p style="margin:0;color:var(--blc-muted)">Not listed.</p>'
    )

    about_html = (
        f"<p>{esc(bio)}</p>"
        if bio
        else f"<p>{esc(org)} is an active member of the Business Leadership Council.</p>"
    )

    return f"""<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{esc(page_title)}</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Albert+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Inter:ital,opsz,wght@0,14..32,400;0,14..32,500;0,14..32,600;0,14..32,700;1,14..32,400&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
  <div class="site-header-group">
    <div class="member-utility-bar">
      <div class="container member-utility-bar__inner">
        <p class="member-utility-bar__text">BLC members — <a href="../login.html">Log in</a> for your directory, events, and profile.</p>
      </div>
    </div>
    <header class="site-header">
      <div class="container site-header__inner">
        <a class="brand" href="../index.html" aria-label="Business Leadership Council home">
          <img src="../assets/blc-logo-header-white.svg" alt="BLC">
        </a>
        <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="site-nav">Menu</button>
        <nav class="nav" id="site-nav">
          <a href="../index.html">Home</a>
          <a href="../leadership.html">Leadership</a>
          <a href="../corporate-sponsorship.html">Sponsorship</a>
          <a href="../events.html">Events</a>
          <a href="../directory.html">Directory</a>
          <a href="../members-directory.html" aria-current="page">Members</a>
          <a href="../articles.html">Articles</a>
          <a href="../contact.html">Contact</a>
          <div class="header-actions">
            <a class="nav__login" href="../login.html">Log in</a>
            <a class="nav__cta" href="../join.html">Become a Member</a>
          </div>
        </nav>
      </div>
    </header>
  </div>

  <main>
    <header class="page-hero">
      <div class="container">
        <p class="section__eyebrow"><a href="../members-directory.html" style="color:inherit;text-decoration:none">← Member network</a></p>
        <div class="profile-hero-block" style="margin:0;color:#fff">
          <div class="profile-logo" style="background:#111;border-color:rgba(255,255,255,0.12)">
            <span style="color:rgba(255,255,255,0.5);font-size:0.7rem;letter-spacing:0.08em;text-transform:uppercase">{esc(logo)}</span>
          </div>
          <div>
            <h1 style="margin-bottom:0.4rem">{esc(org)}</h1>
            <div class="member-row__meta" style="margin-bottom:0.75rem">
              <span class="{badge_class(tier)}">{esc(tier)}</span>{meta_ind}
            </div>
            <p style="margin:0;color:rgba(255,255,255,0.78)">{esc(person_line)}</p>
          </div>
        </div>
      </div>
    </header>

    <section class="section">
      <div class="container profile-layout">
        <div>
          <div class="profile-panel">
            <h2>About</h2>
            {about_html}
          </div>

          <div class="profile-panel">
            <h2>Products &amp; services</h2>
            {tag_list(products)}
          </div>

          <div class="profile-panel glimpse-card">
            <p class="glimpse-card__label">Latest update</p>
            <h2 style="font-size:1.35rem">{esc(glimpse_title)}</h2>
            <p>{esc(glimpse)}</p>
          </div>
        </div>

        <aside>
          <div class="profile-panel">
            <h2>Connect</h2>
            <div class="btn-row" style="flex-direction:column;align-items:stretch">
              {connect_html}
            </div>
          </div>

          <div class="profile-panel">
            <h2>Open to</h2>
            {tag_list(open_to) if open_to else tag_list(["Member connections", "BLC introductions"])}
          </div>

          <div class="profile-panel">
            <h2>Social</h2>
            {social_html}
          </div>

          <div class="profile-panel">
            <h2>Public contact</h2>
            <p style="font-size:0.9375rem;color:var(--blc-muted);margin:0">
              {public_contact_html}
            </p>
          </div>
        </aside>
      </div>
    </section>
    <section class="section section--ink">
      <div class="container join-band reveal">
        <div>
          <p class="section__eyebrow">Membership</p>
          <h2>Celebrate strengths. Share insights. Plan for future success.</h2>
          <p>As a member of the BLC you gain directory visibility, member connections, and a seat at Chicago’s premier Black business table.</p>
        </div>
        <div class="btn-row">
          <a class="btn btn--coral" href="../join.html">View membership tiers</a>
        </div>
      </div>
    </section>

  </main>

  <footer class="site-footer">
    <div class="container site-footer__inner">
      <img src="../assets/blc-logo-header-white.svg" alt="BLC">
      <p>© 2026 Business Leadership Council · Chicago</p>
      <a href="../contact.html">Contact</a>
      <a href="../login.html">Member log in</a>
      <a href="../join.html">Become a Member</a>
    </div>
  </footer>

  <nav class="proto-bar" aria-label="Prototype pages">
    <a href="../index.html">Home</a>
    <a href="../login.html">Login</a>
    <a href="../events.html">Events</a>
    <a href="../event.html">Lakeside Chat</a>
    <a href="../leadership.html">Leadership</a>
    <a href="../corporate-sponsorship.html">Sponsorship</a>
    <a href="../directory.html">Directory</a>
    <a href="../members-directory.html">Members</a>
    <a href="../member.html">Profile</a>
    <a href="../join.html">Join</a>
    <a href="../paywall.html">Paywall</a>
    <a href="../articles.html">Articles</a>
    <a href="../contact.html">Contact</a>
    <a href="../admin-member-summary.html">Admin summary</a>
  </nav>

  <script src="../js/main.js"></script>
</body>
</html>
"""


def wire_directory_links(profile_map: dict[tuple[str, str, str], str]) -> None:
    path = DESIGN / "members-directory.html"
    text = path.read_text(encoding="utf-8")
    if "J3 members" in text:
        count = text.count('class="member-row member-row--private"')
        text = re.sub(
            r"J3 members</p>",
            f'<p class="directory-list__count" data-directory-count>{count} members</p>',
            text,
            count=1,
        )
    for (org, first, last), href in profile_map.items():
        person = f"{first} {last}"
        org_esc = re.escape(org)
        person_esc = re.escape(person)
        pattern = (
            rf'(<div class="member-row member-row--private" data-name="{org_esc} {person_esc}"[^>]*>\s*'
            rf'<a class="member-row__stretch" href=")[^"]+(" aria-label="View {org_esc} profile")'
        )
        text, n = re.subn(pattern, rf"\1{href}\2", text, count=1)
        if n != 1:
            raise SystemExit(f"Failed to wire link for {org} / {person} (n={n})")
    path.write_text(text, encoding="utf-8")


def main() -> None:
    if not CSV.exists():
        raise SystemExit(f"Missing {CSV}")

    with CSV.open(newline="", encoding="utf-8-sig") as f:
        rows = list(csv.DictReader(f))

    OUT.mkdir(parents=True, exist_ok=True)
    profile_map: dict[tuple[str, str, str], str] = {}

    for slug, org, first, last in TOP_PROFILES:
        row = find_member(rows, org, first, last)
        out_path = OUT / f"{slug}.html"
        out_path.write_text(render_profile(slug, row), encoding="utf-8")
        profile_map[(org, first, last)] = f"members/{slug}.html"
        print(f"Wrote {out_path.relative_to(ROOT)}")

    wire_directory_links(profile_map)
    print("Updated members-directory.html links for top 5 profiles")


if __name__ == "__main__":
    main()
