{{-- kom_v02: gaya halaman Review Pengajar (Admin KOM) --}}
@once
<style>
    .kom-rv { padding-bottom: 32px; }
    .kom-rv-card { background:#fff; border:1px solid #e6edf7; border-radius:18px; padding:20px; box-shadow:0 12px 30px -24px rgba(30,60,120,.45); margin-bottom:20px; }
    .kom-rv-card h5 { font-weight:700; color:#1c2b43; margin-bottom:14px; }
    .kom-rv-stats { display:grid; grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)); gap:14px; margin-bottom:20px; }
    .kom-rv-stat { background:#fff; border:1px solid #e6edf7; border-radius:16px; padding:16px 18px; }
    .kom-rv-stat small { display:block; color:#7b879a; font-size:.75rem; text-transform:uppercase; letter-spacing:.4px; font-weight:600; }
    .kom-rv-stat strong { display:block; font-size:1.6rem; color:#1c2b43; margin-top:4px; }
    .kom-rv-stat.utama { background:linear-gradient(135deg,#2f6fed,#5a5cf0); border:0; }
    .kom-rv-stat.utama small, .kom-rv-stat.utama strong { color:#fff; }
    .kom-rv-filter { display:flex; flex-wrap:wrap; gap:12px; align-items:flex-end; }
    .kom-rv-filter > div { flex:1 1 170px; min-width:0; }
    .kom-rv-filter label { font-size:.72rem; font-weight:600; color:#5d6a7e; text-transform:uppercase; letter-spacing:.4px; margin-bottom:4px; }
    .kom-rv-filter .form-control { border-radius:12px; border-color:#e1e7f0; background:#f7f9fc; min-height:42px; }
    .kom-rv-filter .tombol { flex:0 0 auto; display:flex; gap:8px; }
    .kom-rv-btn { border:0; border-radius:12px; padding:10px 16px; font-weight:600; color:#fff; background:linear-gradient(135deg,#2f6fed,#5a5cf0); }
    .kom-rv-btn.abu { background:#eef2f8; color:#5d6a7e; }
    .kom-rv-btn:hover { filter:brightness(1.05); color:#fff; text-decoration:none; }
    .kom-rv-btn.abu:hover { color:#1c2b43; }
    .kom-rv-bar { display:flex; align-items:center; gap:12px; padding:6px 0; font-size:.86rem; color:#33415a; }
    .kom-rv-bar .nama { flex:0 0 210px; }
    .kom-rv-bar .jalur { flex:1; height:8px; border-radius:99px; background:#eef2f8; overflow:hidden; }
    .kom-rv-bar .jalur > div { height:100%; border-radius:99px; background:linear-gradient(90deg,#f0a500,#f7c948); }
    .kom-rv-bar .angka { flex:0 0 40px; text-align:right; font-weight:700; }
    .kom-rv-table { margin:0; }
    .kom-rv-table thead th { background:#f7f9fc; border:0; color:#7b879a; font-size:.72rem; font-weight:600; text-transform:uppercase; letter-spacing:.4px; padding:12px 14px; white-space:nowrap; }
    .kom-rv-table td { border-top:1px solid #f0f3f8; padding:12px 14px; vertical-align:middle; font-size:.88rem; color:#33415a; }
    .kom-rv-table tbody tr:hover { background:#fafcff; }
    .kom-rv-wrap { border:1px solid #e9eef6; border-radius:14px; overflow:auto; }
    .kom-rv-bintang { color:#f0a500; white-space:nowrap; }
    .kom-rv-bintang .kosong { color:#d9dfe8; }
    .kom-rv-chip { display:inline-block; font-size:.72rem; font-weight:600; padding:3px 9px; border-radius:99px; background:#eef2f8; color:#5d6a7e; }
    .kom-rv-chip.biru { background:#eaf2ff; color:#2f6fed; }
    .kom-rv-chip.hijau { background:#e8f8f1; color:#127a55; }
    .kom-rv-chip.kuning { background:#fff7e6; color:#8a5a00; }
    .kom-rv-chip.merah { background:#fdecec; color:#b42318; }
    .kom-rv-kosong { text-align:center; padding:40px 16px; color:#7b879a; }
    .kom-rv-kosong i { font-size:2rem; color:#b6c2d4; display:block; margin-bottom:8px; }
    @media (max-width: 576px) { .kom-rv-bar .nama { flex-basis:130px; } }
</style>
@endonce
