#!/usr/bin/env python3
"""Rebuild design/directory.html and design/members-directory.html from the WA export.

Reads data/members-export.csv (gitignored PII). Public org rows use company contact only.
Members network rows omit personal email/phone (prototype is on public GitHub Pages).
"""

from __future__ import annotations

import csv
import html
import re
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
CSV = ROOT / "data" / "members-export.csv"
DESIGN = ROOT / "design"

PUBLIC_EXCLUDE_LEVELS = {"Internal Office", "Provisional Event Invitee"}
PRIVATE_EXCLUDE_LEVELS = {"Internal Office"}

RANK = {
    "Corporate Leader": 100,
    "Board Member": 90,
    "Established Leader": 80,
    "Sustained Leader": 70,
    "Strategic Partner": 65,
    "Civic & Community Leader": 60,
    "Vanguard Leader": 50,
}


def clean(s: str | None) -> str:
    return (s or "").strip()


def esc(s: str) -> str:
    return html.escape(clean(s), quote=True)


def title_case_name(first: str | None, last: str | None) -> str:
    def fix(part: str) -> str:
        part = clean(part)
        if not part:
            return ""
        parts: list[str] = []
        for p in re.split(r"(\s|-)", part):
            if p in (" ", "-"):
                parts.append(p)
            elif p.lower() in ("ii", "iii", "iv", "jr", "jr.", "sr", "sr."):
                parts.append(p.upper() if p.lower() in ("ii", "iii", "iv") else p.title())
            else:
                parts.append(p[:1].upper() + p[1:].lower() if p else p)
        return "".join(parts)

    return f"{fix(first or '')} {fix(last or '')}".strip()


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
    u = re.sub(r"^https?://", "", url, flags=re.I)
    u = u.split("/")[0]
    return u.replace("www.", "")


def tel_href(phone: str) -> str:
    digits = re.sub(r"[^\d+]", "", phone)
    if digits.startswith("1") and len(digits) == 11:
        return f"+{digits}"
    if len(digits) == 10:
        return f"+1{digits}"
    return digits


def badge_class(tier: str) -> str:
    if "vanguard" in tier.lower():
        return "tier-badge tier-badge--vanguard"
    return "tier-badge"


def public_contact_html(row: dict[str, str]) -> str:
    items: list[str] = []
    phone = clean(row.get("Company Phone"))
    email = clean(row.get("Company Email"))
    web = clean(row.get("Website"))
    if phone:
        items.append(
            f'<li><span class="contact-lines__label">Tel</span>'
            f'<a href="tel:{esc(tel_href(phone))}">{esc(phone)}</a></li>'
        )
    if email:
        items.append(
            f'<li><span class="contact-lines__label">Email</span>'
            f'<a href="mailto:{esc(email)}">{esc(email)}</a></li>'
        )
    if web:
        label = domain_label(web) or "Website"
        href = web if re.match(r"^https?://", web, re.I) else f"https://{web}"
        items.append(
            f'<li><span class="contact-lines__label">Web</span>'
            f'<a href="{esc(href)}" target="_blank" rel="noopener">{esc(label)}</a></li>'
        )
    if not items:
        products = clean(row.get("Products or Services"))
        if products:
            teaser = re.split(r"[\n\r]+", products)[0][:80]
            items.append(
                f'<li><span class="contact-lines__label">Focus</span><span>{esc(teaser)}</span></li>'
            )
        else:
            items.append(
                '<li><span class="contact-lines__label">Info</span><span>Profile on file</span></li>'
            )
    return (
        '<ul class="contact-lines">\n              '
        + "\n              ".join(items)
        + "\n              </ul>"
    )


