@extends('layouts.KomDigital')

@section('content')
    <div class="container-fluid mb-3">
        <div class="row">
            <div class="col-12">

                {{-- Header Section --}}
                <div class="page-header mb-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="page-title">
                                <i class="fas fa-chalkboard-teacher me-2 text-primary"></i>&nbsp;&nbsp;
                                Kelas Tutorial
                            </h2>
                            <p class="page-subtitle">Jadwal Kelas Tutorial</p>
                        </div>

                    </div>
                </div>

                {{-- Stats Cards --}}
                <div class="stats-grid mb-4">
                    <div class="stat-card stat-primary">
                        <div class="stat-icon">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number">{{ $kelasTutorial->count() }}</div>
                            <div class="stat-label">Total Kelas</div>
                        </div>
                    </div>
                    <div class="stat-card stat-success">
                        <div class="stat-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number">{{ $kelasTutorial->where('status', 'aktif')->count() }}</div>
                            <div class="stat-label">Kelas Aktif</div>
                        </div>
                    </div>
                    <div class="stat-card stat-info">
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number">{{ $statistik['total_peserta_bertanya'] ?? 0 }}</div>
                            <div class="stat-label">Peserta Bertanya</div>
                        </div>
                    </div>
                    <div class="stat-card stat-warning">
                        <div class="stat-icon">
                            <i class="fas fa-calendar"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number">{{ $statistik['pertanyaan_hari_ini'] ?? 0 }}</div>
                            <div class="stat-label">Pertanyaan Hari Ini</div>
                        </div>
                    </div>
                </div>

                {{-- Kelas Tutorial Table --}}
                <div class="modern-table-container">
                    <div class="table-responsive">
                        @if ($kelasTutorial->isEmpty())
                            <div class="empty-table">
                                <div class="empty-table-content">
                                    <i class="fas fa-chalkboard-teacher"></i>
                                    <span>Belum ada kelas tutorial</span>
                                </div>
                            </div>
                        @else
                            <table class="modern-table" id="kelasTable">
                                <thead>
                                    <tr>
                                        <th>Nama Kelas</th>
                                        <th>Topik</th>
                                        <th>Start Time</th>
                                        <th class="text-center">Zoom</th>
                                        <th class="text-center">Daftar Pertanyaan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($kelasTutorial as $kelas)
                                        <tr>
                                            <td class="text-truncate" style="max-width: 200px;">
                                                {{ $kelas->kelas->name ?? '-' }}</td>
                                            <td class="text-truncate" style="max-width: 200px;">{{ $kelas->topic ?? '-' }}
                                            </td>
                                            <td>{{ $kelas->start_time ? \Carbon\Carbon::parse($kelas->start_time)->format('d M Y H:i') : '-' }}
                                            </td>
                                            <td class="text-center">
                                                @if (!empty($kelas->zoom_join_url))
                                                    <a href="{{ $kelas->zoom_join_url }}" target="_blank"
                                                        class="modern-btn modern-btn-primary btn-sm"
                                                        title="Join Zoom Meeting">
                                                        <i class="fas fa-video"></i>
                                                    </a>
                                                @else
                                                    <i class="fas fa-video text-muted" title="Zoom tidak tersedia"></i>
                                                @endif
                                            </td>
                                            @php
                                                $pertanyaanJson = json_encode(
                                                    $kelas->pertanyaan->map(function ($p) {
                                                        return [
                                                            'user' => $p->user->name ?? 'Anonim',
                                                            'isi' => $p->pertanyaan ?? '-',
                                                            'waktu' => $p->created_at->format('d M Y H:i'),
                                                        ];
                                                    }),
                                                );
                                            @endphp


                                            <td class="text-center">
                                                <button class="modern-btn modern-btn-info btn-sm btn-detail"
                                                    data-bs-toggle="modal" data-bs-target="#detailModal"
                                                    data-kelas="{{ $kelas->kelas->name ?? '-' }}"
                                                    data-topik="{{ $kelas->topic ?? '-' }}"
                                                    data-pertanyaan="{{ $pertanyaanJson }}">
                                                    Klik disini untuk pertanyaan dari peserta
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                        <div class="pagination justify-content-center mt-4">
                            {{ $kelasTutorial->withQueryString()->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Modal Detail Pertanyaan --}}
    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailModalLabel">Detail Pertanyaan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <h6 id="kelasInfo" class="mb-3 text-muted"></h6>
                    <ul class="list-group" id="pertanyaanList"></ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="modern-btn modern-btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    <style>
        /* Modern CSS Variables */
        :root {
            --primary: #6366f1;
            --primary-light: #a5b4fc;
            --primary-dark: #4f46e5;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #06b6d4;
            --secondary: #6b7280;
            --light: #f8fafc;
            --dark: #1f2937;
            --white: #ffffff;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            --radius: 8px;
            --radius-lg: 12px;
            --radius-xl: 16px;
            --transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Reset & Base */
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--gray-50);
            color: var(--gray-900);
            line-height: 1.6;
        }

        /* Page Header */
        .page-header {
            background: var(--white);
            padding: 2rem;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--gray-200);
        }

        .page-title {
            font-size: 1.875rem;
            font-weight: 700;
            color: var(--gray-900);
            margin: 0;
            display: flex;
            align-items: center;
        }

        .page-subtitle {
            color: var(--gray-600);
            margin: 0.5rem 0 0 0;
            font-size: 1rem;
        }

        .header-actions {
            display: flex;
            gap: 0.75rem;
            align-items: center;
        }

        /* Enhanced Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
        }

        .stat-card {
            background: var(--white);
            padding: 1.5rem;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--gray-200);
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--primary);
        }

        .stat-card.stat-success::before {
            background: var(--success);
        }

        .stat-card.stat-warning::before {
            background: var(--warning);
        }

        .stat-card.stat-info::before {
            background: var(--info);
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }

        .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: var(--radius);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: var(--white);
            background: var(--primary);
            position: relative;
        }

        .stat-card.stat-success .stat-icon {
            background: var(--success);
        }

        .stat-card.stat-warning .stat-icon {
            background: var(--warning);
        }

        .stat-card.stat-info .stat-icon {
            background: var(--info);
        }

        .stat-content {
            flex: 1;
        }

        .stat-number {
            font-size: 2.25rem;
            font-weight: 700;
            color: var(--gray-900);
            line-height: 1;
            margin-bottom: 0.25rem;
        }

        .stat-label {
            color: var(--gray-600);
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .stat-trend {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            font-size: 0.75rem;
            font-weight: 600;
        }

        /* Filter Section */
        .filter-section {
            background: var(--white);
            padding: 1.5rem;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--gray-200);
        }

        .filter-container {
            display: flex;
            gap: 1rem;
            align-items: center;
            flex-wrap: wrap;
        }

        .search-group {
            flex: 1;
            min-width: 300px;
        }

        .search-box {
            position: relative;
        }

        .search-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-400);
            z-index: 2;
        }

        .search-input {
            width: 100%;
            padding: 0.75rem 1rem 0.75rem 2.5rem;
            border: 2px solid var(--gray-200);
            border-radius: var(--radius);
            font-size: 0.875rem;
            transition: var(--transition);
            background: var(--gray-50);
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary);
            background: var(--white);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        .filter-group {
            display: flex;
            gap: 0.75rem;
            align-items: center;
            flex-wrap: wrap;
        }

        .modern-select {
            padding: 0.75rem 1rem;
            border: 2px solid var(--gray-200);
            border-radius: var(--radius);
            background: var(--gray-50);
            font-size: 0.875rem;
            min-width: 140px;
            transition: var(--transition);
        }

        .modern-select:focus {
            outline: none;
            border-color: var(--primary);
            background: var(--white);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        /* Modern Buttons */
        .modern-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border: 2px solid transparent;
            border-radius: var(--radius);
            font-size: 0.875rem;
            font-weight: 600;
            text-decoration: none;
            transition: var(--transition);
            cursor: pointer;
            white-space: nowrap;
        }

        .modern-btn.btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.8rem;
        }

        .modern-btn-primary {
            background: var(--primary);
            color: var(--white);
        }

        .modern-btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
            color: var(--white);
        }

        .modern-btn-secondary {
            background: var(--gray-100);
            color: var(--gray-700);
            border-color: var(--gray-200);
        }

        .modern-btn-secondary:hover {
            background: var(--gray-200);
            color: var(--gray-800);
        }

        .modern-btn-warning {
            background: var(--warning);
            color: var(--white);
        }

        .modern-btn-warning:hover {
            background: #d97706;
            color: var(--white);
        }

        .modern-btn-info {
            background: var(--info);
            color: var(--white);
        }

        .modern-btn-info:hover {
            background: #0891b2;
            color: var(--white);
        }

        .modern-btn-success {
            background: var(--success);
            color: var(--white);
        }

        .modern-btn-success:hover {
            background: #059669;
            color: var(--white);
        }

        .modern-btn-ghost {
            background: transparent;
            color: var(--gray-600);
            border-color: var(--gray-200);
        }

        .modern-btn-ghost:hover {
            background: var(--gray-50);
            color: var(--gray-700);
        }

        /* View Toggle */
        .view-toggle {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--white);
            padding: 1rem 1.5rem;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--gray-200);
        }

        .toggle-buttons {
            display: flex;
            background: var(--gray-100);
            border-radius: var(--radius);
            padding: 0.25rem;
        }

        .toggle-btn {
            padding: 0.5rem 1rem;
            border: none;
            background: transparent;
            color: var(--gray-600);
            font-size: 0.875rem;
            font-weight: 500;
            border-radius: calc(var(--radius) - 2px);
            transition: var(--transition);
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .toggle-btn.active {
            background: var(--white);
            color: var(--primary);
            box-shadow: var(--shadow-sm);
        }

        .results-info {
            color: var(--gray-600);
            font-size: 0.875rem;
        }

        .results-count strong {
            color: var(--gray-900);
            font-weight: 600;
        }

        /* View Containers */
        .view-container {
            display: none;
        }

        .view-container.active {
            display: block;
        }

        /* Modern Grid (Card View) */
        .modern-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
            gap: 1.5rem;
        }

        /* Modern Card */
        .modern-card {
            background: var(--white);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--gray-200);
            overflow: hidden;
            transition: var(--transition);
            position: relative;
        }

        .modern-card:hover {
            transform: translateY(-6px);
            box-shadow: var(--shadow-xl);
            border-color: var(--primary-light);
        }

        /* Card Header */
        .card-header-modern {
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: var(--white);
            position: relative;
        }

        .class-avatar {
            position: relative;
            flex-shrink: 0;
        }

        .avatar-circle {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.3);
            font-size: 1.25rem;
        }

        .status-indicator {
            position: absolute;
            bottom: -2px;
            right: -2px;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            border: 3px solid var(--white);
        }

        .status-active {
            background: var(--success);
            box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.3);
        }

        .status-pending {
            background: var(--warning);
            box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.3);
        }

        .status-completed {
            background: var(--info);
            box-shadow: 0 0 0 2px rgba(6, 182, 212, 0.3);
        }

        .status-inactive {
            background: var(--danger);
            box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.3);
        }

        .pulse {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
        }

        .class-basic-info {
            flex: 1;
            min-width: 0;
        }

        .class-name {
            font-size: 1.25rem;
            font-weight: 700;
            margin: 0 0 0.25rem 0;
            color: var(--white);
            line-height: 1.2;
        }

        .class-module {
            font-size: 0.875rem;
            margin: 0;
            color: rgba(255, 255, 255, 0.8);
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .card-menu {
            position: relative;
        }

        .menu-btn {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--transition);
        }

        .menu-btn:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .card-menu-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            background: var(--white);
            border-radius: var(--radius);
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--gray-200);
            min-width: 180px;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: var(--transition);
        }

        .card-menu.active .card-menu-dropdown {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .menu-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            color: var(--gray-700);
            text-decoration: none;
            font-size: 0.875rem;
            transition: var(--transition);
            border: none;
            background: none;
            width: 100%;
            text-align: left;
            cursor: pointer;
        }

        .menu-item:hover {
            background: var(--gray-50);
            color: var(--gray-900);
        }

        .menu-item.danger {
            color: var(--danger);
        }

        .menu-item.danger:hover {
            background: #fef2f2;
            color: var(--danger);
        }

        .menu-divider {
            height: 1px;
            background: var(--gray-200);
            margin: 0.5rem 0;
        }

        .menu-form {
            margin: 0;
        }

        /* Card Body */
        .card-body-modern {
            padding: 1.5rem;
        }

        .instructor-section {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
            padding: 1rem;
            background: var(--gray-50);
            border-radius: var(--radius);
        }

        .instructor-avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            font-weight: 600;
            font-size: 0.875rem;
        }

        .instructor-info {
            flex: 1;
        }

        .instructor-name {
            font-weight: 600;
            color: var(--gray-900);
            font-size: 0.9rem;
            line-height: 1.2;
        }

        .instructor-email {
            color: var(--gray-500);
            font-size: 0.8rem;
        }

        .info-grid {
            display: grid;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .info-icon {
            width: 36px;
            height: 36px;
            border-radius: var(--radius);
            background: var(--gray-100);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--gray-600);
            font-size: 0.875rem;
        }

        .info-content {
            flex: 1;
        }

        .info-label {
            display: block;
            font-size: 0.75rem;
            color: var(--gray-500);
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.25rem;
        }

        .info-value {
            font-weight: 600;
            color: var(--gray-900);
            font-size: 0.875rem;
        }

        /* Quota Progress */
        .quota-progress {
            background: var(--gray-50);
            padding: 1rem;
            border-radius: var(--radius);
            margin-bottom: 1rem;
        }

        .quota-header {
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 0.75rem;
        }

        .quota-label {
            font-size: 0.75rem;
            color: var(--gray-500);
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .quota-numbers {
            font-weight: 700;
            color: var(--primary);
            font-size: 1rem;
        }

        .progress-modern {
            height: 8px;
            background: var(--gray-200);
            border-radius: 4px;
            overflow: hidden;
            margin-bottom: 0.5rem;
            position: relative;
        }

        .progress-bar-modern {
            height: 100%;
            border-radius: 4px;
            transition: width 0.6s ease;
            position: relative;
        }

        .progress-success {
            background: linear-gradient(90deg, var(--success), #34d399);
        }

        .progress-warning {
            background: linear-gradient(90deg, var(--warning), #fbbf24);
        }

        .progress-info {
            background: linear-gradient(90deg, var(--info), #38bdf8);
        }

        .progress-bar-modern::after {
            content: attr(data-percentage) '%';
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 0.7rem;
            font-weight: 600;
            color: var(--white);
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
        }

        .quota-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .quota-remaining,
        .quota-percentage {
            font-size: 0.75rem;
            color: var(--gray-500);
        }

        /* Status Section */
        .status-section {
            display: flex;
            justify-content: center;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: var(--radius);
            font-size: 0.875rem;
            font-weight: 600;
        }

        .status-badge-active {
            background: #d1fae5;
            color: #065f46;
        }

        .status-badge-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status-badge-completed {
            background: #dbeafe;
            color: #1e40af;
        }

        .status-badge-inactive {
            background: #fee2e2;
            color: #991b1b;
        }

        /* Card Footer */
        .card-footer-modern {
            padding: 1rem 1.5rem;
            background: var(--gray-50);
            border-top: 1px solid var(--gray-200);
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }

        /* Table View */
        .modern-table-container {
            background: var(--white);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--gray-200);
            overflow: hidden;
        }

        .modern-table {
            width: 100%;
            border-collapse: collapse;
        }

        .modern-table th {
            background: var(--gray-50);
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: var(--gray-700);
            font-size: 0.875rem;
            border-bottom: 1px solid var(--gray-200);
        }

        .th-content {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .modern-table td {
            padding: 1rem;
            border-bottom: 1px solid var(--gray-100);
            vertical-align: middle;
        }

        .table-row {
            transition: var(--transition);
        }

        .table-row:hover {
            background: var(--gray-50);
        }

        .table-number {
            font-weight: 600;
            color: var(--gray-500);
            width: 60px;
        }

        .class-cell {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .class-avatar-sm {
            position: relative;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            font-size: 0.875rem;
        }

        .status-dot {
            position: absolute;
            bottom: -2px;
            right: -2px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            border: 2px solid var(--white);
        }

        .status-dot.status-active {
            background: var(--success);
        }

        .status-dot.status-pending {
            background: var(--warning);
        }

        .status-dot.status-completed {
            background: var(--info);
        }

        .status-dot.status-inactive {
            background: var(--danger);
        }

        .class-info {
            min-width: 0;
        }

        .class-name-sm {
            font-weight: 600;
            color: var(--gray-900);
            font-size: 0.875rem;
            line-height: 1.2;
        }

        .instructor-name-sm {
            color: var(--gray-500);
            font-size: 0.75rem;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .rayon-badge {
            display: inline-flex;
            padding: 0.25rem 0.75rem;
            background: var(--gray-100);
            color: var(--gray-700);
            border-radius: var(--radius);
            font-size: 0.75rem;
            font-weight: 500;
        }

        .module-cell {
            max-width: 200px;
        }

        .module-name {
            font-weight: 500;
            color: var(--gray-900);
            font-size: 0.875rem;
        }

        .schedule-cell {
            display: flex;
            flex-direction: column;
            gap: 0.125rem;
        }

        .schedule-date {
            font-weight: 600;
            color: var(--gray-900);
            font-size: 0.875rem;
        }

        .schedule-time {
            color: var(--gray-500);
            font-size: 0.75rem;
        }

        .participants-cell {
            min-width: 120px;
        }

        .participants-numbers {
            font-weight: 600;
            color: var(--primary);
            font-size: 0.875rem;
            margin-bottom: 0.25rem;
        }

        .progress-mini {
            height: 4px;
            background: var(--gray-200);
            border-radius: 2px;
            overflow: hidden;
            margin-bottom: 0.25rem;
        }

        .progress-bar-mini {
            height: 100%;
            border-radius: 2px;
            transition: width 0.6s ease;
        }

        .progress-bar-mini.progress-success {
            background: var(--success);
        }

        .progress-bar-mini.progress-warning {
            background: var(--warning);
        }

        .progress-bar-mini.progress-info {
            background: var(--info);
        }

        .participants-remaining {
            color: var(--gray-500);
            font-size: 0.7rem;
        }

        .table-actions {
            display: flex;
            justify-content: center;
        }

        .action-group {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }

        .action-btn {
            width: 32px;
            height: 32px;
            border-radius: var(--radius);
            border: 1px solid var(--gray-200);
            background: var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--gray-600);
            text-decoration: none;
            transition: var(--transition);
            cursor: pointer;
            font-size: 0.875rem;
        }

        .action-btn:hover {
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        .action-info {
            color: var(--info);
            border-color: var(--info);
        }

        .action-info:hover {
            background: var(--info);
            color: var(--white);
        }

        .action-warning {
            color: var(--warning);
            border-color: var(--warning);
        }

        .action-warning:hover {
            background: var(--warning);
            color: var(--white);
        }

        .action-secondary {
            color: var(--gray-600);
            border-color: var(--gray-200);
        }

        .action-secondary:hover {
            background: var(--gray-100);
            color: var(--gray-700);
        }

        .dropdown-table {
            position: relative;
        }

        .dropdown-menu-table {
            position: absolute;
            top: 100%;
            right: 0;
            background: var(--white);
            border-radius: var(--radius);
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--gray-200);
            min-width: 160px;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: var(--transition);
        }

        .dropdown-table.active .dropdown-menu-table {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-item-table {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 0.75rem;
            color: var(--gray-700);
            text-decoration: none;
            font-size: 0.8rem;
            transition: var(--transition);
            border: none;
            background: none;
            width: 100%;
            text-align: left;
            cursor: pointer;
        }

        .dropdown-item-table:hover {
            background: var(--gray-50);
            color: var(--gray-900);
        }

        .dropdown-item-table.danger {
            color: var(--danger);
        }

        .dropdown-item-table.danger:hover {
            background: #fef2f2;
            color: var(--danger);
        }

        /* Empty States */
        .empty-state-modern {
            grid-column: 1 / -1;
            text-align: center;
            padding: 4rem 2rem;
        }

        .empty-illustration {
            width: 80px;
            height: 80px;
            margin: 0 auto 1.5rem;
            background: var(--gray-100);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: var(--gray-400);
        }

        .empty-state-modern h3 {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--gray-900);
            margin: 0 0 0.5rem 0;
        }

        .empty-state-modern p {
            color: var(--gray-600);
            margin: 0 0 1.5rem 0;
        }

        .empty-table {
            text-align: center;
            padding: 3rem 1rem;
        }

        .empty-table-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1rem;
            color: var(--gray-500);
        }

        .empty-table-content i {
            font-size: 2rem;
            color: var(--gray-400);
        }

        /* Results Footer */
        .results-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 2rem;
            padding: 1rem 1.5rem;
            background: var(--white);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--gray-200);
        }

        .results-summary {
            color: var(--gray-600);
            font-size: 0.875rem;
        }

        .summary-text strong {
            color: var(--gray-900);
            font-weight: 600;
        }

        .last-updated {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--gray-500);
            font-size: 0.8rem;
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .modern-grid {
                grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
                gap: 1rem;
            }

            .filter-container {
                flex-direction: column;
                align-items: stretch;
            }

            .search-group {
                min-width: auto;
            }

            .filter-group {
                justify-content: center;
            }
        }

        @media (max-width: 768px) {
            .page-header {
                padding: 1.5rem;
            }

            .page-title {
                font-size: 1.5rem;
            }

            .header-actions {
                flex-direction: column;
                gap: 0.5rem;
                width: 100%;
            }

            .modern-btn {
                width: 100%;
                justify-content: center;
            }

            .stats-grid {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 1rem;
            }

            .stat-card {
                padding: 1rem;
            }

            .stat-number {
                font-size: 1.75rem;
            }

            .filter-section {
                padding: 1rem;
            }

            .modern-grid {
                grid-template-columns: 1fr;
            }

            .view-toggle {
                flex-direction: column;
                gap: 1rem;
                align-items: stretch;
            }

            .toggle-buttons {
                justify-content: center;
            }

            .card-header-modern {
                padding: 1rem;
            }

            .card-body-modern {
                padding: 1rem;
            }

            .action-buttons {
                flex-direction: column;
            }

            .modern-table-container {
                overflow-x: auto;
            }

            .modern-table {
                min-width: 800px;
            }

            .results-footer {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
        }

        @media (max-width: 480px) {
            .page-header {
                padding: 1rem;
            }

            .filter-section {
                padding: 0.75rem;
            }

            .modern-btn {
                padding: 0.625rem 1rem;
                font-size: 0.8rem;
            }

            .card-header-modern {
                padding: 0.75rem;
                flex-direction: column;
                text-align: center;
                gap: 0.75rem;
            }

            .class-basic-info {
                text-align: center;
            }

            .card-body-modern {
                padding: 0.75rem;
            }

            .instructor-section {
                flex-direction: column;
                text-align: center;
            }
        }

        /* Animation Classes */
        .fade-in {
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .slide-in {
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* Loading States */
        .loading {
            opacity: 0.6;
            pointer-events: none;
            position: relative;
        }

        .loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 20px;
            height: 20px;
            margin: -10px 0 0 -10px;
            border: 2px solid var(--gray-300);
            border-top: 2px solid var(--primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Focus States */
        .modern-btn:focus,
        .action-btn:focus,
        .menu-btn:focus,
        .toggle-btn:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        /* Print Styles */
        @media print {

            .header-actions,
            .filter-section,
            .view-toggle,
            .action-buttons,
            .table-actions,
            .card-menu {
                display: none !important;
            }

            .modern-card {
                break-inside: avoid;
                box-shadow: none;
                border: 1px solid var(--gray-300);
            }

            .page-header {
                box-shadow: none;
                border-bottom: 2px solid var(--gray-300);
            }
        }

        /* Focused state for navigation */
        .kelas-card.focused,
        .table-row.focused {
            outline: 2px solid var(--primary);
            outline-offset: 2px;
        }
    </style>
    {{-- Styles --}}
    <style>
        /* Sesuaikan style modern sesuai contoh Anda */
        .modern-table-container {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgb(0 0 0 / 0.1);
            border: 1px solid #e5e7eb;
            overflow: hidden;
        }

        .modern-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 0.75rem;
        }

        .modern-table thead tr th {
            background: transparent;
            color: #4f46e5;
            font-weight: 600;
            padding: 0.75rem 1rem;
            text-align: left;
            border-bottom: 2px solid #4f46e5;
        }

        .modern-table tbody tr {
            background: #f9fafb;
            border-radius: 12px;
            box-shadow: 0 1px 2px rgb(0 0 0 / 0.05);
            transition: box-shadow 0.3s ease;
        }

        .modern-table tbody tr:hover {
            box-shadow: 0 4px 6px rgb(0 0 0 / 0.1);
        }

        .modern-table tbody tr td {
            padding: 0.75rem 1rem;
            vertical-align: middle;
        }

        .text-truncate {
            max-width: 200px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .modern-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.375rem 0.75rem;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: background-color 0.3s ease;
            text-decoration: none;
            color: white;
            user-select: none;
        }

        .modern-btn-primary {
            background-color: #4f46e5;
        }

        .modern-btn-primary:hover {
            background-color: #4338ca;
        }

        .modern-btn-info {
            background-color: #06b6d4;
        }

        .modern-btn-info:hover {
            background-color: #0891b2;
        }

        .modern-btn-secondary {
            background-color: #e5e7eb;
            color: #374151;
        }

        .modern-btn-secondary:hover {
            background-color: #d1d5db;
            color: #1f2937;
        }

        .list-group-item {
            padding: 0.75rem 1rem;
            border: none;
            border-bottom: 1px solid #e5e7eb;
        }

        .list-group-item:last-child {
            border-bottom: none;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const detailModal = document.getElementById('detailModal');
            const kelasInfo = detailModal.querySelector('#kelasInfo');
            const pertanyaanList = detailModal.querySelector('#pertanyaanList');

            detailModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const pertanyaanData = button.getAttribute('data-pertanyaan');
                const kelasName = button.getAttribute('data-kelas');
                const topik = button.getAttribute('data-topik');

                // Set judul modal
                kelasInfo.textContent = `Kelas: ${kelasName} | Topik: ${topik}`;
                pertanyaanList.innerHTML = '';

                try {
                    const pertanyaans = JSON.parse(pertanyaanData);

                    if (pertanyaans.length === 0) {
                        pertanyaanList.innerHTML = `
                        <li class="list-group-item">Tidak ada pertanyaan untuk kelas ini.</li>`;
                        return;
                    }

                    // Group pertanyaan berdasarkan user
                    const grouped = pertanyaans.reduce((acc, curr) => {
                        const user = curr.user || 'Anonim';
                        if (!acc[user]) acc[user] = [];
                        acc[user].push(curr);
                        return acc;
                    }, {});

                    let userCounter = 1;

                    Object.entries(grouped).forEach(([user, list]) => {
                        // Bagian Nama dan Tanggal
                        const headerItem = document.createElement('li');
                        headerItem.className = 'list-group-item py-2';
                        headerItem.innerHTML = `
                        <strong class="d-block" style="font-size: 1rem;">${userCounter}. ${user}</strong>
                        <div><small class="text-muted">${list[0].waktu}</small></div>
                    `;
                        pertanyaanList.appendChild(headerItem);

                        // Bagian pertanyaan 1, 2, dst.
                        const pertanyaanItem = document.createElement('li');
                        pertanyaanItem.className = 'list-group-item ps-3 py-1';
                        pertanyaanItem.innerHTML = list
                            .map((item, index) => `${index + 1}. ${item.isi}`)
                            .join('<br>');
                        pertanyaanList.appendChild(pertanyaanItem);

                        // Pemisah antar user
                        const bottomHr = document.createElement('li');
                        bottomHr.className = 'list-group-item p-0';
                        bottomHr.innerHTML = '<hr class="my-3">';
                        pertanyaanList.appendChild(bottomHr);

                        userCounter++;
                    });

                } catch (error) {
                    pertanyaanList.innerHTML = `
                    <li class="list-group-item text-danger">Gagal memuat pertanyaan.</li>`;
                }
            });
        });
    </script>


@endsection
