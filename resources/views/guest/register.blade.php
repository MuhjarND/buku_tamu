<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku Tamu - Registrasi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #1B4332 0%, #1e3a8a 50%, #B8860B 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .card {
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            border: none;
        }
        .card-header {
            background: linear-gradient(135deg, #1B4332 0%, #1e3a8a 50%, #B8860B 100%);
            color: white;
            border-radius: 20px 20px 0 0 !important;
            padding: 2rem;
        }
        .btn-primary {
            background: linear-gradient(135deg, #1B4332 0%, #1e3a8a 50%, #B8860B 100%);
            border: none;
            padding: 12px 30px;
            border-radius: 10px;
            font-weight: 600;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }
        .form-control, .form-select {
            border-radius: 10px;
            padding: 12px;
            border: 2px solid #e0e0e0;
        }
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .camera-preview {
            border: 3px dashed #667eea;
            border-radius: 15px;
            min-height: 250px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8f9fa;
            position: relative;
            overflow: hidden;
        }
        #preview {
            max-width: 100%;
            max-height: 300px;
            border-radius: 10px;
        }
        .select2-container--bootstrap-5 .select2-selection {
            border-radius: 10px !important;
            border: 2px solid #e0e0e0 !important;
            min-height: 48px !important;
        }
        .select2-results__option[data-status="keluar"] {
            background-color: #fff3cd !important;
            color: #856404 !important;
        }
        .select2-results__option[data-status="ada"] {
            background-color: #d1e7dd !important;
            color: #0f5132 !important;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header text-center">
                        <h2 class="mb-0"><i class="fas fa-clipboard-user me-2"></i>Buku Tamu Digital</h2>
                        <p class="mb-0 mt-2">Silakan isi data Anda dengan lengkap</p>
                        <a href="{{ route('employees.list') }}" class="btn btn-sm btn-light mt-2" style="border-radius: 20px;">
                            <i class="fas fa-users me-1"></i>Lihat Daftar Pegawai
                        </a>
                    </div>
                    <div class="card-body p-4">
                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form action="{{ route('guest.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="mb-4">
                                <label class="form-label fw-bold"><i class="fas fa-user me-2"></i>Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                       value="{{ old('name') }}" placeholder="Masukkan nama lengkap" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold"><i class="fas fa-phone me-2"></i>Nomor Telepon <span class="text-danger">*</span></label>
                                <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" 
                                       value="{{ old('phone') }}" placeholder="Contoh: 081234567890" required>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold"><i class="fas fa-envelope me-2"></i>Email</label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                       value="{{ old('email') }}" placeholder="email@example.com">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold"><i class="fas fa-building me-2"></i>Perusahaan/Instansi</label>
                                <input type="text" name="company" class="form-control @error('company') is-invalid @enderror" 
                                       value="{{ old('company') }}" placeholder="Nama perusahaan/instansi">
                                @error('company')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold"><i class="fas fa-clipboard-list me-2"></i>Keperluan <span class="text-danger">*</span></label>
                                <textarea name="purpose" rows="3" class="form-control @error('purpose') is-invalid @enderror" 
                                          placeholder="Jelaskan keperluan kunjungan Anda" required>{{ old('purpose') }}</textarea>
                                @error('purpose')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold"><i class="fas fa-users me-2"></i>Pegawai yang Dituju <span class="text-danger">*</span></label>
                                <select name="employee_ids[]" class="form-select @error('employee_ids') is-invalid @enderror" 
                                        id="employee-select" multiple="multiple" required>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}" 
                                                data-status="{{ $employee->presence_status }}"
                                                data-position="{{ $employee->position }}"
                                                {{ in_array($employee->id, old('employee_ids', [])) ? 'selected' : '' }}>
                                            {{ $employee->name }} - {{ $employee->position }} 
                                            @if($employee->presence_status == 'keluar')
                                                (Sedang Keluar)
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('employee_ids')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Anda dapat memilih lebih dari satu pegawai</small>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold"><i class="fas fa-camera me-2"></i>Foto</label>
                                <div class="camera-preview mb-3" id="preview-container">
                                    <img id="preview" style="display: none;">
                                    <div id="placeholder">
                                        <i class="fas fa-camera fa-3x text-muted"></i>
                                        <p class="text-muted mt-2">Ambil atau Upload Foto</p>
                                    </div>
                                </div>
                                <div class="d-grid gap-2">
                                    <button type="button" class="btn btn-outline-primary" id="start-camera">
                                        <i class="fas fa-camera me-2"></i>Buka Kamera
                                    </button>
                                    <input type="file" name="photo" id="photo-upload" accept="image/*" 
                                           class="form-control @error('photo') is-invalid @enderror" style="display: none;">
                                    <button type="button" class="btn btn-outline-secondary" id="upload-photo">
                                        <i class="fas fa-upload me-2"></i>Upload Foto
                                    </button>
                                </div>
                                @error('photo')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-grid gap-2 mt-4">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-paper-plane me-2"></i>Kirim Data
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Camera Modal -->
    <div class="modal fade" id="cameraModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ambil Foto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <video id="camera-stream" autoplay playsinline style="max-width: 100%; border-radius: 10px;"></video>
                    <canvas id="camera-canvas" style="display: none;"></canvas>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="capture-photo">
                        <i class="fas fa-camera me-2"></i>Ambil Foto
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Inisialisasi Select2
            $('#employee-select').select2({
                theme: 'bootstrap-5',
                placeholder: 'Pilih pegawai yang ingin ditemui',
                allowClear: true,
                templateResult: formatEmployee,
                templateSelection: formatEmployeeSelection
            });

            // Format tampilan dropdown
            function formatEmployee(employee) {
                if (!employee.id) return employee.text;
                
                const $employee = $(employee.element);
                const status = $employee.data('status');
                const position = $employee.data('position');
                
                let badge = '';
                if (status === 'keluar') {
                    badge = '<span class="badge bg-warning ms-2">Sedang Keluar</span>';
                } else {
                    badge = '<span class="badge bg-success ms-2">Ada</span>';
                }
                
                return $(`<div>${employee.text} ${badge}</div>`);
            }

            // Format tampilan selected item
            function formatEmployeeSelection(employee) {
                return employee.text;
            }

            let stream = null;
            const cameraModal = new bootstrap.Modal(document.getElementById('cameraModal'));
            const video = document.getElementById('camera-stream');
            const canvas = document.getElementById('camera-canvas');
            const preview = document.getElementById('preview');
            const placeholder = document.getElementById('placeholder');
            const photoUpload = document.getElementById('photo-upload');

            // Buka kamera
            $('#start-camera').click(async function() {
                try {
                    stream = await navigator.mediaDevices.getUserMedia({ 
                        video: { facingMode: 'user' } 
                    });
                    video.srcObject = stream;
                    cameraModal.show();
                } catch (err) {
                    alert('Tidak dapat mengakses kamera: ' + err.message);
                }
            });

            // Ambil foto
            $('#capture-photo').click(function() {
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                canvas.getContext('2d').drawImage(video, 0, 0);
                
                canvas.toBlob(function(blob) {
                    const file = new File([blob], 'photo.jpg', { type: 'image/jpeg' });
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    photoUpload.files = dataTransfer.files;
                    
                    // Tampilkan preview
                    preview.src = URL.createObjectURL(blob);
                    preview.style.display = 'block';
                    placeholder.style.display = 'none';
                });

                // Stop camera
                if (stream) {
                    stream.getTracks().forEach(track => track.stop());
                }
                cameraModal.hide();
            });

            // Upload foto
            $('#upload-photo').click(function() {
                photoUpload.click();
            });

            photoUpload.addEventListener('change', function(e) {
                if (e.target.files && e.target.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.src = e.target.result;
                        preview.style.display = 'block';
                        placeholder.style.display = 'none';
                    };
                    reader.readAsDataURL(e.target.files[0]);
                }
            });

            // Stop camera saat modal ditutup
            $('#cameraModal').on('hidden.bs.modal', function() {
                if (stream) {
                    stream.getTracks().forEach(track => track.stop());
                }
            });
        });
    </script>
</body>
</html>