def private_contact_html(row: dict[str, str]) -> str:
    items: list[str] = []
    open_to = clean(row.get("Contact Me Regarding")) or clean(row.get("Curated Collaboration"))
    products = clean(row.get("Products or Services"))
    web = clean(row.get("Website"))
    company_email = clean(row.get("Company Email"))
    if open_to:
        teaser = re.split(r"[\n\r]+", open_to)[0][:90]
        items.append(
            f'<li><span class="contact-lines__label">Open</span><span>{esc(teaser)}</span></li>'
        )
    elif products:
        teaser = re.split(r"[\n\r]+", products)[0][:90]
        items.append(
            f'<li><span class="contact-lines__label">Offers</span><span>{esc(teaser)}</span></li>'
        )
    elif company_email:
        items.append(
            f'<li><span class="contact-lines__label">Email</span>'
            f'<a href="mailto:{esc(company_email)}">{esc(company_email)}</a></li>'
        )
    elif web:
        label = domain_label(web) or "Website"
        href = web if re.match(r"^https?://", web, re.I) else f"https://{web}"
        items.append(
            f'<li><span class="contact-lines__label">Web</span>'
            f'<a href="{esc(href)}" target="_blank" rel="noopener">{esc(label)}</a></li>'
        )
    else:
        items.append(
            '<li><span class="contact-lines__label">Open</span><span>Member connections</span></li>'
        )
    return (
        '<ul class="contact-lines">\n              '
        + "\n              ".join(items)
        + "\n              </ul>"
    )


def glimpse_html(row: dict[str, str]) -> str:
    for key in (
        "Curated Collaboration",
        "Intentions",
        "Why BLC?",
        "Objectives",
        "Contribution",
    ):
        val = clean(row.get(key))
        if val:
            teaser = re.split(r"[\n\r]+", val)[0][:140]
            return f"""<div class="member-row__glimpse">
              <ul class="contact-lines">
                <li>
                  <span class="contact-lines__label contact-lines__label--recent">Recent</span>
                  <span class="member-row__glimpse-text">{esc(teaser)}</span>
                </li>
              </ul>
              <span class="member-row__connect">Connect →</span>
            </div>"""
    return """<div class="member-row__glimpse">
              <ul class="contact-lines">
                <li>
                  <span class="contact-lines__label contact-lines__label--recent">Recent</span>
                  <span class="member-row__glimpse-text">Active BLC member.</span>
                </li>
              </ul>
              <span class="member-row__connect">Connect →</span>
            </div>"""


def update_directory(
    path: Path,
    tiers: list[str],
    industries: list[str],
    count_label: str,
    rows_html: list[str],
) -> None:
    text = path.read_text(encoding="utf-8")
    tier_opts = ['              <option value="">All tiers</option>'] + [
        f"              <option>{esc(t)}</option>" for t in tiers
    ]
    text = re.sub(
        r'(<select name="tier"[^>]*>)(.*?)(</select>)',
        lambda m: m.group(1) + "\n" + "\n".join(tier_opts) + "\n            " + m.group(3),
        text,
        count=1,
        flags=re.S,
    )
    ind_opts = ['              <option value="">All industries</option>'] + [
        f"              <option>{esc(i)}</option>" for i in industries
    ]
    text = re.sub(
        r'(<select name="industry"[^>]*>)(.*?)(</select>)',
        lambda m: m.group(1) + "\n" + "\n".join(ind_opts) + "\n            " + m.group(3),
        text,
        count=1,
        flags=re.S,
    )
    text = re.sub(
        r'(<p class="directory-list__count" data-directory-count>)(.*?)(</p>)',
        rf"\1{count_label}\3",
        text,
        count=1,
    )
    head_re = (
        r'(<div class="directory-list[^"]*"[^>]*data-directory-list[^>]*>\s*'
        r'<div class="directory-list__head[^"]*"[^>]*>.*?</div>\s*)(.*?)'
        r'(\s*</div>\s*</div>\s*</section>)'
    )

    def repl(m: re.Match[str]) -> str:
        return m.group(1) + "\n" + "\n\n".join(rows_html) + "\n" + m.group(3)

    new_text, n = re.subn(head_re, repl, text, count=1, flags=re.S)
    if n != 1:
        raise SystemExit(f"Failed to replace list in {path} (n={n})")
    path.write_text(new_text, encoding="utf-8")


