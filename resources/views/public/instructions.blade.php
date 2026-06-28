<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Arahan untuk PTSP</title>
    @include('partials.app-icons')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1B4332 0%, #1e3a8a 50%, #B8860B 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        .card-app {
            border: none;
            border-radius: 16px;
            box-shadow: 0 12px 40px rgba(0,0,0,0.15);
            overflow: hidden;
        }
        .card-header-app {
            background: linear-gradient(135deg, #1B4332 0%, #1e3a8a 50%, #B8860B 100%);
            color: #fff;
            padding: 1.2rem 1.5rem;
        }
        .badge-app {
            background: rgba(255,255,255,0.2);
            border: 1px solid rgba(255,255,255,0.25);
            color: #fff;
            border-radius: 999px;
            padding: 0.35rem 0.75rem;   
            font-size: 0.85rem;
        }
        .card-body {
            background: #fff;
        }
        textarea { resize: vertical; }
        .info-label { font-size: 0.9rem; color: #6c757d; }
        .info-value { font-weight: 700; }
        @media (max-width: 576px) {
            body { padding: 0.75rem; }
            .card-header-app { text-align: center; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-7 col-md-9">
                <div class="card card-app">
                    <div class="card-header-app">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <h5 class="fw-bold mb-0">Arahan untuk PTSP</h5>
                                <small class="text-white-50">Berikan instruksi agar tamu ditangani sesuai kebutuhan</small>
                            </div>
                            <span class="badge-app"><i class="fas fa-key me-1"></i>Tautan Aman</span>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3 mb-3">
                            <div class="col-sm-6">
                                <div class="info-label">Pegawai</div>
                                <div class="info-value">{{ $guestEmployee->employee_name }}</div>
                            </div>
                            <div class="col-sm-6">
                                <div class="info-label">Tamu</div>
                                <div class="info-value">{{ $guestEmployee->guest_name }}</div>
                            </div>
                            <div class="col-sm-12">
                                <div class="info-label">Instansi</div>
                                <div class="info-value">{{ $guestEmployee->guest_company ?? '-' }}</div>
                            </div>
                            <div class="col-sm-12">
                                <div class="info-label">Keperluan Tamu</div>
                                <div class="info-value">{{ $guestEmployee->guest_purpose ?? '-' }}</div>
                            </div>
                        </div>

                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        <form method="POST" action="{{ url('/instruction/' . $token) }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-bold">Arahan</label>
                                <textarea name="instructions" class="form-control" rows="4" placeholder="Contoh: Tamu diarahkan langsung ke ruangan saya" required>{{ old('instructions', $guestEmployee->instructions) }}</textarea>
                                @error('instructions')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-2"></i>Kirim Arahan
                            </button>
                            @if($guestEmployee->instructions_submitted_at)
                                <small class="text-muted ms-2">Terakhir dikirim: {{ \Carbon\Carbon::parse($guestEmployee->instructions_submitted_at)->diffForHumans() }}</small>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>
</html>
