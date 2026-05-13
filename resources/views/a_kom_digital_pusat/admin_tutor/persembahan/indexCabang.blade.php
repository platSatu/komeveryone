@extends('layouts.KomDigital')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">

            {{-- NOTIFICATIONS --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm rounded-3 d-flex align-items-center" role="alert">
                    <i class="fas fa-check-circle me-2 fs-5"></i>
                    <div><strong>{{ session('success') }}</strong></div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error') || isset($error))
                <div class="alert alert-danger alert-dismissible fade show shadow-sm rounded-3 d-flex align-items-center" role="alert">
                    <i class="fas fa-exclamation-triangle me-2 fs-5"></i>
                    <div><strong>{{ session('error') ?? $error }}</strong></div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- Header Section --}}
            <div class="page-header mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="page-title">
                            <i class="fas fa-donate me-2 text-primary pt-1"></i>&nbsp;
                            Data Persembahan
                        </h4>
                        <p class="page-subtitle pb-1">Kelola data persembahan</p>
                    </div>
                    <div class="header-actions">
                        <button class="modern-btn modern-btn-ghost" onclick="refreshData()">
                            <i class="fas fa-sync-alt"></i>
                            Refresh
                        </button>
                        {{-- Tambah tombol lain jika perlu --}}
                    </div>
                </div>
            </div>

            {{-- Enhanced Stats Cards --}}
            <div class="stats-grid mb-4">
                @php
                    $cards = [
                        [
                            'icon' => 'fa-coins',
                            'label' => 'Total Persembahan',
                            'value' => 'Rp ' . number_format($totalPersembahan ?? 0, 0, ',', '.'),
                            'color' => 'success',
                        ],
                        [
                            'icon' => 'fa-chalkboard-teacher',
                            'label' => 'Total Kelas',
                            'value' => $totalKelas ?? 0,
                            'color' => 'info',
                        ],
                        [
                            'icon' => 'fa-users',
                            'label' => 'Total User',
                            'value' => $totalUser  ?? 0,
                            'color' => 'warning',
                        ],
                    ];
                @endphp

                @foreach ($cards as $card)
                    <div class="stat-card stat-{{ $card['color'] }}">
                        <div class="stat-icon">
                            <i class="fas {{ $card['icon'] }}"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number">{{ $card['value'] }}</div>
                            <div class="stat-label">{{ $card['label'] }}</div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Filter Section --}}
            <div class="filter-section mb-4">
                <div class="filter-container">
                    <div class="search-group">
                        <div class="search-box">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" name="search" id="searchInput" class="search-input"
                                placeholder="Cari user atau kelas tutorial..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="filter-group">
                        <input type="date" name="date_from" id="dateFrom" class="modern-select" value="{{ request('date_from') }}">
                        <input type="date" name="date_to" id="dateTo" class="modern-select" value="{{ request('date_to') }}">

                        <button class="modern-btn modern-btn-secondary" onclick="clearFilters()">
                            <i class="fas fa-times"></i> Clear
                        </button>
                        <button class="modern-btn modern-btn-primary" onclick="applyFilters()">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                    </div>
                </div>
            </div>

            {{-- Data Persembahan Table --}}
            <div class="modern-table-container">
                <div class="table-responsive">
                    <table class="modern-table" id="persembahanTable">
    <thead>
        <tr>
            <th>#</th>
            <th>User</th>
           
            <th>Kelas Tutorial</th>
            <th>Order Id</th>
            <th class="text-end">Jumlah</th>
            <th class="text-end">Status</th>
            <th>Tanggal</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($groupedDonasi as $uctId => $data)
            @php
                $userId = $data['user_class_tutorial']->user->id ?? 'Anonim';
                $userName = $data['user_class_tutorial']->user->name ?? 'Anonim';
                $userEmail = $data['user_class_tutorial']->user->email ?? '-';
                $description = $data['user_class_tutorial']->classTutorial->topic ?? '-';

                if (!empty($data['donasi_list']) && $data['donasi_list']->count() > 0) {
                    $lastDonation = $data['donasi_list']->sortByDesc('transaction_time')->first();
                    $status = $lastDonation->status ?? '-';
                } else {
                    $status = '-';
                }
            @endphp

            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $userName }}<br>
                    <small class="text-muted">{{ $userEmail }}</small><br>
                    <small class="text-muted">{{ $userId }}</small>
                </td>
              
                <td>{{ $description }}</td>
                <td>{{ $lastDonation->order_id ?? '-' }}</td>
                <td class="text-end">Rp {{ number_format($data['total_amount'], 0, ',', '.') }}</td>
                <td>
                    @php
                        $badgeClass = 'badge bg-warning text-white';
                        if (strtolower($status) === 'paid') {
                            $badgeClass = 'badge bg-success text-white';
                        }
                    @endphp
                    <span class="{{ $badgeClass }}">{{ ucfirst($status) }}</span>
                </td>
                <td>
                    @if(!empty($data['donasi_list']))
                        {{ $data['donasi_list']->max('transaction_time')->format('d M Y') }}
                    @else
                        -
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="empty-table">
                    <div class="empty-table-content">
                        <i class="fas fa-donate"></i>
                        <span>Belum ada data persembahan</span>
                    </div>
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

                     <div class="pagination justify-content-center mt-4">
     {{ $donasiData->withQueryString()->links('pagination::bootstrap-4') }}

 </div>
                </div>
                
            </div>

            {{-- Results Footer --}}
            <div class="results-footer mt-4 mb-4">
                <div class="results-summary">
                    <span class="summary-text">
                        Menampilkan <strong>{{ $groupedDonasi->count() }}</strong> dari <strong>{{ $groupedDonasi->count() }}</strong> data persembahan
                    </span>
                </div>
                <div class="last-updated">
                    <i class="fas fa-clock"></i>
                    <span>Terakhir diperbarui: {{ now()->format('d M Y, H:i') }}</span>
                </div>
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
{{-- Script untuk filter dan refresh --}}
<script>
    function clearFilters() {
        document.getElementById('searchInput').value = '';
        document.getElementById('dateFrom').value = '';
        document.getElementById('dateTo').value = '';
        applyFilters();
    }

    function applyFilters() {
        const search = document.getElementById('searchInput').value;
        const dateFrom = document.getElementById('dateFrom').value;
        const dateTo = document.getElementById('dateTo').value;

        const params = new URLSearchParams();

        if (search) params.append('search', search);
        if (dateFrom) params.append('date_from', dateFrom);
        if (dateTo) params.append('date_to', dateTo);

        window.location.href = '{{ route('kom.persembahan_cabang.index') }}' + '?' + params.toString();
    }

    function refreshData() {
        location.reload();
    }