def main() -> None:
    if not CSV.exists():
        raise SystemExit(f"Missing {CSV}")

    with CSV.open(newline="", encoding="utf-8-sig") as f:
        rows = list(csv.DictReader(f))

    active = [
        r
        for r in rows
        if clean(r.get("Archived")).lower() != "yes"
        and clean(r.get("Membership status")) == "Active"
    ]

    public_people = [
        r for r in active if tier_clean(r.get("Membership level")) not in PUBLIC_EXCLUDE_LEVELS
    ]
    org_best: dict[str, dict[str, str]] = {}
    for r in public_people:
        org = clean(r.get("Organization")) or title_case_name(
            r.get("First name"), r.get("Last name")
        )
        key = org.lower()
        cur = org_best.get(key)
        if cur is None or RANK.get(tier_clean(r.get("Membership level")), 10) > RANK.get(
            tier_clean(cur.get("Membership level")), 10
        ):
            org_best[key] = r

    orgs = sorted(
        org_best.values(),
        key=lambda r: (
            clean(r.get("Organization"))
            or title_case_name(r.get("First name"), r.get("Last name"))
        ).lower(),
    )

    public_rows: list[str] = []
    for r in orgs:
        org = clean(r.get("Organization")) or title_case_name(
            r.get("First name"), r.get("Last name")
        )
        tier = tier_clean(r.get("Membership level"))
        ind = industry_primary(r.get("Industry"))
        meta_ind = f"\n                <span>{esc(ind)}</span>" if ind else ""
        public_rows.append(
            f"""          <div class="member-row" data-name="{esc(org)}" data-tier="{esc(tier)}" data-industry="{esc(ind)}">
            <a class="member-row__stretch" href="member.html" aria-label="View {esc(org)} profile"></a>
            <div class="member-row__logo"><span>{esc(logo_label(org))}</span></div>
            <div class="member-row__main">
              <h2 class="member-row__name">{esc(org)}</h2>
              <div class="member-row__meta">
                <span class="{badge_class(tier)}">{esc(tier)}</span>{meta_ind}
              </div>
            </div>
            <div class="member-row__contact">
              {public_contact_html(r)}
            </div>
          </div>"""
        )

    private_people = [
        r for r in active if tier_clean(r.get("Membership level")) not in PRIVATE_EXCLUDE_LEVELS
    ]
    private_people.sort(
        key=lambda r: (
            (
                clean(r.get("Organization"))
                or title_case_name(r.get("First name"), r.get("Last name"))
            ).lower(),
            clean(r.get("Last name")).lower(),
            clean(r.get("First name")).lower(),
        )
    )

    private_rows: list[str] = []
    for r in private_people:
        org = clean(r.get("Organization")) or title_case_name(
            r.get("First name"), r.get("Last name")
        )
        person = title_case_name(r.get("First name"), r.get("Last name"))
        title = clean(r.get("Position/Title"))
        person_line = f"{person} · {title}" if title else person
        tier = tier_clean(r.get("Membership level"))
        ind = industry_primary(r.get("Industry"))
        meta_ind = f"\n                <span>{esc(ind)}</span>" if ind else ""
        private_rows.append(
            f"""          <div class="member-row member-row--private" data-name="{esc(org + ' ' + person)}" data-tier="{esc(tier)}" data-industry="{esc(ind)}">
            <a class="member-row__stretch" href="member.html" aria-label="View {esc(org)} profile"></a>
            <div class="member-row__logo"><span>{esc(logo_label(org))}</span></div>
            <div class="member-row__main">
              <h2 class="member-row__name">{esc(org)}</h2>
              <div class="member-row__meta">
                <span class="{badge_class(tier)}">{esc(tier)}</span>{meta_ind}
              </div>
              <p class="member-row__person">{esc(person_line)}</p>
            </div>
            <div class="member-row__contact">
              {private_contact_html(r)}
            </div>
            {glimpse_html(r)}
          </div>"""
        )

    update_directory(
        DESIGN / "directory.html",
        sorted({tier_clean(r.get("Membership level")) for r in orgs}),
        sorted({industry_primary(r.get("Industry")) for r in orgs if industry_primary(r.get("Industry"))}),
        f"{len(orgs)} organizations",
        public_rows,
    )
    update_directory(
        DESIGN / "members-directory.html",
        sorted({tier_clean(r.get("Membership level")) for r in private_people}),
        sorted(
            {
                industry_primary(r.get("Industry"))
                for r in private_people
                if industry_primary(r.get("Industry"))
            }
        ),
        f"{len(private_people)} members",
        private_rows,
    )
    print(f"Public orgs: {len(orgs)}")
    print(f"Members network: {len(private_people)}")


if __name__ == "__main__":
    main()
