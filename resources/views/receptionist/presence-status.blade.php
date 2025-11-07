@extends('layouts.app')

@section('title', 'Status Kehadiran Pegawai')
@section('page-title', 'Status Kehadiran Pegawai')

@section('content')
<div class="table-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="mb-0 fw-bold">
            <i class="fas fa-user-check me-2"></i>Kelola Status Kehadiran
        </h5>
        <div>
            <span class="badge bg-success me-2">
                <i class="fas fa-circle me-1"></i>Ada: {{ $employees->where('presence_status', 'ada')->count() }}
            </span>
            <span class="badge bg-warning">
                <i class="fas fa-circle me-1"></i>Keluar: {{ $employees->where('presence_status', 'keluar')->count() }}
            </span>
        </div>
    </div>

    <div class="alert alert-info">
        <i class="fas fa-info-circle me-2"></i>
        Status kehadiran akan ditampilkan kepada tamu saat memilih pegawai yang ingin ditemui.
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th width="50">No</th>
                    <th>Nama Pegawai</th>
                    <th>Jabatan</th>
                    <th>Telepon</th>
                    <th>Status Kehadiran</th>
                    <th>Terakhir Update</th>
                    <th width="150">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($employees as $index => $employee)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #1B4332 0%, #1e3a8a 50%, #B8860B 100%);" 
                                     class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold me-2">
                                    {{ strtoupper(substr($employee->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-bold">{{ $employee->name }}</div>
                                    <small class="text-muted">{{ $employee->email }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-info">{{ $employee->position ?? 'Pegawai' }}</span>
                        </td>
                        <td>{{ $employee->phone }}</td>
                        <td>
                            <div id="status-badge-{{ $employee->id }}">
                                @if($employee->presence_status == 'ada')
                                    <span class="badge bg-success">
                                        <i class="fas fa-check-circle me-1"></i>Ada
                                    </span>
                                @else
                                    <span class="badge bg-warning">
                                        <i class="fas fa-sign-out-alt me-1"></i>Sedang Keluar
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <small id="last-update-{{ $employee->id }}">
                                @if($employee->presence_updated_at)
                                    {{ \Carbon\Carbon::parse($employee->presence_updated_at)->diffForHumans() }}
                                @else
                                    -
                                @endif
                            </small>
                        </td>
                        <td>
                            <div class="btn-group">
                                @if($employee->presence_status == 'ada')
                                    <button class="btn btn-sm btn-warning" 
                                            onclick="updateStatus({{ $employee->id }}, 'keluar')"
                                            title="Tandai Keluar">
                                        <i class="fas fa-sign-out-alt me-1"></i>Keluar
                                    </button>
                                @else
                                    <button class="btn btn-sm btn-success" 
                                            onclick="updateStatus({{ $employee->id }}, 'ada')"
                                            title="Tandai Ada">
                                        <i class="fas fa-sign-in-alt me-1"></i>Ada
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
function updateStatus(employeeId, status) {
    const statusText = status === 'ada' ? 'Ada' : 'Keluar';
    
    if (!confirm(`Apakah Anda yakin mengubah status menjadi "${statusText}"?`)) {
        return;
    }

    $.ajax({
        url: `/receptionist/employee/${employeeId}/presence-status`,
        method: 'POST',
        data: { status: status },
        beforeSend: function() {
            $('button').prop('disabled', true);
        },
        success: function(response) {
            // Update badge
            const badgeHtml = status === 'ada' 
                ? '<span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Ada</span>'
                : '<span class="badge bg-warning"><i class="fas fa-sign-out-alt me-1"></i>Sedang Keluar</span>';
            $(`#status-badge-${employeeId}`).html(badgeHtml);
            
            // Update last update time
            $(`#last-update-${employeeId}`).text('Baru saja');
            
            // Reload page after 1 second
            setTimeout(function() {
                location.reload();
            }, 1000);
        },
        error: function(xhr) {
            alert(xhr.responseJSON?.message || 'Terjadi kesalahan');
            $('button').prop('disabled', false);
        }
    });
}
</script>
@endpush