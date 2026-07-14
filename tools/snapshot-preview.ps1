# Snapshot live WordPress homepage into docs/ for GitHub Pages (as-is preview).
param(
  [string]$BaseUrl = 'http://127.0.0.1/wordpress'
)

$ErrorActionPreference = 'Stop'
$theme = Split-Path $PSScriptRoot -Parent
if ((Split-Path $theme -Leaf) -eq 'tools') { $theme = Split-Path $theme -Parent }
$docs = Join-Path $theme 'docs'
$wp = (Resolve-Path (Join-Path $theme '..\..\..')).Path
$mediaRoot = Join-Path $docs 'media'

Write-Host "Theme: $theme"
Write-Host "Docs:  $docs"
Write-Host "WP:    $wp"

New-Item -ItemType Directory -Force -Path $docs | Out-Null
if (Test-Path $mediaRoot) { Remove-Item $mediaRoot -Recurse -Force }
New-Item -ItemType Directory -Force -Path $mediaRoot | Out-Null

$html = (Invoke-WebRequest -Uri "$BaseUrl/" -UseBasicParsing).Content

$uploadMatches = [regex]::Matches($html, [regex]::Escape($BaseUrl) + '/wp-content/uploads/[^"''\s\)]+')
# also absolute variants
$uploadMatches2 = [regex]::Matches($html, 'https?://(?:127\.0\.0\.1|localhost)/wordpress/wp-content/uploads/[^"''\s\)]+')
$urls = @($uploadMatches + $uploadMatches2 | ForEach-Object { $_.Value }) | Sort-Object -Unique
Write-Host "Upload URLs: $($urls.Count)"

$map = @{}
foreach ($url in $urls) {
  if ($url -match '/wp-content/uploads/(.+)$') {
    $rel = ($Matches[1] -replace '\?.*$','')
    $out = Join-Path $mediaRoot ($rel -replace '/','\')
    $dir = Split-Path $out -Parent
    if (!(Test-Path $dir)) { New-Item -ItemType Directory -Force -Path $dir | Out-Null }
    if (!(Test-Path $out)) {
      try { Invoke-WebRequest -Uri $url -OutFile $out -UseBasicParsing | Out-Null } catch { Write-Host "FAIL $url" }
    }
    $map[$url] = ('media/' + ($rel -replace '\\','/'))
  }
}

$files = @(
  'wp-content\themes\blocksy\static\bundle\main.min.css',
  'wp-content\themes\blocksy\static\bundle\elementor-frontend.min.css',
  'wp-content\themes\blocksy\static\bundle\wpforms.min.css',
  'wp-content\themes\blocksy\static\bundle\no-scripts.min.css',
  'wp-content\themes\blocksy\inc\architecture-portfolio\portfolio.css',
  'wp-content\themes\blocksy\inc\architecture-portfolio\portfolio.js',
  'wp-content\plugins\elementor\assets\css\frontend.min.css',
  'wp-content\plugins\elementor\assets\css\widget-heading.min.css',
  'wp-content\plugins\elementor\assets\css\widget-image.min.css',
  'wp-content\plugins\elementor\assets\css\widget-counter.min.css',
  'wp-content\plugins\elementor\assets\css\widget-icon-box.min.css',
  'wp-content\plugins\elementor\assets\css\widget-divider.min.css',
  'wp-includes\js\jquery\jquery.min.js',
  'wp-includes\js\jquery\jquery-migrate.min.js'
)
foreach ($f in $files) {
  $src = Join-Path $wp $f
  $dst = Join-Path $docs $f
  if (Test-Path $src) {
    New-Item -ItemType Directory -Force -Path (Split-Path $dst -Parent) | Out-Null
    Copy-Item $src $dst -Force
  }
}

$outHtml = $html
foreach ($k in ($map.Keys | Sort-Object { $_.Length } -Descending)) {
  $outHtml = $outHtml.Replace($k, $map[$k])
}
$outHtml = $outHtml -replace 'https?://127\.0\.0\.1/wordpress/', ''
$outHtml = $outHtml -replace 'https?://localhost/wordpress/', ''
$outHtml = $outHtml -replace "href='/'", "href='#'"
$outHtml = [regex]::Replace($outHtml, "(?s)<script[^>]+elementor/assets/js/[^>]+></script>\s*", '')
$outHtml = [regex]::Replace($outHtml, "(?s)<script[^>]+jquery-numerator[^>]+></script>\s*", '')
$outHtml = [regex]::Replace($outHtml, "(?s)<script[^>]+jquery/ui/core[^>]+></script>\s*", '')
$outHtml = [regex]::Replace($outHtml, "(?s)<script[^>]+themes/blocksy/static/bundle/main\.js[^>]+></script>\s*", '')

$banner = @"
<base href="/MSL_arcitect/">
<style id="msl-preview-banner">
#wpadminbar{display:none!important}
html{margin-top:0!important}
.msl-gh-banner{position:sticky;top:0;z-index:99999;background:#16181c;color:#fff;font:600 12px/1.4 Manrope,Segoe UI,sans-serif;padding:8px 14px;display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap}
.msl-gh-banner a{color:#d4b788}
</style>
"@
$outHtml = $outHtml -replace '<head>', "<head>`n$banner"
$outHtml = $outHtml -replace '<body([^>]*)>', '<body$1>`n<div class="msl-gh-banner"><span>MSL Interior — live WordPress snapshot</span><span><a href="https://github.com/Mryamaslam/MSL_arcitect">GitHub</a> · Edit in WordPress, then rebuild preview</span></div>'

Set-Content -Path (Join-Path $docs 'index.html') -Value $outHtml -Encoding UTF8
Write-Host "Wrote docs/index.html — media files: $((Get-ChildItem $mediaRoot -Recurse -File).Count)"
