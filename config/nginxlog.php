<?php

return [
    // Path file access.log nginx di server. WAJIB disesuaikan lewat .env,
    // contoh: NGINX_LOG_PATH=/var/log/nginx/komeveryone-access.log
    // (cek nama file aslinya lewat konfigurasi nginx di VPS, biasanya di
    // /etc/nginx/sites-available/<nama-site>, baris "access_log ...").
    'log_path' => env('NGINX_LOG_PATH', '/var/log/nginx/access.log'),

    // Ekstensi file yang dianggap "aset statis", bukan kunjungan halaman
    // sungguhan -- disaring pas ditampilkan di dashboard (tetap tersimpan
    // apa adanya di tabel nginx_access_logs).
    'asset_extensions' => [
        'css', 'js', 'mjs', 'map',
        'png', 'jpg', 'jpeg', 'gif', 'svg', 'ico', 'webp', 'avif',
        'woff', 'woff2', 'ttf', 'eot', 'otf',
        'json', 'xml', 'txt', 'pdf',
        'mp4', 'webm', 'mp3',
    ],
];
