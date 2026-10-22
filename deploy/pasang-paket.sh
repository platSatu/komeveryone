#!/usr/bin/env bash
# kom_v02 — Pasang paket upload di VPS (TANPA git).
#
# Pemakaian (di VPS):
#   bash pasang-paket.sh /home/USER/kom-paket-20261012-1030.zip /var/www/komeveryone
#
# Urutan:
#   1. cek paket            2. BACKUP file lama + database
#   3. mode perawatan       4. salin file baru, hapus file yang dihapus
#   5. composer (jika perlu) 6. migrate (dengan konfirmasi) + seeder terjemahan
#   7. cache ulang + restart queue   8. aplikasi online lagi
set -euo pipefail

PAKET="${1:-}"
APP="${2:-}"
if [[ -z "$PAKET" || -z "$APP" ]]; then
  echo "Pemakaian: bash pasang-paket.sh <file.zip> <folder-aplikasi>"; exit 1
fi
[[ -f "$PAKET" ]] || { echo "Paket tidak ditemukan: $PAKET"; exit 1; }
[[ -f "$APP/artisan" ]] || { echo "Folder aplikasi Laravel tidak valid: $APP"; exit 1; }
command -v unzip >/dev/null || { echo "Pasang unzip dulu: sudo apt install unzip"; exit 1; }

WAKTU="$(date +%Y%m%d-%H%M%S)"
KERJA="$(mktemp -d)"
BACKUP="$HOME/backup-kom/$WAKTU"
mkdir -p "$BACKUP"
trap 'rm -rf "$KERJA"' EXIT

echo "==> 1. Membuka paket"
unzip -q "$PAKET" -d "$KERJA"
[[ -f "$KERJA/daftar-file.txt" ]] || { echo "Paket tidak berisi daftar-file.txt"; exit 1; }
cat "$KERJA/dasar.txt" 2>/dev/null || true
JUMLAH=$(grep -c . "$KERJA/daftar-file.txt" || true)
echo "    $JUMLAH file akan dipasang ke $APP"

PEMILIK="$(stat -c '%U:%G' "$APP/artisan")"
PEMILIK_STORAGE="$(stat -c '%U:%G' "$APP/storage")"
cd "$APP"

echo "==> 2. Backup file lama → $BACKUP/file-lama.tar.gz"
( cat "$KERJA/daftar-file.txt"; cat "$KERJA/hapus.txt" 2>/dev/null ) | sort -u | while read -r f; do
  if [[ -n "$f" && -e "$f" ]]; then echo "$f"; fi
done > "$BACKUP/ada.txt"
if [[ -s "$BACKUP/ada.txt" ]]; then
  tar -czf "$BACKUP/file-lama.tar.gz" -T "$BACKUP/ada.txt"
fi
cp "$KERJA/daftar-file.txt" "$BACKUP/"

echo "    Backup database (mysqldump dari .env)…"
DB_NAME=$(grep -E '^DB_DATABASE=' .env | cut -d= -f2- | tr -d '"' || true)
DB_USER=$(grep -E '^DB_USERNAME=' .env | cut -d= -f2- | tr -d '"' || true)
DB_PASS=$(grep -E '^DB_PASSWORD=' .env | cut -d= -f2- | tr -d '"' || true)
DB_HOST=$(grep -E '^DB_HOST=' .env | cut -d= -f2- | tr -d '"' || true)
if command -v mysqldump >/dev/null && [[ -n "$DB_NAME" ]]; then
  MYSQL_PWD="$DB_PASS" mysqldump -h "${DB_HOST:-127.0.0.1}" -u "$DB_USER" --single-transaction --quick "$DB_NAME" | gzip > "$BACKUP/database.sql.gz" \
    && echo "    OK: $BACKUP/database.sql.gz" \
    || echo "    PERINGATAN: backup database gagal — hentikan (Ctrl+C) jika belum punya backup lain."
else
  echo "    PERINGATAN: mysqldump tidak tersedia, database TIDAK dibackup."
fi

echo "==> 3. Mode perawatan"
php artisan down --retry=30 || true

echo "==> 4. Menyalin file baru"
while read -r f; do
  if [[ -z "$f" ]]; then continue; fi
  mkdir -p "$(dirname "$f")"
  cp -f "$KERJA/$f" "$f"
done < "$KERJA/daftar-file.txt"
if [[ -s "$KERJA/hapus.txt" ]]; then
  while read -r f; do
    if [[ -n "$f" && -f "$f" ]]; then rm -f "$f"; echo "    dihapus: $f"; fi
  done < "$KERJA/hapus.txt"
fi
chown -R "$PEMILIK" app bootstrap config database resources routes public 2>/dev/null || true

echo "==> 5. Composer"
if grep -qx 'composer.lock' "$KERJA/daftar-file.txt"; then
  composer install --no-dev --optimize-autoloader --no-interaction
else
  composer dump-autoload -o --no-interaction 2>/dev/null || echo "    (composer tidak ada, dilewati)"
fi

echo "==> 6. Database"
php artisan migrate:status | tail -n 15 || true
echo
php artisan migrate --pretend --force | tail -n 40 || true
read -r -p "Jalankan migrate sekarang? (y/N) " jawab
if [[ "$jawab" =~ ^[Yy]$ ]]; then
  php artisan migrate --force
else
  echo "    migrate DILEWATI."
fi
if grep -qx 'database/seeders/UiTranslationEnglishSeeder.php' "$KERJA/daftar-file.txt"; then
  php artisan db:seed --class=UiTranslationEnglishSeeder --force
fi

echo "==> 7. Cache ulang & restart queue"
php artisan optimize:clear
php artisan optimize
php artisan queue:restart || true

# file cache yang baru dibuat harus bisa ditulis web server lagi
chown -R "$PEMILIK_STORAGE" storage bootstrap/cache 2>/dev/null || true

echo "==> 8. Online kembali"
php artisan up

echo
echo "SELESAI. Backup ada di: $BACKUP"
echo "Kembalikan file lama jika perlu: cd $APP && tar -xzf $BACKUP/file-lama.tar.gz && php artisan optimize"
