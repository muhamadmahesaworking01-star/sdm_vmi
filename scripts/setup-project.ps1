param(
  [switch]$SkipMigrate,
  [switch]$SkipDbSeed,
  [string]$DatabaseName = 'cthdatabase'
)

$ErrorActionPreference = 'Stop'
$scriptPath = Split-Path -Parent $MyInvocation.MyCommand.Path
$projectRoot = Resolve-Path (Join-Path $scriptPath '..')
Set-Location $projectRoot

function Write-Step($message) {
  Write-Host "`n==> $message" -ForegroundColor Cyan
}

Write-Step 'Checking PHP and Composer'
if (-not (Get-Command php -ErrorAction SilentlyContinue)) { throw 'PHP not found in PATH.' }
if (-not (Get-Command composer -ErrorAction SilentlyContinue)) { throw 'Composer not found in PATH.' }

Write-Step 'Installing PHP dependencies'
composer install --no-interaction

Write-Step 'Installing Node dependencies'
if (Test-Path 'package.json') {
  npm install
} else {
  Write-Warning 'package.json not found; skipping npm install.'
}

if (-not (Test-Path '.env')) {
  Write-Step 'Creating .env from .env.example'
  if (Test-Path '.env.example') {
    Copy-Item '.env.example' '.env'
  } else {
    throw '.env.example not found.'
  }
}

Write-Step 'Generating app key'
php artisan key:generate --force

if (-not $SkipMigrate) {
  Write-Step "Running migrations for database $DatabaseName"
  php artisan migrate --force

  if (-not $SkipDbSeed) {
    Write-Step 'Running database seeders'
    php artisan db:seed --force
  }
}

Write-Step 'Linking storage'
php artisan storage:link

Write-Step 'Setup completed. Run php artisan serve and npm run dev to start development.'
