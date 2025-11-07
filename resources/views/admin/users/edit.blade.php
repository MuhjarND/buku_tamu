@extends('layouts.app')

@section('title', 'Edit User')
@section('page-title', 'Edit User')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="table-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-user-edit me-2"></i>Form Edit User
                    </h5>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Kembali
                    </a>
                </div>

                <form action="{{ route('admin.users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                               value="{{ old('name', $user->name) }}" placeholder="Masukkan nama lengkap" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Foto Profil</label>
                        @if($user->photo)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $user->photo) }}" style="max-width: 200px; border-radius: 10px;">
                                <p class="text-muted small mb-0">Foto saat ini</p>
                            </div>
                        @endif
                        <input type="file" name="photo" id="photo" class="form-control @error('photo') is-invalid @enderror" accept="image/*">
                        @error('photo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Upload foto baru untuk mengganti (Format: JPG, PNG. Maksimal 2MB)</small>
                        <div class="mt-2" id="preview-container" style="display: none;">
                            <img id="photo-preview" src="" style="max-width: 200px; border-radius: 10px;">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                               value="{{ old('email', $user->email) }}" placeholder="email@example.com" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Nomor Telepon <span class="text-danger">*</span></label>
                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" 
                               value="{{ old('phone', $user->phone) }}" placeholder="081234567890" required>
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Password:</strong> Kosongkan jika tidak ingin mengubah password
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Password Baru</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                               placeholder="Minimal 6 karakter (kosongkan jika tidak diubah)">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" class="form-control" 
                               placeholder="Ulangi password baru">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Role <span class="text-danger">*</span></label>
                        <select name="role" class="form-select @error('role') is-invalid @enderror" id="role-select" required>
                            <option value="">Pilih Role</option>
                            <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="receptionist" {{ old('role', $user->role) == 'receptionist' ? 'selected' : '' }}>Resepsionis</option>
                            <option value="employee" {{ old('role', $user->role) == 'employee' ? 'selected' : '' }}>Pegawai</option>
                        </select>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div id="employee-fields" style="display: {{ old('role', $user->role) == 'employee' ? 'block' : 'none' }};">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Jabatan</label>
                            <select name="position" class="form-select" id="position-select">
                                <option value="">Pilih Jabatan</option>
                                @php
                                    $positions = DB::table('positions')->where('is_active', true)->orderBy('order')->get();
                                @endphp
                                @foreach($positions as $pos)
                                    <option value="{{ $pos->name }}" data-order="{{ $pos->order }}">
                                        {{ $pos->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="is_active" id="active" value="1" 
                                   {{ old('is_active', $user->is_active) == '1' ? 'checked' : '' }} required>
                            <label class="form-check-label" for="active">
                                <span class="badge bg-success">Aktif</span>
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="is_active" id="inactive" value="0" 
                                   {{ old('is_active', $user->is_active) == '0' ? 'checked' : '' }}>
                            <label class="form-check-label" for="inactive">
                                <span class="badge bg-secondary">Nonaktif</span>
                            </label>
                        </div>
                        @error('is_active')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save me-2"></i>Update User
                        </button>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Preview foto
document.getElementById('photo').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('photo-preview').src = e.target.result;
            document.getElementById('preview-container').style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
});

// Toggle employee fields
document.getElementById('role-select').addEventListener('change', function() {
    const employeeFields = document.getElementById('employee-fields');
    if (this.value === 'employee') {
        employeeFields.style.display = 'block';
    } else {
        employeeFields.style.display = 'none';
    }
});

// Auto set position_order based on position
document.getElementById('position-select').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const order = selectedOption.getAttribute('data-order');
    document.querySelector('input[name="position_order"]').value = order || 999;
});
</script>
@endpush