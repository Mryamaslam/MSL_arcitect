# MSL Interior (Blocksy theme)

WordPress theme for **MSL Interiors & Architects**.

## Three ways to use

| Mode | URL / path | What it does |
|------|------------|--------------|
| **Edit (WordPress)** | `http://localhost/wordpress/` + WP Admin | Change content, projects, images, phone numbers |
| **Theme code (GitHub)** | [github.com/Mryamaslam/MSL_arcitect](https://github.com/Mryamaslam/MSL_arcitect) | Version theme files; push updates |
| **Live preview (GitHub Pages)** | [mryamaslam.github.io/MSL_arcitect](https://mryamaslam.github.io/MSL_arcitect/) | Static **as-is** snapshot of your WordPress homepage (real projects + images) |

## Refresh GitHub preview after WordPress edits

With XAMPP running:

```powershell
cd wp-content\themes\blocksy
python tools\snapshot_preview.py
git add docs
git commit -m "Refresh live preview snapshot"
git push origin main
```

This exports the homepage **and every project detail page** (so project cards open instead of 404).

Pages rebuilds in about 1–2 minutes.

## Notes

- GitHub Pages cannot run PHP/WordPress — the preview is a static export in `docs/`.
- Full CMS features (project CPT, media library, forms) stay on WordPress (local or hosting).
- Same theme code works in both places: edit in WP, snapshot to GitHub when you want the public preview updated.
