#!/usr/bin/env python3
"""Export WordPress homepage + project pages into docs/ for GitHub Pages."""
from __future__ import annotations

import re
import urllib.request
from pathlib import Path

BASE = "http://127.0.0.1/wordpress"
THEME = Path(__file__).resolve().parents[1]
DOCS = THEME / "docs"
WP = THEME.parents[2]  # .../wordpress
MEDIA = DOCS / "media"
SITE = "/MSL_arcitect/"

UA = {"User-Agent": "MSL-Snapshot/1.0"}


def fetch(url: str) -> str:
    req = urllib.request.Request(url, headers=UA)
    with urllib.request.urlopen(req, timeout=60) as r:
        return r.read().decode("utf-8", errors="replace")


def download(url: str, dest: Path) -> None:
    dest.parent.mkdir(parents=True, exist_ok=True)
    if dest.exists() and dest.stat().st_size > 0:
        return
    req = urllib.request.Request(url, headers=UA)
    with urllib.request.urlopen(req, timeout=120) as r:
        dest.write_bytes(r.read())


def collect_uploads(html: str) -> list[str]:
    return sorted(
        set(
            re.findall(
                r"https?://(?:127\.0\.0\.1|localhost)/wordpress/wp-content/uploads/[^\"'\s)]+",
                html,
            )
        )
    )


def rewrite(html: str, url_map: dict[str, str]) -> str:
    out = html
    for old, new in sorted(url_map.items(), key=lambda kv: -len(kv[0])):
        out = out.replace(old, new)
    out = re.sub(r"https?://127\.0\.0\.1/wordpress/", "", out)
    out = re.sub(r"https?://localhost/wordpress/", "", out)
    out = re.sub(
        r"<script[^>]+elementor/assets/js/[^>]+></script>\s*", "", out, flags=re.I
    )
    out = re.sub(
        r"<script[^>]+jquery-numerator[^>]+></script>\s*", "", out, flags=re.I
    )
    out = re.sub(
        r"<script[^>]+jquery/ui/core[^>]+></script>\s*", "", out, flags=re.I
    )
    out = re.sub(
        r"<script[^>]+themes/blocksy/static/bundle/main\.js[^>]+></script>\s*",
        "",
        out,
        flags=re.I,
    )
    banner = f"""<base href="{SITE}">
<style id="msl-preview-banner">
#wpadminbar{{display:none!important}}
html{{margin-top:0!important}}
.msl-gh-banner{{position:sticky;top:0;z-index:99999;background:#16181c;color:#fff;font:600 12px/1.4 Manrope,Segoe UI,sans-serif;padding:8px 14px;display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap}}
.msl-gh-banner a{{color:#d4b788}}
</style>
"""
    out = out.replace("<head>", "<head>\n" + banner, 1)
    if "msl-gh-banner" not in out:
        out = re.sub(
            r"<body([^>]*)>",
            r'<body\1>\n<div class="msl-gh-banner"><span>MSL Interior — WordPress snapshot</span><span><a href="https://github.com/Mryamaslam/MSL_arcitect">GitHub</a></span></div>',
            out,
            count=1,
        )
    # Project detail links
    out = re.sub(
        r'href="(?:\./)?projects/([^"/]+)/?"',
        rf'href="{SITE}projects/\1/"',
        out,
    )
    out = out.replace('href="/#projects"', f'href="{SITE}#projects"')
    out = out.replace("href='/#projects'", f"href='{SITE}#projects'")
    out = out.replace('href="#projects"', f'href="{SITE}#projects"')
    # Logo / home
    out = re.sub(r'href="/"(\s)', rf'href="{SITE}"\1', out)
    return out


def copy_static() -> None:
    files = [
        "wp-content/themes/blocksy/static/bundle/main.min.css",
        "wp-content/themes/blocksy/static/bundle/elementor-frontend.min.css",
        "wp-content/themes/blocksy/static/bundle/wpforms.min.css",
        "wp-content/themes/blocksy/static/bundle/no-scripts.min.css",
        "wp-content/themes/blocksy/inc/architecture-portfolio/portfolio.css",
        "wp-content/themes/blocksy/inc/architecture-portfolio/portfolio.js",
        "wp-content/plugins/elementor/assets/css/frontend.min.css",
        "wp-content/plugins/elementor/assets/css/widget-heading.min.css",
        "wp-content/plugins/elementor/assets/css/widget-image.min.css",
        "wp-content/plugins/elementor/assets/css/widget-counter.min.css",
        "wp-content/plugins/elementor/assets/css/widget-icon-box.min.css",
        "wp-content/plugins/elementor/assets/css/widget-divider.min.css",
        "wp-includes/js/jquery/jquery.min.js",
        "wp-includes/js/jquery/jquery-migrate.min.js",
    ]
    for rel in files:
        src = WP / rel.replace("/", "\\")
        dst = DOCS / rel.replace("/", "\\")
        if src.exists():
            dst.parent.mkdir(parents=True, exist_ok=True)
            dst.write_bytes(src.read_bytes())


def main() -> None:
    DOCS.mkdir(parents=True, exist_ok=True)
    MEDIA.mkdir(parents=True, exist_ok=True)

    print("Fetch home...")
    home = fetch(f"{BASE}/")
    slugs = sorted(set(re.findall(r"/projects/([^/\"'\s]+)/?", home)))
    print(f"Projects: {len(slugs)}")

    pages = {"__home__": home}
    for slug in slugs:
        url = f"{BASE}/projects/{slug}/"
        print(f"Fetch {slug}")
        try:
            pages[slug] = fetch(url)
        except Exception as e:
            print(f"SKIP {slug}: {e}")

    url_map: dict[str, str] = {}
    for html in pages.values():
        for u in collect_uploads(html):
            m = re.search(r"/wp-content/uploads/(.+)$", u)
            if not m:
                continue
            rel = re.sub(r"\?.*$", "", m.group(1))
            dest = MEDIA / rel.replace("/", "\\")
            try:
                download(u, dest)
            except Exception as e:
                print(f"FAIL {u}: {e}")
            url_map[u] = "media/" + rel.replace("\\", "/")

    print(f"Media mapped: {len(url_map)}")
    copy_static()

    (DOCS / "index.html").write_text(
        rewrite(pages["__home__"], url_map), encoding="utf-8", newline="\n"
    )
    print("Wrote docs/index.html")

    for slug in slugs:
        if slug not in pages:
            continue
        d = DOCS / "projects" / slug
        d.mkdir(parents=True, exist_ok=True)
        (d / "index.html").write_text(
            rewrite(pages[slug], url_map), encoding="utf-8", newline="\n"
        )
        print(f"Wrote projects/{slug}/")

    print("DONE")


if __name__ == "__main__":
    main()
