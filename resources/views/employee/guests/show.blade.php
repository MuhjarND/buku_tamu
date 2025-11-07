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

                @if($guest->status == 'verified')
                    <span class="badge bg-success mb-3" style="font-size: 1rem; padding: 0.5rem 1.5rem;">
                        <i class="fas fa-check-circle me-1"></i>Menunggu Pertemuan
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

                @if($guest->status == 'verified')
                    <div class="d-grid gap-2 mt-3">
                        <button class="btn btn-success btn-lg" onclick="startMeeting({{ $guest->id }})">
                            <i class="fas fa-play me-2"></i>Mulai Pertemuan
                        </button>
                    </div>
                @elseif($guest->status == 'meeting')
                    <div class="d-grid gap-2 mt-3">
                        <button class="btn btn-primary btn-lg" onclick="checkoutGuest({{ $guest->id }})">
                            <i class="fas fa-sign-out-alt me-2"></i>Checkout Tamu
                        </button>
                    </div>
                @endif

                <a href="{{ route('employee.guests.index') }}" class="btn btn-secondary mt-2 w-100">
                    <i class="fas fa-arrow-left me-2"></i>Kembali
                </a>
            </div>
        </div>

        <!-- Timeline Status -->
        <div class="card stat-card mt-3">
            <div class="card-body">
                <h6 class="fw-bold mb-3">
                    <i class="fas fa-clock me-2"></i>Timeline
                </h6>
                
                <div class="timeline">
                    <!-- Check In -->
                    <div class="timeline-item mb-3">
                        <div class="d-flex align-items-start">
                            <div class="timeline-icon bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 30px; height: 30px; min-width: 30px;">
                                <i class="fas fa-sign-in-alt fa-sm"></i>
                            </div>
                            <div>
                                <small class="text-muted">Check In</small>
                                <div class="fw-bold">
                                    {{ \Carbon\Carbon::parse($guest->check_in_time)->format('d M Y, H:i') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Verified -->
                    @if($guest->verified_at)
                        <div class="timeline-item mb-3">
                            <div class="d-flex align-items-start">
                                <div class="timeline-icon bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 30px; height: 30px; min-width: 30px;">
                                    <i class="fas fa-check fa-sm"></i>
                                </div>
                                <div>
                                    <small class="text-muted">Terverifikasi</small>
                                    <div class="fw-bold">
                                        {{ \Carbon\Carbon::parse($guest->verified_at)->format('d M Y, H:i') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Check Out -->
                    @if($guest->check_out_time)
                        <div class="timeline-item">
                            <div class="d-flex align-items-start">
                                <div class="timeline-icon bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 30px; height: 30px; min-width: 30px;">
                                    <i class="fas fa-sign-out-alt fa-sm"></i>
                                </div>
                                <div>
                                    <small class="text-muted">Check Out</small>
                                    <div class="fw-bold">
                                        {{ \Carbon\Carbon::parse($guest->check_out_time)->format('d M Y, H:i') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="timeline-item">
                            <div class="d-flex align-items-start">
                                <div class="timeline-icon bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 30px; height: 30px; min-width: 30px;">
                                    <i class="fas fa-clock fa-sm"></i>
                                </div>
                                <div>
                                    <small class="text-muted">Check Out</small>
                                    <div class="text-muted">Belum checkout</div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
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
                        <div class="alert alert-light border">
                            {{ $guest->purpose }}
                        </div>
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
                            <div class="d-flex align-items-center p-3 border rounded {{ $employee->id == Auth::id() ? 'bg-light' : '' }}">
                                <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #1B4332 0%, #1e3a8a 50%, #B8860B 100%);" 
                                     class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold me-3">
                                    {{ strtoupper(substr($employee->name, 0, 1)) }}
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-bold">{{ $employee->name }}</div>
                                    <small class="text-muted">{{ $employee->email }}</small>
                                    @if($employee->id == Auth::id())
                                        <div>
                                            <span class="badge bg-primary mt-1">
                                                <i class="fas fa-user me-1"></i>Anda
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
                            <small class="text-muted">{{ ucfirst($verifiedBy->role) }}</small>
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
            window.location.href = '{{ route("employee.guests.index") }}';
        },
        error: function(xhr) {
            alert(xhr.responseJSON?.message || 'Terjadi kesalahan');
            $('button').prop('disabled', false);
        }
    });
}
</script>
@endpush