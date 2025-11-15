<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Berhasil - Buku Tamu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #1B4332 0%, #1e3a8a 50%, #B8860B 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .success-card {
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            border: none;
            animation: slideUp 0.5s ease-out;
        }
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .success-icon {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #1B4332 0%, #1e3a8a 50%, #B8860B 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            animation: scaleIn 0.5s ease-out 0.3s both;
        }
        @keyframes scaleIn {
            from {
                transform: scale(0);
            }
            to {
                transform: scale(1);
            }
        }
        .btn-home {
            background: linear-gradient(135deg, #1B4332 0%, #1e3a8a 50%, #B8860B 100%);
            border: none;
            padding: 12px 40px;
            border-radius: 10px;
            font-weight: 600;
            color: white;
        }
        .btn-home:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="card success-card">
                    <div class="card-body p-5 text-center">
                        <div class="success-icon">
                            <i class="fas fa-check fa-3x text-white"></i>
                        </div>
                        <h2 class="mb-4 fw-bold">Registrasi Berhasil!</h2>
                        <p class="lead mb-4">
                            Terima kasih telah mengisi buku tamu. Data Anda telah berhasil disimpan.
                        </p>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            PTSP akan segera memverifikasi data Anda. Anda akan menerima notifikasi WhatsApp setelah verifikasi selesai.
                        </div>
                        <p class="text-muted mb-4">
                            <i class="fas fa-hourglass-half me-2"></i>
                            Mohon menunggu di area PTSP
                        </p>
                        <a href="{{ route('guest.index') }}" class="btn btn-home btn-lg mb-2">
                            <i class="fas fa-home me-2"></i>Kembali ke Beranda
                        </a>
                        <div class="mt-3">
                            <small class="text-muted">Sudah selesai?</small><br>
                            <a href="{{ route('guest.checkout.page') }}" class="text-decoration-none">
                                <i class="fas fa-sign-out-alt me-1"></i>Checkout Sekarang
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
