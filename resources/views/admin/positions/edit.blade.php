@extends('layouts.app')

@section('title', 'Edit Jabatan')
@section('page-title', 'Edit Jabatan')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="table-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-edit me-2"></i>Form Edit Jabatan
                    </h5>
                    <a href="{{ route('admin.positions.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Kembali
                    </a>
                </div>

                <form action="{{ route('admin.positions.update', $position->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Jabatan <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                               value="{{ old('name', $position->name) }}" placeholder="Contoh: Hakim Tinggi" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Urutan <span class="text-danger">*</span></label>
                        <input type="number" name="order" class="form-control @error('order') is-invalid @enderror" 
                               value="{{ old('order', $position->order) }}" min="1" required>
                        @error('order')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror>
                        <small class="text-muted">Urutan tampilan (1 = paling atas)</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Tampil di Halaman Publik <span class="text-danger">*</span></label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="show_in_public" id="show_yes" value="1" 
                                   {{ old('show_in_public', $position->show_in_public) == '1' ? 'checked' : '' }} required>
                            <label class="form-check-label" for="show_yes">
                                <span class="badge bg-success">Ya, Tampilkan</span>
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="show_in_public" id="show_no" value="0" 
                                   {{ old('show_in_public', $position->show_in_public) == '0' ? 'checked' : '' }}>
                            <label class="form-check-label" for="show_no">
                                <span class="badge bg-secondary">Tidak, Sembunyikan</span>
                            </label>
                        </div>
                        <small class="text-muted">Pegawai dengan jabatan ini akan tampil/tidak di halaman publik</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Deskripsi</label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror" 
                                  rows="3" placeholder="Deskripsi jabatan (opsional)">{{ old('description', $position->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="is_active" id="active" value="1" 
                                   {{ old('is_active', $position->is_active) == '1' ? 'checked' : '' }} required>
                            <label class="form-check-label" for="active">
                                <span class="badge bg-success">Aktif</span>
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="is_active" id="inactive" value="0" 
                                   {{ old('is_active', $position->is_active) == '0' ? 'checked' : '' }}>
                            <label class="form-check-label" for="inactive">
                                <span class="badge bg-secondary">Nonaktif</span>
                            </label>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save me-2"></i>Update Jabatan
                        </button>
                        <a href="{{ route('admin.positions.index') }}" class="btn btn-outline-secondary">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection