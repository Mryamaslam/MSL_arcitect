# Rebuild GitHub Pages from live WordPress (homepage + all projects).
# Requires: XAMPP running, Python installed.

$ErrorActionPreference = 'Stop'
$theme = Split-Path $PSScriptRoot -Parent
Set-Location $theme
python (Join-Path $theme 'tools\snapshot_preview.py')
Write-Host ""
Write-Host "Snapshot ready in docs/. Next:"
Write-Host "  git add docs"
Write-Host "  git commit -m `"Refresh live preview from WordPress`""
Write-Host "  git push origin main"
