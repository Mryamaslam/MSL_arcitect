# MSL Interior — WordPress + GitHub dual live

Same site, three ways:

| You want… | Use this |
|-----------|----------|
| **Edit** content, projects, images, phone | WordPress Admin → `http://localhost/wordpress/wp-admin` |
| **Live / share online (free preview)** | GitHub Pages → https://mryamaslam.github.io/MSL_arcitect/ |
| **Full live website (real WordPress)** | Host theme on any WP host (cPanel, Cloudways, etc.) |

## How it stays in sync

1. **WordPress is the source of truth**  
   Edit everything in WP (local or host).

2. **GitHub theme repo** stores theme code:  
   https://github.com/Mryamaslam/MSL_arcitect

3. **GitHub Pages** shows a static **snapshot** of your WP site  
   (homepage + every project page, real images).  
   Clicking a project opens the detail page (not 404).

### After you edit in WordPress — refresh GitHub preview

XAMPP must be running:

```powershell
cd c:\xampp\htdocs\wordpress\wp-content\themes\blocksy
python tools\snapshot_preview.py
git add docs
git commit -m "Refresh live preview from WordPress"
git push origin main
```

Wait 1–2 minutes, then open:  
https://mryamaslam.github.io/MSL_arcitect/

## Local WordPress (edit + full features)

1. Start XAMPP (Apache + MySQL)
2. Open http://localhost/wordpress/
3. Theme: Blocksy (this folder)
4. Projects CPT, media library, forms all work here

## Important

- GitHub Pages **cannot run PHP**. It is a design/share preview, not the WP admin.
- For a **real public WordPress site**, upload this theme + your WP database/media to hosting.
- Same theme files work in both places; use the snapshot script whenever you want GitHub Pages updated.
