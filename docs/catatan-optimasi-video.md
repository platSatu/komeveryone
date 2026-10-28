# Catatan optimasi video KOM (28 Oktober 2026) — BELUM DIKERJAKAN

Tujuan: video tetap lancar saat ±40 peserta menonton bersamaan.

## Kondisi sekarang (dicek di VPS dengan ffprobe)
- 30 video di `public/videos`, total 17 GB (rata-rata ±570 MB per video).
- Semua 1920×1080, H.264, bitrate ±3,3 Mbps → 40 penonton ≈ 130 Mbps keluar dari server.
- **Belum faststart**: atom `moov` ada di AKHIR file (`mdat` lebih dulu) → browser harus
  meminta bagian akhir file dulu sebelum video bisa diputar / di-seek.
- nginx `komeveryone.gbigatsu.id` belum punya pengaturan khusus video (hanya `location /`).
- Kode sudah ringan: simpan progres video tiap 30 detik (kom_v02).
- ffmpeg/ffprobe sudah terpasang di VPS.

## Rencana bertahap
1. **Faststart semua video** (tanpa encode ulang, kualitas tidak berubah, beberapa detik per video):
   `ffmpeg -i asli.mp4 -c copy -movflags +faststart baru.mp4`
   Tulis ke folder baru dulu, cek, baru tukar. Video asli disimpan sebagai cadangan.
   Butuh ±17 GB disk sementara.
2. **nginx khusus `/videos/`**: `sendfile on`, `tcp_nopush on`, `Cache-Control` panjang,
   `access_log off`, (opsional) modul `mp4` untuk seek.
3. **(Opsional) versi 720p ±1,3 Mbps** → beban ±50 Mbps untuk 40 penonton, total ±7 GB.
   Kualitas sedikit turun; proses beberapa jam. Putuskan setelah tahap 1–2.
4. Tanyakan ke penyedia server: kecepatan **upload/keluar** server (menentukan kapasitas penonton).
   Kalau penonton bertambah ratusan: pertimbangkan CDN / layanan video (mis. Cloudflare, Bunny Stream).
