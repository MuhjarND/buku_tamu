@extends('layouts.app')

@section('title', 'Verifikasi Tamu')
@section('page-title', 'Verifikasi Tamu')

@section('content')
<div class="row mb-4">
    <!-- Statistik Cards -->
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon bg-warning bg-opacity-10 text-warning me-3">
                    <i class="fas fa-clock"></i>
                </div>
                <div>
                    <h3 class="mb-0 fw-bold">{{ $statistics['pending'] }}</h3>
                    <small class="text-muted">Menunggu</small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon bg-success bg-opacity-10 text-success me-3">
                    <i class="fas fa-check"></i>
                </div>
                <div>
                    <h3 class="mb-0 fw-bold">{{ $statistics['verified'] }}</h3>
                    <small class="text-muted">Terverifikasi</small>
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
                    <small class="text-muted">Bertemu</small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary me-3">
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
    <form action="{{ route('receptionist.guests.index') }}" method="GET" class="row g-3">
        <div class="col-md-4">
            <label class="form-label fw-bold">Status</label>
            <select name="status" class="form-select" onchange="this.form.submit()">
                <option value="all" {{ $status == 'all' ? 'selected' : '' }}>Semua Status</option>
                <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                <option value="verified" {{ $status == 'verified' ? 'selected' : '' }}>Terverifikasi</option>
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

<!-- Tabel Tamu -->
<div class="table-card">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0 fw-bold">
            <i class="fas fa-list me-2"></i>Daftar Tamu
        </h5>
        @if($status == 'pending')
            <span class="badge bg-warning">
                <i class="fas fa-clock me-1"></i>{{ $guests->total() }} Menunggu Verifikasi
            </span>
        @endif
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
                    <th>Pegawai Dituju</th>
                    <th>Status</th>
                    <th>Waktu</th>
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
                                <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #1B4332 0%, #1e3a8a 50%, #B8860B 100%);" 
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
                                <span class="badge bg-warning">
                                    <i class="fas fa-clock me-1"></i>Menunggu
                                </span>
                            @elseif($guest->status == 'verified')
                                <span class="badge bg-success">
                                    <i class="fas fa-check me-1"></i>Terverifikasi
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
                            <small>{{ \Carbon\Carbon::parse($guest->created_at)->format('d/m/Y H:i') }}</small>
                        </td>
                        <td>
                            <div class="btn-group">
                                <a href="{{ route('receptionist.guest.show', $guest->id) }}" 
                                   class="btn btn-sm btn-info text-white">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($guest->status == 'pending')
                                    <button class="btn btn-sm btn-success" 
                                            onclick="verifyGuest({{ $guest->id }})">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" 
                                            onclick="rejectGuest({{ $guest->id }})">
                                        <i class="fas fa-times"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center py-4">
                            <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                            <p class="text-muted">Tidak ada data tamu</p>
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

<!-- Modal Reject -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tolak Verifikasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Masukkan alasan penolakan:</p>
                <textarea id="reject-reason" class="form-control" rows="3" 
                          placeholder="Contoh: Data tidak lengkap, Foto tidak jelas, dll"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" onclick="confirmReject()">Tolak</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentGuestId = null;
const rejectModal = new bootstrap.Modal(document.getElementById('rejectModal'));

function verifyGuest(guestId) {
    if (!confirm('Apakah Anda yakin ingin memverifikasi tamu ini? Notifikasi akan dikirim ke pegawai dan tamu.')) {
        return;
    }

    $.ajax({
        url: `/receptionist/guest/${guestId}/verify`,
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

function rejectGuest(guestId) {
    currentGuestId = guestId;
    $('#reject-reason').val('');
    rejectModal.show();
}

function confirmReject() {
    const reason = $('#reject-reason').val().trim();
    
    if (!reason) {
        alert('Mohon masukkan alasan penolakan');
        return;
    }

    $.ajax({
        url: `/receptionist/guest/${currentGuestId}/reject`,
        method: 'POST',
        data: { reason: reason },
        beforeSend: function() {
            $('button').prop('disabled', true);
        },
        success: function(response) {
            rejectModal.hide();
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