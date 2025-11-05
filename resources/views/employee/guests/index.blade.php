@extends('layouts.app')

@section('title', 'Tamu Saya')
@section('page-title', 'Tamu Saya')

@section('content')
<div class="row mb-4">
    <!-- Statistik Cards -->
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon bg-success bg-opacity-10 text-success me-3">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div>
                    <h3 class="mb-0 fw-bold">{{ $statistics['verified'] }}</h3>
                    <small class="text-muted">Menunggu</small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon bg-info bg-opacity-10 text-info me-3">
                    <i class="fas fa-handshake"></i>
                </div>
                <div>
                    <h3 class="mb-0 fw-bold">{{ $statistics['meeting'] }}</h3>
                    <small class="text-muted">Sedang Bertemu</small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary me-3">
                    <i class="fas fa-check-double"></i>
                </div>
                <div>
                    <h3 class="mb-0 fw-bold">{{ $statistics['completed'] }}</h3>
                    <small class="text-muted">Selesai</small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon bg-warning bg-opacity-10 text-warning me-3">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <h3 class="mb-0 fw-bold">{{ $statistics['total'] }}</h3>
                    <small class="text-muted">Total Tamu</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter dan Search -->
<div class="table-card mb-4">
    <form action="{{ route('employee.guests.index') }}" method="GET" class="row g-3">
        <div class="col-md-4">
            <label class="form-label fw-bold">Status</label>
            <select name="status" class="form-select" onchange="this.form.submit()">
                <option value="all" {{ $status == 'all' ? 'selected' : '' }}>Semua Status</option>
                <option value="verified" {{ $status == 'verified' ? 'selected' : '' }}>Menunggu Pertemuan</option>
                <option value="meeting" {{ $status == 'meeting' ? 'selected' : '' }}>Sedang Bertemu</option>
                <option value="completed" {{ $status == 'completed' ? 'selected' : '' }}>Selesai</option>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label fw-bold">Pencarian</label>
            <input type="text" name="search" class="form-control" placeholder="Cari nama, telepon, atau perusahaan..." value="{{ $search }}">
        </div>
        <div class="col-md-2">
            <label class="form-label fw-bold">&nbsp;</label>
            <button type="submit" class="btn btn-primary w-100">
                <i class="fas fa-search me-2"></i>Cari
            </button>
        </div>
    </form>
</div>

<!-- Info Alert -->
@if($status == 'verified' && $guests->total() > 0)
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <i class="fas fa-info-circle me-2"></i>
        <strong>{{ $guests->total() }} tamu</strong> menunggu untuk bertemu dengan Anda. Silakan mulai pertemuan.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Tabel Tamu -->
<div class="table-card">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0 fw-bold">
            <i class="fas fa-list me-2"></i>Daftar Tamu
        </h5>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>Foto</th>
                    <th>Nama</th>
                    <th>Telepon</th>
                    <th>Perusahaan</th>
                    <th>Keperluan</th>
                    <th>Waktu Datang</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($guests as $guest)
                    <tr>
                        <td>{{ $guests->firstItem() + $loop->index }}</td>
                        <td>
                            @if($guest->photo)
                                <img src="{{ asset('storage/' . $guest->photo) }}" 
                                     class="rounded" width="50" height="50" 
                                     style="object-fit: cover;">
                            @else
                                <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);" 
                                     class="rounded d-flex align-items-center justify-content-center text-white fw-bold">
                                    {{ strtoupper(substr($guest->name, 0, 1)) }}
                                </div>
                            @endif
                        </td>
                        <td>
                            <div class="fw-bold">{{ $guest->name }}</div>
                            @if($guest->email)
                                <small class="text-muted">{{ $guest->email }}</small>
                            @endif
                        </td>
                        <td>{{ $guest->phone }}</td>
                        <td>{{ $guest->company ?? '-' }}</td>
                        <td>
                            <small>{{ Str::limit($guest->purpose, 50) }}</small>
                        </td>
                        <td>
                            <small>{{ \Carbon\Carbon::parse($guest->check_in_time)->format('d/m/Y H:i') }}</small>
                        </td>
                        <td>
                            @if($guest->status == 'verified')
                                <span class="badge bg-success">
                                    <i class="fas fa-check-circle me-1"></i>Menunggu
                                </span>
                            @elseif($guest->status == 'meeting')
                                <span class="badge bg-info">
                                    <i class="fas fa-handshake me-1"></i>Bertemu
                                </span>
                            @elseif($guest->status == 'completed')
                                <span class="badge bg-primary">
                                    <i class="fas fa-check-double me-1"></i>Selesai
                                </span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group">
                                <a href="{{ route('employee.guest.show', $guest->id) }}" 
                                   class="btn btn-sm btn-info text-white" title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($guest->status == 'verified')
                                    <button class="btn btn-sm btn-success" 
                                            onclick="startMeeting({{ $guest->id }})"
                                            title="Mulai Pertemuan">
                                        <i class="fas fa-play"></i>
                                    </button>
                                @elseif($guest->status == 'meeting')
                                    <button class="btn btn-sm btn-primary" 
                                            onclick="checkoutGuest({{ $guest->id }})"
                                            title="Checkout">
                                        <i class="fas fa-sign-out-alt"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-4">
                            <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                            <p class="text-muted">Belum ada tamu yang menuju Anda</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($guests->hasPages())
        <div class="mt-3">
            {{ $guests->links() }}
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function startMeeting(guestId) {
    if (!confirm('Apakah Anda sudah bertemu dengan tamu ini?')) {
        return;
    }

    $.ajax({
        url: `/employee/guest/${guestId}/start-meeting`,
        method: 'POST',
        beforeSend: function() {
            $('button').prop('disabled', true);
        },
        success: function(response) {
            alert(response.message);
            location.reload();
        },
        error: function(xhr) {
            alert(xhr.responseJSON?.message || 'Terjadi kesalahan');
            $('button').prop('disabled', false);
        }
    });
}

function checkoutGuest(guestId) {
    if (!confirm('Apakah pertemuan sudah selesai dan tamu akan checkout?')) {
        return;
    }

    $.ajax({
        url: `/employee/guest/${guestId}/checkout`,
        method: 'POST',
        beforeSend: function() {
            $('button').prop('disabled', true);
        },
        success: function(response) {
            alert(response.message);
            location.reload();
        },
        error: function(xhr) {
            alert(xhr.responseJSON?.message || 'Terjadi kesalahan');
            $('button').prop('disabled', false);
        }
    });
}
</script>
@endpush