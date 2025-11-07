@extends('layouts.app')

@section('title', 'Detail Tamu')
@section('page-title', 'Detail Tamu')

@section('content')
<div class="row">
    <div class="col-lg-4 mb-4">
        <!-- Info Tamu Card -->
        <div class="card stat-card">
            <div class="card-body text-center">
                @if($guest->photo)
                    <img src="{{ asset('storage/' . $guest->photo) }}" 
                         class="rounded-circle mb-3" width="150" height="150" 
                         style="object-fit: cover;">
                @else
                    <div style="width: 150px; height: 150px; background: linear-gradient(135deg, #1B4332 0%, #1e3a8a 50%, #B8860B 100%);" 
                         class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold fs-1 mx-auto mb-3">
                        {{ strtoupper(substr($guest->name, 0, 1)) }}
                    </div>
                @endif

                <h4 class="fw-bold mb-1">{{ $guest->name }}</h4>
                <p class="text-muted mb-3">{{ $guest->company ?? 'Perorangan' }}</p>

                @if($guest->status == 'pending')
                    <span class="badge bg-warning mb-3" style="font-size: 1rem; padding: 0.5rem 1.5rem;">
                        <i class="fas fa-clock me-1"></i>Menunggu Verifikasi
                    </span>
                @elseif($guest->status == 'verified')
                    <span class="badge bg-success mb-3" style="font-size: 1rem; padding: 0.5rem 1.5rem;">
                        <i class="fas fa-check me-1"></i>Terverifikasi
                    </span>
                @elseif($guest->status == 'meeting')
                    <span class="badge bg-info mb-3" style="font-size: 1rem; padding: 0.5rem 1.5rem;">
                        <i class="fas fa-handshake me-1"></i>Sedang Bertemu
                    </span>
                @elseif($guest->status == 'completed')
                    <span class="badge bg-primary mb-3" style="font-size: 1rem; padding: 0.5rem 1.5rem;">
                        <i class="fas fa-check-double me-1"></i>Selesai
                    </span>
                @endif

                @if($guest->status == 'pending')
                    <div class="d-grid gap-2 mt-3">
                        <button class="btn btn-success btn-lg" onclick="verifyGuest({{ $guest->id }})">
                            <i class="fas fa-check me-2"></i>Verifikasi Tamu
                        </button>
                        <button class="btn btn-danger" onclick="rejectGuest({{ $guest->id }})">
                            <i class="fas fa-times me-2"></i>Tolak
                        </button>
                    </div>
                @endif

                <a href="{{ route('receptionist.guests.index') }}" class="btn btn-secondary mt-2 w-100">
                    <i class="fas fa-arrow-left me-2"></i>Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <!-- Detail Informasi -->
        <div class="card stat-card mb-4">
            <div class="card-body">
                <h5 class="fw-bold mb-4">
                    <i class="fas fa-info-circle me-2"></i>Informasi Detail
                </h5>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="text-muted small">Nama Lengkap</label>
                        <p class="fw-bold mb-0">{{ $guest->name }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">Nomor Telepon</label>
                        <p class="fw-bold mb-0">{{ $guest->phone }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">Email</label>
                        <p class="fw-bold mb-0">{{ $guest->email ?? '-' }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">Perusahaan/Instansi</label>
                        <p class="fw-bold mb-0">{{ $guest->company ?? '-' }}</p>
                    </div>
                    <div class="col-12">
                        <label class="text-muted small">Keperluan</label>
                        <p class="fw-bold mb-0">{{ $guest->purpose }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">Waktu Check-in</label>
                        <p class="fw-bold mb-0">
                            @if($guest->check_in_time)
                                {{ \Carbon\Carbon::parse($guest->check_in_time)->format('d M Y, H:i') }} WIB
                            @else
                                -
                            @endif
                        </p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">Waktu Check-out</label>
                        <p class="fw-bold mb-0">
                            @if($guest->check_out_time)
                                {{ \Carbon\Carbon::parse($guest->check_out_time)->format('d M Y, H:i') }} WIB
                            @else
                                <span class="text-muted">Belum checkout</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pegawai yang Dituju -->
        <div class="card stat-card mb-4">
            <div class="card-body">
                <h5 class="fw-bold mb-4">
                    <i class="fas fa-users me-2"></i>Pegawai yang Dituju
                </h5>

                <div class="row g-3">
                    @foreach($employees as $employee)
                        <div class="col-md-6">
                            <div class="d-flex align-items-center p-3 border rounded">
                                <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #1B4332 0%, #1e3a8a 50%, #B8860B 100%);" 
                                     class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold me-3">
                                    {{ strtoupper(substr($employee->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-bold">{{ $employee->name }}</div>
                                    <small class="text-muted">{{ $employee->email }}</small>
                                    @if($employee->is_notified)
                                        <div>
                                            <span class="badge bg-success mt-1">
                                                <i class="fas fa-check-circle me-1"></i>Ternotifikasi
                                            </span>
                                        </div>
                                    @else
                                        <div>
                                            <span class="badge bg-secondary mt-1">
                                                Belum dinotifikasi
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Info Verifikasi -->
        @if($guest->verified_by && $verifiedBy)
            <div class="card stat-card">
                <div class="card-body">
                    <h5 class="fw-bold mb-4">
                        <i class="fas fa-user-check me-2"></i>Informasi Verifikasi
                    </h5>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="text-muted small">Diverifikasi Oleh</label>
                            <p class="fw-bold mb-0">{{ $verifiedBy->name }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Waktu Verifikasi</label>
                            <p class="fw-bold mb-0">
                                {{ \Carbon\Carbon::parse($guest->verified_at)->format('d M Y, H:i') }} WIB
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
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
let currentGuestId = {{ $guest->id }};
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
            window.location.href = '{{ route("receptionist.guests.index") }}';
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
            window.location.href = '{{ route("receptionist.guests.index") }}';
        },
        error: function(xhr) {
            alert(xhr.responseJSON?.message || 'Terjadi kesalahan');
            $('button').prop('disabled', false);
        }
    });
}
</script>
@endpush