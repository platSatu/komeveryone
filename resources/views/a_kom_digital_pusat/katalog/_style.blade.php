{{-- kom_v02 — gaya bersama halaman Bahasa Belajar, Pilih Kelas, dan Kode Buku --}}
<style>
    .kom-hero { display:flex; gap:16px; align-items:center; background:linear-gradient(135deg,#1e3c72,#2a5298); color:#fff; border-radius:16px; padding:22px 24px; }
    .kom-hero h3 { color:#fff; font-weight:700; font-size:1.35rem; }
    .kom-hero p { opacity:.9; }
    .kom-hero-icon { width:56px; height:56px; flex:0 0 56px; border-radius:14px; background:rgba(255,255,255,.15); display:flex; align-items:center; justify-content:center; font-size:26px; }
    .kom-note { background:#f1f5fb; border-left:4px solid #2a5298; border-radius:10px; padding:12px 16px; color:#334; }
    .kom-note-warning { background:#fff8e6; border-color:#f0ad4e; }
    .kom-note-success { background:#ecf9f0; border-color:#28a745; }
    .kom-note-danger  { background:#fdeeee; border-color:#dc3545; }
    .kom-btn { border-radius:10px; padding:10px 20px; font-weight:600; }

    .kom-lang-card { display:flex; align-items:center; gap:14px; width:100%; cursor:pointer; background:#fff; border:2px solid #e3e8f0; border-radius:14px; padding:16px 18px; transition:.2s; margin:0; }
    .kom-lang-card:hover { border-color:#2a5298; box-shadow:0 6px 18px rgba(42,82,152,.12); }
    .kom-lang-card.is-selected { border-color:#2a5298; background:#f4f7fd; }
    .kom-lang-card.is-disabled { opacity:.45; cursor:not-allowed; }
    .kom-lang-flag { font-size:30px; line-height:1; }
    .kom-lang-text { display:flex; flex-direction:column; flex:1; }
    .kom-lang-text small { color:#7a8699; }
    .kom-lang-check { color:#2a5298; font-size:22px; opacity:0; transition:.2s; }
    .kom-lang-card.is-selected .kom-lang-check { opacity:1; }

    .kom-class-card { background:#fff; border:1px solid #e3e8f0; border-radius:16px; padding:20px; height:100%; display:flex; flex-direction:column; transition:.2s; }
    .kom-class-card:hover { box-shadow:0 8px 22px rgba(30,60,114,.10); }
    .kom-class-card.is-muted { background:#fafbfc; }
    .kom-class-cover { height:120px; border-radius:12px; margin:-4px -4px 16px; background:linear-gradient(135deg,#2a5298,#4f7cd6); display:flex; align-items:center; justify-content:center; color:#fff; font-size:30px; font-weight:800; letter-spacing:1px; background-size:cover; background-position:center; }
    .kom-class-card.is-muted .kom-class-cover { background:linear-gradient(135deg,#9aa6b8,#c3cbd8); }
    .kom-class-card h5 { font-weight:700; margin-bottom:6px; }
    .kom-class-meta { color:#7a8699; font-size:.875rem; margin-bottom:12px; }
    .kom-badge { display:inline-block; border-radius:999px; padding:4px 12px; font-size:.78rem; font-weight:600; }
    .kom-badge-berjalan { background:#e7efff; color:#2a5298; }
    .kom-badge-selesai  { background:#e6f6ec; color:#1e7e34; }
    .kom-badge-terkunci { background:#fff3e0; color:#b26a00; }
    .kom-badge-segera   { background:#eef0f3; color:#6c757d; }
    .kom-badge-tersedia { background:#e7f7f6; color:#138496; }
    .kom-class-action { margin-top:auto; padding-top:14px; }
    .kom-class-action .btn { width:100%; }

    .kom-input-big { font-size:1.15rem; letter-spacing:1px; padding:12px 16px; border-radius:12px; border:2px solid #e3e8f0; }
    .kom-input-big:focus { border-color:#2a5298; box-shadow:none; }
    .gap-2 { gap:.5rem; } .g-3 { row-gap:1rem; }

    /* ---- kom_v02: tampilan ramah (Beranda & umum) ---- */
    .kom-page .text-muted, .kom-text { color:#5b6576 !important; }
    .kom-card { background:#fff; border:1px solid #e6ebf2; border-radius:18px; padding:24px; box-shadow:0 2px 10px rgba(30,60,114,.04); }
    .kom-card-success { background:linear-gradient(135deg,#f0fbf4,#fff); border-color:#cdeed8; }
    .kom-card-label { display:inline-block; font-size:.8rem; font-weight:600; color:#2a5298; background:#eef3fc; border-radius:999px; padding:4px 12px; margin-bottom:12px; }
    .kom-card-success .kom-card-label { color:#1e7e34; background:#e2f6e9; }
    .kom-card-title { font-weight:700; color:#1f2a44; margin-bottom:14px; }
    .kom-btn-lg { padding:12px 26px; font-size:1rem; }
    .kom-progress { height:10px; background:#edf1f7; border-radius:999px; overflow:hidden; }
    .kom-progress > div { height:100%; background:linear-gradient(90deg,#2a5298,#4f7cd6); border-radius:999px; transition:width .6s; }
    .kom-next { display:flex; gap:14px; align-items:center; background:#f6f8fc; border-radius:12px; padding:12px 16px; }
    .kom-next > i { color:#2a5298; font-size:18px; }
    .kom-next small { display:block; color:#7a8699; font-size:.75rem; text-transform:uppercase; letter-spacing:.5px; }
    .kom-next strong { display:block; color:#1f2a44; }
    .kom-next span { color:#5b6576; font-size:.85rem; }
    .kom-quick a { display:flex; align-items:center; gap:12px; padding:12px 6px; border-bottom:1px solid #eef1f6; color:#1f2a44; text-decoration:none; transition:.15s; }
    .kom-quick a:last-child { border-bottom:0; }
    .kom-quick a:hover { color:#2a5298; padding-left:10px; }
    .kom-quick a > i:first-child { width:34px; height:34px; border-radius:10px; background:#eef3fc; color:#2a5298; display:inline-flex; align-items:center; justify-content:center; }
    .kom-quick a span { flex:1; font-weight:500; }
    .kom-quick a > i:last-child { color:#b3bccb; font-size:.8rem; }
    .kom-section-title { font-weight:700; color:#1f2a44; margin:8px 0 14px; }
    .kom-step-card { background:#fff; border:1px solid #e6ebf2; border-radius:16px; padding:20px; height:100%; }
    .kom-step-card h6 { font-weight:700; color:#1f2a44; margin:14px 0 8px; }
    .kom-step-card p { color:#5b6576; font-size:.9rem; margin:0; line-height:1.55; }
    .kom-step-icon { position:relative; width:46px; height:46px; border-radius:14px; background:#eef3fc; color:#2a5298; display:flex; align-items:center; justify-content:center; font-size:20px; }
    .kom-step-icon span { position:absolute; top:-6px; right:-6px; width:20px; height:20px; border-radius:50%; background:#2a5298; color:#fff; font-size:.7rem; font-weight:700; display:flex; align-items:center; justify-content:center; }
    .kom-lang-code { display:inline-flex; align-items:center; justify-content:center; min-width:44px; height:44px; border-radius:12px; background:#eef3fc; color:#2a5298; font-weight:800; letter-spacing:.5px; }
    .kom-lang-card.is-selected .kom-lang-code { background:#2a5298; color:#fff; }
    .kom-pill { display:inline-block; border-radius:999px; padding:2px 10px; font-size:.72rem; font-weight:700; background:#eef3fc; color:#2a5298; letter-spacing:.5px; }
    .kom-class-meta, .kom-lang-text small { color:#6b7585; }
    .kom-page { padding-bottom:40px; }
    .kom-class-card h5 { flex:1; margin-right:10px; }
    .kom-class-card .kom-badge { white-space:nowrap; }
</style>
