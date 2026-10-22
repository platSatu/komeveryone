{{--
    kom_v02: gaya bersama halaman rangkaian kelas tutorial (jadwal, pertanyaan, persembahan)
    supaya tampilannya seragam: modern & minimalis.
    Pemakaian: @include('a_kom_digital_pusat.partials._kom_ui')
--}}
@once
<style>
    .kom-ui-card {
        background: linear-gradient(180deg, #f2f7ff 0%, #ffffff 200px);
        border: 1px solid #e6edf7; border-radius: 24px;
        box-shadow: 0 18px 40px -22px rgba(30, 60, 120, .35);
        padding: 32px;
    }
    .kom-ui-head { text-align:center; margin-bottom: 24px; }
    .kom-ui-icon {
        width:60px; height:60px; margin:0 auto 12px; border-radius:18px;
        display:flex; align-items:center; justify-content:center;
        background: linear-gradient(135deg, #2f6fed, #6a5cff); color:#fff; font-size:1.5rem;
        box-shadow: 0 10px 22px -10px rgba(47,111,237,.7);
    }
    .kom-ui-head h3 { margin:0 0 4px; font-weight:700; color:#1c2b43; }
    .kom-ui-head p { margin:0; color:#7b879a; font-size:.9rem; }

    .kom-ui-alert { border:0; border-radius:16px; padding:14px 18px; font-size:.92rem; }
    .kom-ui-alert.alert-success { background:#e8f8f1; color:#127a55; }
    .kom-ui-alert.alert-danger { background:#fdecec; color:#b42318; }
    .kom-ui-alert.alert-info { background:#eaf2ff; color:#2a5bc4; }
    .kom-ui-alert.alert-warning { background:#fff7e6; color:#8a5a00; }
    .kom-ui-alert.alert-primary { background:#eef0ff; color:#3b3fb6; }

    .kom-ui-input, .kom-ui-card .form-control.kom-ui-input {
        border:1px solid #e1e7f0; border-radius:14px; background:#f7f9fc; color:#1c2b43;
        padding:12px 16px; font-size:.95rem; box-shadow:none; transition: border-color .15s, box-shadow .15s, background .15s;
    }
    select.kom-ui-input { height:auto; min-height:48px; }
    .kom-ui-input:focus { background:#fff; border-color:#2f6fed; box-shadow:0 0 0 4px rgba(47,111,237,.12); outline:0; }
    .kom-ui-label { font-size:.8rem; font-weight:600; color:#5d6a7e; text-transform:uppercase; letter-spacing:.4px; margin-bottom:6px; }

    .kom-btn-primary {
        display:inline-flex; align-items:center; justify-content:center; gap:8px;
        border:0; border-radius:14px; padding:13px 18px; font-size:.98rem; font-weight:600; color:#fff !important;
        background: linear-gradient(135deg, #2f6fed, #5a5cf0);
        box-shadow: 0 12px 24px -12px rgba(47,111,237,.8);
        transition: transform .15s, box-shadow .15s, filter .15s;
    }
    .kom-btn-primary:hover { transform: translateY(-1px); filter: brightness(1.05); box-shadow: 0 16px 28px -12px rgba(47,111,237,.9); }
    .kom-btn-primary:active { transform:none; }
    .kom-btn-primary:disabled { opacity:.7; transform:none; cursor:not-allowed; }
    .kom-btn-primary.kecil { padding:8px 14px; font-size:.85rem; border-radius:10px; }
    .kom-btn-penuh {
        display:inline-flex; align-items:center; justify-content:center; gap:8px;
        border:0; border-radius:14px; padding:13px 18px; font-weight:600; background:#eef1f6; color:#9aa5b5; cursor:not-allowed;
    }
    .kom-btn-penuh.kecil { padding:8px 14px; font-size:.85rem; border-radius:10px; }

    .kom-link-cancel { background:none; border:0; color:#8a96a8; font-size:.85rem; padding:6px 10px; border-radius:10px; transition: color .15s, background .15s; }
    .kom-link-cancel:hover { color:#b42318; background:#fdecec; }

    @media (max-width: 576px) {
        .kom-ui-card { padding:24px 18px; border-radius:20px; }
    }
</style>
@endonce
