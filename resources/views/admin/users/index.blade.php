@extends('layouts.app')

@section('title', 'Kelola User')
@section('page-title', 'Kelola User')

@section('content')
<div class="row mb-4">
    <!-- Statistik Cards -->
    <div class="col-lg-2 col-md-4 mb-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="stat-icon bg-danger bg-opacity-10 text-danger mb-2">
                    <i class="fas fa-user-shield"></i>
                </div>
                <h4 class="mb-0 fw-bold">{{ $statistics['admin'] }}</h4>
                <small class="text-muted">Admin</small>
            </div>
        </div>
    </div>

    <div class="col-lg-2 col-md-4 mb-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="stat-icon bg-warning bg-opacity-10 text-warning mb-2">
                    <i class="fas fa-user-check"></i>
                </div>
                <h4 class="mb-0 fw-bold">{{ $statistics['receptionist'] }}</h4>
                <small class="text-muted">Resepsionis</small>
            </div>
        </div>
    </div>

    <div class="col-lg-2 col-md-4 mb-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="stat-icon bg-info bg-opacity-10 text-info mb-2">
                    <i class="fas fa-user-tie"></i>
                </div>
                <h4 class="mb-0 fw-bold">{{ $statistics['employee'] }}</h4>
                <small class="text-muted">Pegawai</small>
            </div>
        </div>
    </div>

    <div class="col-lg-2 col-md-4 mb-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="stat-icon bg-success bg-opacity-10 text-success mb-2">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h4 class="mb-0 fw-bold">{{ $statistics['active'] }}</h4>
                <small class="text-muted">Aktif</small>
            </div>
        </div>
    </div>

    <div class="col-lg-2 col-md-4 mb-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="stat-icon bg-secondary bg-opacity-10 text-secondary mb-2">
                    <i class="fas fa-times-circle"></i>
                </div>
                <h4 class="mb-0 fw-bold">{{ $statistics['inactive'] }}</h4>
                <small class="text-muted">Nonaktif</small>
            </div>
        </div>
    </div>

    <div class="col-lg-2 col-md-4 mb-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary mb-2">
                    <i class="fas fa-users"></i>
                </div>
                <h4 class="mb-0 fw-bold">{{ $statistics['total'] }}</h4>
                <small class="text-muted">Total User</small>
            </div>
        </div>
    </div>
</div>

<!-- Filter dan Search -->
<div class="table-card mb-4">
    <form action="{{ route('admin.users.index') }}" method="GET" class="row g-3">
        <div class="col-md-4">
            <label class="form-label fw-bold">Role</label>
            <select name="role" class="form-select" onchange="this.form.submit()">
                <option value="all" {{ $role == 'all' ? 'selected' : '' }}>Semua Role</option>
                <option value="admin" {{ $role == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="receptionist" {{ $role == 'receptionist' ? 'selected' : '' }}>Resepsionis</option>
                <option value="employee" {{ $role == 'employee' ? 'selected' : '' }}>Pegawai</option>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label fw-bold">Pencarian</label>
            <input type="text" name="search" class="form-control" placeholder="Cari nama, email, atau telepon..." value="{{ $search }}">
        </div>
        <div class="col-md-2">
            <label class="form-label fw-bold">&nbsp;</label>
            <button type="submit" class="btn btn-primary w-100">
                <i class="fas fa-search me-2"></i>Cari
            </button>
        </div>
    </form>
</div>

<!-- Tabel User -->
<div class="table-card">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0 fw-bold">
            <i class="fas fa-list me-2"></i>Daftar User
        </h5>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Tambah User
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Telepon</th>
                    <th>Jabatan</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Dibuat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>{{ $users->firstItem() + $loop->index }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                @if($user->photo)
                                    <img src="{{ asset('storage/' . $user->photo) }}" 
                                         class="rounded-circle me-2" 
                                         style="width: 50px; height: 50px; object-fit: cover;">
                                @else
                                    <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);" 
                                         class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold me-2">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                @endif
                                <div>
                                    <div class="fw-bold">{{ $user->name }}</div>
                                    @if($user->id == auth()->id())
                                        <span class="badge bg-primary">Anda</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->phone }}</td>
                        <td>
                            @if($user->role == 'employee' && $user->position)
                                <div>
                                    <span class="badge bg-info">{{ $user->position }}</span>
                                    @if($user->keterangan)
                                        <br><small class="text-muted">{{ $user->keterangan }}</small>
                                    @endif
                                </div>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($user->role == 'admin')
                                <span class="badge bg-danger">
                                    <i class="fas fa-user-shield me-1"></i>Admin
                                </span>
                            @elseif($user->role == 'receptionist')
                                <span class="badge bg-warning">
                                    <i class="fas fa-user-check me-1"></i>Resepsionis
                                </span>
                            @elseif($user->role == 'employee')
                                <span class="badge bg-info">
                                    <i class="fas fa-user-tie me-1"></i>Pegawai
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($user->is_active)
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
                            <small>{{ \Carbon\Carbon::parse($user->created_at)->format('d/m/Y') }}</small>
                        </td>
                        <td>
                            <div class="btn-group">
                                <a href="{{ route('admin.users.edit', $user->id) }}" 
                                   class="btn btn-sm btn-warning text-white" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if($user->id != auth()->id())
                                    <button class="btn btn-sm btn-danger" 
                                            onclick="deleteUser({{ $user->id }})" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-4">
                            <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                            <p class="text-muted">Tidak ada data user</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($users->hasPages())
        <div class="mt-3">
            {{ $users->links() }}
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function deleteUser(userId) {
    if (!confirm('Apakah Anda yakin ingin menghapus user ini?')) {
        return;
    }

    $.ajax({
        url: `/admin/users/${userId}`,
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