@php
    $colorMap = [
        'active' => 'success',
        'completed' => 'success',
        'in_progress' => 'warning',
        'failed' => 'danger',
        'pending' => 'secondary',
        'not_started' => 'dark',
    ];

    $color = $colorMap[$status ?? ''] ?? 'secondary';
@endphp

<span class="badge bg-{{ $color }} text-white">
    <i class="fa fa-check-circle"></i> {{ ucfirst(str_replace('_', ' ', $status ?? 'unknown')) }}
</span>

