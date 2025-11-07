@extends('layouts.app')

@section('title', 'Kelola Jabatan')
@section('page-title', 'Kelola Jabatan')

@section('content')
<div class="table-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="mb-0 fw-bold">
            <i class="fas fa-briefcase me-2"></i>Daftar Jabatan
        </h5>
        <a href="{{ route('admin.positions.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Tambah Jabatan
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th width="50">No</th>
                    <th>Nama Jabatan</th>
                    <th width="100">Urutan</th>
                    <th>Tampil di Publik</th>
                    <th>Deskripsi</th>
                    <th width="100">Status</th>
                    <th width="150">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($positions as $index => $position)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <div class="fw-bold">{{ $position->name }}</div>
                        </td>
                        <td>
                            <span class="badge bg-secondary">{{ $position->order }}</span>
                        </td>
                        <td>
                            @if($position->show_in_public)
                                <span class="badge bg-success">
                                    <i class="fas fa-eye me-1"></i>Ya
                                </span>
                            @else
                                <span class="badge bg-secondary">
                                    <i class="fas fa-eye-slash me-1"></i>Tidak
                                </span>
                            @endif
                        </td>
                        <td>
                            <small class="text-muted">{{ $position->description ?? '-' }}</small>
                        </td>
                        <td>
                            @if($position->is_active)
                                <span class="badge bg-success">
                                    <i class="fas fa-check-circle me-1"></i>Aktif
                                </span>
                            @else
                                <span class="badge bg-secondary">
                                    <i class="fas fa-times-circle me-1"></i>Nonaktif
                                </span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group">
                                <a href="{{ route('admin.positions.edit', $position->id) }}" 
                                   class="btn btn-sm btn-warning text-white" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-sm btn-danger" 
                                        onclick="deletePosition({{ $position->id }})" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                            <p class="text-muted">Tidak ada data jabatan</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
function deletePosition(positionId) {
    if (!confirm('Apakah Anda yakin ingin menghapus jabatan ini?')) {
        return;
    }

    $.ajax({
        url: `/admin/positions/${positionId}`,
        method: 'DELETE',
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