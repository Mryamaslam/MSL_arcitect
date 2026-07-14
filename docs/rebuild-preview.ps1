# Rebuild GitHub Pages snapshot from local WordPress homepage
# Run while XAMPP/Apache + WordPress is running.

$ErrorActionPreference = 'Stop'
$base = 'http://127.0.0.1/wordpress'
$theme = Split-Path $PSScriptRoot -Parent
$docs = $PSScriptRoot
$wp = Split-Path (Split-Path (Split-Path $theme -Parent) -Parent) -Parent
# theme = .../themes/blocksy → wp root is 3 levels up? blocksy->themes->wp-content->wordpress = 3 levels
$wp = (Resolve-Path (Join-Path $theme '..\..\..')).Path

Write-Host "WP root: $wp"
Write-Host "Fetching $base/ ..."

# Prefer running the existing captured logic from theme root
Set-Location $theme
& powershell -NoProfile -ExecutionPolicy Bypass -File (Join-Path $theme 'tools\snapshot-preview.ps1')
