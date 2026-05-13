@extends('layouts.kom')

@section('content')
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Hasil Review Pengajar</h5>
                </div>
                <div class="card-body">
                    @if (isset($message))
                        <div class="alert alert-info">{{ $message }}</div>
                    @else
                        {{-- ✅ Tampilkan nama pengajar --}}
                        @if (isset($pengajar))
                            <div class="mb-3">
                                <h5>Review Untuk Pengajar: <strong>{{ $pengajar->name ?? 'Tidak diketahui' }}</strong></h5>
                            </div>
                        @endif

                        <p>Total Review Masuk: <strong>{{ $reviewsCount }}</strong></p>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped mt-3">
                                <thead class="table-light">
                                    <tr>
                                        <th>Kategori</th>
                                        <th class="text-center">Rata-Rata Skor</th>
                                        <th class="text-center">Penilaian</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($averages as $field => $average)
                                        <tr>
                                            <td>{{ ucwords(str_replace('_', ' ', $field)) }}</td>
                                            <td class="text-center">{{ $average ?? '-' }}</td>
                                            <td class="text-center">
                                                @php
                                                    $fullStars = floor($average); // bintang penuh (integer)
                                                    $halfStar = $average - $fullStars >= 0.5; // bintang setengah (jika mau)
                                                    $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
                                                @endphp

                                                {{-- Render full stars --}}
                                                @for ($i = 0; $i < $fullStars; $i++)
                                                    <span style="color: gold; font-size: 1.2rem;">&#9733;</span>
                                                    {{-- ★ --}}
                                                @endfor

                                                {{-- Render half star (optional) --}}
                                                @if ($halfStar)
                                                    <span style="color: gold; font-size: 1.2rem;">&#189;</span>
                                                    {{-- Bisa ganti icon lain jika mau --}}
                                                @endif

                                                {{-- Render empty stars --}}
                                                @for ($i = 0; $i < $emptyStars; $i++)
                                                    <span style="color: #ccc; font-size: 1.2rem;">&#9733;</span>
                                                    {{-- ★ abu-abu --}}
                                                @endfor
                                            </td>

                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <hr>

                        <canvas id="reviewChart" height="100"></canvas>
                    @endif
                </div>

            </div>
        </div>
    </div>

    @if (!isset($message))
        @push('scripts')
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const ctx = document.getElementById('reviewChart').getContext('2d');
                    const reviewChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: @json($chartLabels),
                            datasets: [{
                                label: 'Rata-Rata Skor (1-5)',
                                data: @json($chartData),
                                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                                borderColor: 'rgba(54, 162, 235, 1)',
                                borderWidth: 1,
                                borderRadius: 4,
                                barPercentage: 0.6,
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    max: 5,
                                    ticks: {
                                        stepSize: 1,
                                        callback: function(value) {
                                            const map = {
                                                1: 'Sangat Buruk',
                                                2: 'Buruk',
                                                3: 'Cukup',
                                                4: 'Baik',
                                                5: 'Sangat Baik'
                                            };
                                            return map[value] || value;
                                        }
                                    },
                                    title: {
                                        display: true,
                                        text: 'Skor'
                                    }
                                }
                            },
                            plugins: {
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            const val = context.parsed.y;
                                            const map = {
                                                1: 'Sangat Buruk',
                                                2: 'Buruk',
                                                3: 'Cukup',
                                                4: 'Baik',
                                                5: 'Sangat Baik'
                                            };
                                            return `Skor: ${val} (${map[val] || '-'})`;
                                        }
                                    }
                                },
                                legend: {
                                    display: false
                                },
                                title: {
                                    display: true,
                                    text: 'Rata-Rata Review Pengajar'
                                }
                            }
                        }
                    });
                });
            </script>
        @endpush
    @endif
@endsection
