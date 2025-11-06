@extends('layouts.app')

@section('title', 'Laporan')
@section('page-title', 'Laporan')

@section('content')
<!-- Statistik Periode -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary mb-2">
                    <i class="fas fa-users"></i>
                </div>
                <h3 class="mb-0 fw-bold">{{ $statistics['total_visitors'] }}</h3>
                <small class="text-muted">Total Pengunjung</small>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="stat-icon bg-success bg-opacity-10 text-success mb-2">
                    <i class="fas fa-check-double"></i>
                </div>
                <h3 class="mb-0 fw-bold">{{ $statistics['completed'] }}</h3>
                <small class="text-muted">Selesai</small>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="stat-icon bg-info bg-opacity-10 text-info mb-2">
                    <i class="fas fa-handshake"></i>
                </div>
                <h3 class="mb-0 fw-bold">{{ $statistics['meeting'] }}</h3>
                <small class="text-muted">Sedang Bertemu</small>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="stat-icon bg-warning bg-opacity-10 text-warning mb-2">
                    <i class="fas fa-clock"></i>
                </div>
                <h3 class="mb-0 fw-bold">{{ $statistics['pending'] }}</h3>
                <small class="text-muted">Menunggu</small>
            </div>
        </div>
    </div>
</div>

<!-- Filter -->
<div class="table-card mb-4">
    <form action="{{ route('admin.reports.index') }}" method="GET" class="row g-3">
        <div class="col-md-3">
            <label class="form-label fw-bold">Dari Tanggal</label>
            <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}" required>
        </div>
        <div class="col-md-3">
            <label class="form-label fw-bold">Sampai Tanggal</label>
            <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}" required>
        </div>
        <div class="col-md-3">
            <label class="form-label fw-bold">Status</label>
            <select name="status" class="form-select">
                <option value="all" {{ $status == 'all' ? 'selected' : '' }}>Semua Status</option>
                <option value="completed" {{ $status == 'completed' ? 'selected' : '' }}>Selesai</option>
                <option value="meeting" {{ $status == 'meeting' ? 'selected' : '' }}>Bertemu</option>
                <option value="verified" {{ $status == 'verified' ? 'selected' : '' }}>Terverifikasi</option>
                <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Pending</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label fw-bold">Pegawai</label>
            <select name="employee_id" class="form-select">
                <option value="all" {{ $employeeId == 'all' ? 'selected' : '' }}>Semua Pegawai</option>
                @foreach($employees as $employee)
                    <option value="{{ $employee->id }}" {{ $employeeId == $employee->id ? 'selected' : '' }}>
                        {{ $employee->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search me-2"></i>Tampilkan Laporan
            </button>
            <a href="{{ route('admin.reports.export', request()->all()) }}" class="btn btn-success">
                <i class="fas fa-file-excel me-2"></i>Export Excel
            </a>
        </div>
    </form>
</div>

<!-- Chart Grafik Harian -->
<div class="row mb-4">
    <div class="col-lg-8">
        <div class="table-card">
            <h5 class="mb-3 fw-bold">
                <i class="fas fa-chart-line me-2"></i>Grafik Kunjungan Harian
            </h5>
            <canvas id="dailyChart" height="100"></canvas>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="table-card">
            <h5 class="mb-3 fw-bold">
                <i class="fas fa-trophy me-2"></i>Top 5 Pegawai
            </h5>
            @forelse($topEmployees as $index => $employee)
                <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                    <div class="me-3">
                        <span class="badge {{ $index == 0 ? 'bg-warning' : ($index == 1 ? 'bg-secondary' : 'bg-info') }}" 
                              style="font-size: 1.2rem; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                            {{ $index + 1 }}
                        </span>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-bold">{{ $employee->name }}</div>
                        <small class="text-muted">{{ $employee->total_guests }} tamu</small>
                    </div>
                </div>
            @empty
                <p class="text-muted text-center">Belum ada data</p>
            @endforelse
        </div>
    </div>
</div>

<!-- Tabel Detail -->
<div class="table-card">
    <h5 class="mb-3 fw-bold">
        <i class="fas fa-table me-2"></i>Detail Laporan ({{ $guests->count() }} tamu)
    </h5>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Nama</th>
                    <th>Telepon</th>
                    <th>Perusahaan</th>
                    <th>Pegawai Dituju</th>
                    <th>Status</th>
                    <th>Check Out</th>
                </tr>
            </thead>
            <tbody>
                @forelse($guests as $index => $guest)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <small>{{ \Carbon\Carbon::parse($guest->check_in_time)->format('d/m/Y H:i') }}</small>
                        </td>
                        <td>
                            <div class="fw-bold">{{ $guest->name }}</div>
                            <small class="text-muted">{{ $guest->email ?? '-' }}</small>
                        </td>
                        <td>{{ $guest->phone }}</td>
                        <td>{{ $guest->company ?? '-' }}</td>
                        <td>
                            @php
                                $employees = DB::table('guest_employees')
                                    ->join('users', 'guest_employees.employee_id', '=', 'users.id')
                                    ->where('guest_employees.guest_id', $guest->id)
                                    ->pluck('users.name')
                                    ->toArray();
                            @endphp
                            <small>{{ implode(', ', $employees) }}</small>
                        </td>
                        <td>
                            @if($guest->status == 'pending')
                                <span class="badge bg-warning">Pending</span>
                            @elseif($guest->status == 'verified')
                                <span class="badge bg-success">Verified</span>
                            @elseif($guest->status == 'meeting')
                                <span class="badge bg-info">Meeting</span>
                            @else
                                <span class="badge bg-primary">Completed</span>
                            @endif
                        </td>
                        <td>
                            @if($guest->check_out_time)
                                <small>{{ \Carbon\Carbon::parse($guest->check_out_time)->format('d/m/Y H:i') }}</small>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                            <p class="text-muted">Tidak ada data pada periode ini</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
// Data untuk chart
const dailyData = @json($dailyStats);
const labels = dailyData.map(item => {
    const date = new Date(item.date);
    return date.toLocaleDateString('id-ID', { day: '2-digit', month: 'short' });
});
const data = dailyData.map(item => item.count);

// Konfigurasi chart
const ctx = document.getElementById('dailyChart').getContext('2d');
const gradient = ctx.createLinearGradient(0, 0, 0, 400);
gradient.addColorStop(0, 'rgba(102, 126, 234, 0.5)');
gradient.addColorStop(1, 'rgba(118, 75, 162, 0.1)');

const chart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: labels,
        datasets: [{
            label: 'Jumlah Tamu',
            data: data,
            borderColor: 'rgb(102, 126, 234)',
            backgroundColor: gradient,
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointRadius: 5,
            pointHoverRadius: 7,
            pointBackgroundColor: 'white',
            pointBorderColor: 'rgb(102, 126, 234)',
            pointBorderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12,
                titleFont: {
                    size: 14
                },
                bodyFont: {
                    size: 13
                },
                callbacks: {
                    label: function(context) {
                        return 'Tamu: ' + context.parsed.y + ' orang';
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1,
                    callback: function(value) {
                        return value + ' tamu';
                    }
                },
                grid: {
                    color: 'rgba(0, 0, 0, 0.05)'
                }
            },
            x: {
                grid: {
                    display: false
                }
            }
        }
    }
});
</script>
@endpush