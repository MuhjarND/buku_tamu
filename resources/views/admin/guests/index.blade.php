@extends('layouts.app')

@section('title', 'Data Tamu')
@section('page-title', 'Data Tamu')

@section('content')
<div class="row mb-4">
    <!-- Statistik Cards -->
    <div class="col-lg-2 col-md-4 mb-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="stat-icon bg-success bg-opacity-10 text-success mb-2">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <h4 class="mb-0 fw-bold">{{ $statistics['today'] }}</h4>
                <small class="text-muted">Hari Ini</small>
            </div>
        </div>
    </div>

    <div class="col-lg-2 col-md-4 mb-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="stat-icon bg-warning bg-opacity-10 text-warning mb-2">
                    <i class="fas fa-clock"></i>
                </div>
                <h4 class="mb-0 fw-bold">{{ $statistics['pending'] }}</h4>
                <small class="text-muted">Pending</small>
            </div>
        </div>
    </div>

    <div class="col-lg-2 col-md-4 mb-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="stat-icon bg-success bg-opacity-10 text-success mb-2">
                    <i class="fas fa-check"></i>
                </div>
                <h4 class="mb-0 fw-bold">{{ $statistics['verified'] }}</h4>
                <small class="text-muted">Verified</small>
            </div>
        </div>
    </div>

    <div class="col-lg-2 col-md-4 mb-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="stat-icon bg-info bg-opacity-10 text-info mb-2">
                    <i class="fas fa-handshake"></i>
                </div>
                <h4 class="mb-0 fw-bold">{{ $statistics['meeting'] }}</h4>
                <small class="text-muted">Meeting</small>
            </div>
        </div>
    </div>

    <div class="col-lg-2 col-md-4 mb-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary mb-2">
                    <i class="fas fa-check-double"></i>
                </div>
                <h4 class="mb-0 fw-bold">{{ $statistics['completed'] }}</h4>
                <small class="text-muted">Completed</small>
            </div>
        </div>
    </div>

    <div class="col-lg-2 col-md-4 mb-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="stat-icon bg-dark bg-opacity-10 text-dark mb-2">
                    <i class="fas fa-users"></i>
                </div>
                <h4 class="mb-0 fw-bold">{{ $statistics['total'] }}</h4>
                <small class="text-muted">Total</small>
            </div>
        </div>
    </div>
</div>

<!-- Filter -->
<div class="table-card mb-4">
    <form action="{{ route('admin.guests.index') }}" method="GET" class="row g-3">
        <div class="col-md-3">
            <label class="form-label fw-bold">Status</label>
            <select name="status" class="form-select" onchange="this.form.submit()">
                <option value="all" {{ $status == 'all' ? 'selected' : '' }}>Semua Status</option>
                <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="verified" {{ $status == 'verified' ? 'selected' : '' }}>Verified</option>
                <option value="meeting" {{ $status == 'meeting' ? 'selected' : '' }}>Meeting</option>
                <option value="completed" {{ $status == 'completed' ? 'selected' : '' }}>Completed</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label fw-bold">Dari Tanggal</label>
            <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
        </div>
        <div class="col-md-3">
            <label class="form-label fw-bold">Sampai Tanggal</label>
            <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
        </div>
        <div class="col-md-3">
            <label class="form-label fw-bold">Pencarian</label>
            <input type="text" name="search" class="form-control" placeholder="Nama/Telepon/Perusahaan" value="{{ $search }}">
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search me-2"></i>Cari
            </button>
            <a href="{{ route('admin.guests.index') }}" class="btn btn-secondary">
                <i class="fas fa-redo me-2"></i>Reset
            </a>
        </div>
    </form>
</div>

<!-- Tabel Tamu -->
<div class="table-card">
    <h5 class="mb-3 fw-bold">
        <i class="fas fa-list me-2"></i>Daftar Tamu ({{ $guests->total() }})
    </h5>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>Foto</th>
                    <th>Nama</th>
                    <th>Telepon</th>
                    <th>Perusahaan</th>
                    <th>Pegawai Dituju</th>
                    <th>Status</th>
                    <th>Check In</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($guests as $guest)
                    <tr>
                        <td>{{ $guests->firstItem() + $loop->index }}</td>
                        <td>
                            @if($guest->photo)
                                <img src="{{ asset('storage/' . $guest->photo) }}" class="rounded" width="40" height="40" style="object-fit: cover;">
                            @else
                                <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #1B4332 0%, #1e3a8a 50%, #B8860B 100%);" 
                                     class="rounded d-flex align-items-center justify-content-center text-white fw-bold">
                                    {{ strtoupper(substr($guest->name, 0, 1)) }}
                                </div>
                            @endif
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
                            <small>{{ \Carbon\Carbon::parse($guest->check_in_time)->format('d/m/Y H:i') }}</small>
                        </td>
                        <td>
                            <div class="btn-group">
                                <a href="{{ route('admin.guest.show', $guest->id) }}" class="btn btn-sm btn-info text-white">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button class="btn btn-sm btn-danger" onclick="deleteGuest({{ $guest->id }})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-4">
                            <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                            <p class="text-muted">Tidak ada data tamu</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($guests->hasPages())
        <div class="mt-3">
            {{ $guests->links() }}
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function deleteGuest(guestId) {
    if (!confirm('Apakah Anda yakin ingin menghapus tamu ini?')) {
        return;
    }

    $.ajax({
        url: `/admin/guest/${guestId}`,
        method: 'DELETE',
        success: function(response) {
            alert(response.message);
            location.reload();
        },
        error: function(xhr) {
            alert(xhr.responseJSON?.message || 'Terjadi kesalahan');
        }
    });
}
</script>
@endpush