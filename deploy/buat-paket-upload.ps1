<#
  kom_v02 — Buat paket upload untuk VPS (TANPA git di VPS).

  Dijalankan di komputer lokal (Windows), di folder proyek:
      powershell -ExecutionPolicy Bypass -File deploy\buat-paket-upload.ps1 -Dasar main

  -Dasar  = branch / commit yang SAMA dengan kode yang sekarang ada di VPS
            (contoh: main, origin/main, atau hash commit terakhir yang di-upload).

  Hasil (di folder deploy\paket\):
      kom-paket-<tanggal>.zip   → berisi file yang berubah + daftar-file.txt + hapus.txt
  Upload zip itu dan deploy\pasang-paket.sh ke VPS (WinSCP / FileZilla / scp).

  Yang TIDAK pernah ikut: .env, vendor, node_modules, storage, public/storage,
  folder deploy, dan docs/RENCANA-MULTI-BAHASA.md.
#>
param(
    [Parameter(Mandatory = $true)][string]$Dasar
)

Set-Location (Split-Path -Parent $PSScriptRoot)

git rev-parse --verify --quiet $Dasar | Out-Null
if ($LASTEXITCODE -ne 0) { Write-Host "Branch/commit '$Dasar' tidak ditemukan." -ForegroundColor Red; exit 1 }
$ErrorActionPreference = 'Stop'

$kecuali = @('^\.env', '^vendor/', '^node_modules/', '^storage/', '^public/storage', '^deploy/',
             '^docs/RENCANA-MULTI-BAHASA\.md$', '^bootstrap/cache/', '^\.git')

function Saring($daftar) {
    $daftar | Where-Object { $_ -and $_.Trim() -ne '' } | ForEach-Object { $_.Trim() } |
        Where-Object { $f = $_; -not ($kecuali | Where-Object { $f -match $_ }) } |
        Sort-Object -Unique
}

$berubah = git -c core.quotepath=off diff --name-only --diff-filter=ACMR $Dasar
$baru    = git -c core.quotepath=off ls-files --others --exclude-standard
$dihapus = git -c core.quotepath=off diff --name-only --diff-filter=D $Dasar

$file  = Saring (@($berubah) + @($baru)) | Where-Object { Test-Path -LiteralPath $_ -PathType Leaf }
$hapus = Saring @($dihapus)

if (-not $file -and -not $hapus) { Write-Host 'Tidak ada perubahan dibanding' $Dasar -ForegroundColor Yellow; exit 0 }

$folderPaket = Join-Path 'deploy' 'paket'
New-Item -ItemType Directory -Force -Path $folderPaket | Out-Null
$nama = 'kom-paket-' + (Get-Date -Format 'yyyyMMdd-HHmm') + '.zip'
$zipPath = Join-Path (Resolve-Path $folderPaket) $nama
if (Test-Path $zipPath) { Remove-Item $zipPath }

Add-Type -AssemblyName System.IO.Compression
Add-Type -AssemblyName System.IO.Compression.FileSystem
$zip = [System.IO.Compression.ZipFile]::Open($zipPath, 'Create')
try {
    foreach ($f in $file) {
        $entri = $f -replace '\\', '/'
        [System.IO.Compression.ZipFileExtensions]::CreateEntryFromFile($zip, (Resolve-Path -LiteralPath $f).Path, $entri, 'Optimal') | Out-Null
    }
    $tulis = {
        param($namaEntri, $isi)
        $e = $zip.CreateEntry($namaEntri)
        $w = New-Object System.IO.StreamWriter($e.Open(), (New-Object System.Text.UTF8Encoding($false)))
        $w.NewLine = "`n"
        foreach ($baris in $isi) { $w.WriteLine(($baris -replace '\\', '/')) }
        $w.Close()
    }
    & $tulis 'daftar-file.txt' $file
    & $tulis 'hapus.txt' $hapus
    & $tulis 'dasar.txt' @("dasar: $Dasar", "commit lokal: $(git rev-parse HEAD)", "dibuat: $(Get-Date -Format 'yyyy-MM-dd HH:mm')")
} finally {
    $zip.Dispose()
}

Write-Host ''
Write-Host "Paket dibuat: $zipPath" -ForegroundColor Green
Write-Host ("  {0} file dikirim, {1} file dihapus di VPS" -f @($file).Count, @($hapus).Count)
Write-Host ''
Write-Host 'Daftar file:'
$file | ForEach-Object { Write-Host "  + $_" }
$hapus | ForEach-Object { Write-Host "  - $_" -ForegroundColor DarkYellow }
