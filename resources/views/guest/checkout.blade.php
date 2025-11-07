<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Checkout - Buku Tamu</title>
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
        .checkout-card {
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            border: none;
        }
        .checkout-icon {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #1B4332 0%, #1e3a8a 50%, #B8860B 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
        }
        .btn-checkout {
            background: linear-gradient(135deg, #1B4332 0%, #1e3a8a 50%, #B8860B 100%);
            border: none;
            padding: 12px 40px;
            border-radius: 10px;
            font-weight: 600;
            color: white;
        }
        .btn-checkout:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7">
                <div class="card checkout-card">
                    <div class="card-body p-5 text-center">
                        <div class="checkout-icon">
                            <i class="fas fa-sign-out-alt fa-3x text-white"></i>
                        </div>
                        <h2 class="mb-4 fw-bold">Checkout Tamu</h2>
                        <p class="lead mb-4">
                            Terima kasih atas kunjungan Anda. Silakan checkout untuk menyelesaikan kunjungan Anda.
                        </p>
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold">Masukkan Nomor Telepon Anda</label>
                            <input type="text" id="phone" class="form-control form-control-lg text-center" 
                                   placeholder="081234567890" maxlength="15">
                            <small class="text-muted">Nomor telepon yang Anda gunakan saat registrasi</small>
                        </div>

                        <div class="d-grid gap-2">
                            <button onclick="processCheckout()" class="btn btn-checkout btn-lg">
                                <i class="fas fa-sign-out-alt me-2"></i>Checkout Sekarang
                            </button>
                            <a href="{{ route('guest.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 20px;">
                <div class="modal-body p-5 text-center">
                    <div style="width: 80px; height: 80px; background: #28a745; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                        <i class="fas fa-check fa-2x text-white"></i>
                    </div>
                    <h3 class="fw-bold mb-3">Checkout Berhasil!</h3>
                    <p class="mb-4">Terima kasih atas kunjungan Anda. Sampai jumpa lagi!</p>
                    <a href="{{ route('guest.index') }}" class="btn btn-primary">
                        <i class="fas fa-home me-2"></i>Kembali ke Beranda
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function processCheckout() {
            const phone = $('#phone').val().trim();

            if (!phone) {
                alert('Mohon masukkan nomor telepon Anda');
                return;
            }

            $.ajax({
                url: '/guest/checkout-by-phone',
                method: 'POST',
                data: { phone: phone },
                beforeSend: function() {
                    $('button').prop('disabled', true);
                    $('button').html('<span class="spinner-border spinner-border-sm me-2"></span>Memproses...');
                },
                success: function(response) {
                    if (response.success) {
                        const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                        successModal.show();
                    }
                },
                error: function(xhr) {
                    alert(xhr.responseJSON?.message || 'Terjadi kesalahan');
                    $('button').prop('disabled', false);
                    $('button').html('<i class="fas fa-sign-out-alt me-2"></i>Checkout Sekarang');
                }
            });
        }

        // Enter key handler
        $('#phone').keypress(function(e) {
            if (e.which == 13) {
                processCheckout();
            }
        });
    </script>
</body>
</html>