</script>

{{-- SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.querySelectorAll('.btn-tarik-dana').forEach(button => {
        button.addEventListener('click', function() {
            const btn = this;
            const uctId = btn.getAttribute('data-uctid');
            const userId = btn.getAttribute('data-userid');
            const cabangId = btn.getAttribute('data-cabangid');
            const totalAmount = parseFloat(btn.getAttribute('data-totalamount'));
            const route = btn.getAttribute('data-route');

            if (isNaN(totalAmount) || totalAmount <= 0) {
                Swal.fire('Tidak Bisa Tarik', 'Dana tidak tersedia atau sudah ditarik.', 'warning');
                return;
            }

            Swal.fire({
                title: 'Konfirmasi Tarik Dana',
                text: `Anda yakin ingin menarik dana sebesar Rp ${totalAmount.toLocaleString('id-ID')}?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Tarik',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    btn.querySelector('.btn-text').classList.add('d-none');
                    btn.querySelector('.spinner-border').classList.remove('d-none');
                    btn.disabled = true;

                    fetch(route, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                userclasstutorial_id: uctId,
                                user_id: userId,
                                cabang_id: cabangId,
                                jumlah: totalAmount,
                                tanggal_penarikan: new Date().toISOString().split('T')[0],
                            }),
                        })
                        .then(response => response.json())
                        .then(data => {
                            btn.querySelector('.btn-text').classList.remove('d-none');
                            btn.querySelector('.spinner-border').classList.add('d-none');
                            btn.disabled = false;

                            if (data.status === 'success') {
                                Swal.fire('Berhasil', data.message, 'success')
                                    .then(() => location.reload());
                            } else {
                                Swal.fire('Gagal', data.message || 'Terjadi kesalahan saat menarik dana.', 'error');
                            }
                        })
                        .catch(() => {
                            btn.querySelector('.btn-text').classList.remove('d-none');
                            btn.querySelector('.spinner-border').classList.add('d-none');
                            btn.disabled = false;
                            Swal.fire('Gagal', 'Terjadi kesalahan jaringan.', 'error');
                        });
                }
            });
        });
    });
</script>
@endsection