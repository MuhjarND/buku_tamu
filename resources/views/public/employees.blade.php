<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pegawai - Status Kehadiran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 2rem 0;
        }
        .header-section {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        .stat-card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        .employee-card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: all 0.3s;
            overflow: hidden;
        }
        .employee-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        .employee-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid white;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .employee-avatar-placeholder {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2.5rem;
            font-weight: bold;
            border: 4px solid white;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .position-group-title {
            background: white;
            padding: 1rem 1.5rem;
            border-radius: 15px;
            margin-bottom: 1.5rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        .position-group-title h3 {
            margin: 0;
            color: #667eea;
            font-weight: 700;
        }
        .auto-scroll-controls {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 1000;
        }
        .scroll-btn {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: white;
            border: none;
            box-shadow: 0 5px 20px rgba(0,0,0,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            cursor: pointer;
            transition: all 0.3s;
        }
        .scroll-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 8px 25px rgba(0,0,0,0.3);
        }
        .scroll-btn.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        @media (max-width: 768px) {
            .auto-scroll-controls {
                bottom: 20px;
                right: 20px;
            }
            .scroll-btn {
                width: 50px;
                height: 50px;
                font-size: 1.2rem;
            }
        }
        .status-badge-ada {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
            padding: 0.5rem 1.5rem;
            border-radius: 25px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .status-badge-keluar {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 0.5rem 1.5rem;
            border-radius: 25px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .position-badge {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 0.35rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        .search-box {
            border-radius: 25px;
            padding: 12px 20px;
            border: 2px solid #e0e0e0;
        }
        .search-box:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .filter-select {
            border-radius: 25px;
            padding: 12px 20px;
            border: 2px solid #e0e0e0;
        }
        .pulse {
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 25px;
            padding: 10px 30px;
            font-weight: 600;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }
        .last-update {
            font-size: 0.85rem;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header-section text-center">
            <h1 class="fw-bold mb-2">
                <i class="fas fa-users-cog me-2"></i>Daftar Pegawai
            </h1>
            <p class="text-muted mb-3">Status Kehadiran Real-time</p>
            <div class="d-flex justify-content-center gap-3 flex-wrap">
                <a href="{{ route('guest.index') }}" class="btn btn-primary">
                    <i class="fas fa-clipboard-user me-2"></i>Buku Tamu
                </a>
                <a href="{{ route('login') }}" class="btn btn-outline-secondary" style="border-radius: 25px;">
                    <i class="fas fa-sign-in-alt me-2"></i>Login Sistem
                </a>
            </div>
        </div>

        <!-- Statistik -->
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="card stat-card">
                    <div class="card-body text-center">
                        <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);" 
                             class="rounded-circle d-flex align-items-center justify-content-center text-white mx-auto mb-3">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                        <h2 class="fw-bold mb-0">{{ $statistics['total'] }}</h2>
                        <p class="text-muted mb-0">Total Pegawai</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card stat-card">
                    <div class="card-body text-center">
                        <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);" 
                             class="rounded-circle d-flex align-items-center justify-content-center text-white mx-auto mb-3">
                            <i class="fas fa-user-check fa-2x"></i>
                        </div>
                        <h2 class="fw-bold mb-0">{{ $statistics['ada'] }}</h2>
                        <p class="text-muted mb-0">Sedang Ada</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card stat-card">
                    <div class="card-body text-center">
                        <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);" 
                             class="rounded-circle d-flex align-items-center justify-content-center text-white mx-auto mb-3">
                            <i class="fas fa-user-times fa-2x"></i>
                        </div>
                        <h2 class="fw-bold mb-0">{{ $statistics['keluar'] }}</h2>
                        <p class="text-muted mb-0">Sedang Keluar</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter & Search -->
        <div class="card mb-4" style="border-radius: 15px; border: none; box-shadow: 0 5px 15px rgba(0,0,0,0.08);">
            <div class="card-body">
                <form action="{{ route('employees.list') }}" method="GET" class="row g-3">
                    <div class="col-md-6">
                        <input type="text" name="search" class="form-control search-box" 
                               placeholder="🔍 Cari nama pegawai..." value="{{ $search }}">
                    </div>
                    <div class="col-md-4">
                        <select name="position" class="form-select filter-select" onchange="this.form.submit()">
                            <option value="all" {{ $positionFilter == 'all' ? 'selected' : '' }}>Semua Jabatan</option>
                            @foreach($positions as $pos)
                                <option value="{{ $pos->position }}" {{ $positionFilter == $pos->position ? 'selected' : '' }}>
                                    {{ $pos->position }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search"></i> Cari
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Last Update Info -->
        <div class="text-center mb-3">
            <span class="last-update" id="last-update">
                <i class="fas fa-sync-alt me-1"></i>
                Terakhir diperbarui: {{ now()->format('d M Y, H:i') }} WIB
            </span>
        </div>

        <!-- Daftar Pegawai Grouped by Position -->
        @forelse($employeesByPosition as $position => $employees)
            <div class="position-group mb-5">
                <div class="position-group-title">
                    <h3>
                        <i class="fas fa-briefcase me-2"></i>{{ $position ?? 'Pegawai' }}
                        <span class="badge bg-secondary ms-2">{{ $employees->count() }} orang</span>
                    </h3>
                </div>

                <div class="row">
                    @foreach($employees as $employee)
                        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                            <div class="card employee-card" data-employee-id="{{ $employee->id }}">
                                <div class="card-body p-4 text-center">
                                    <!-- Avatar dengan Foto -->
                                    <div class="mb-3">
                                        @if($employee->photo)
                                            <img src="{{ asset('storage/' . $employee->photo) }}" 
                                                 alt="{{ $employee->name }}"
                                                 class="employee-avatar mx-auto">
                                        @else
                                            <div class="employee-avatar-placeholder mx-auto">
                                                {{ strtoupper(substr($employee->name, 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Nama -->
                                    <h5 class="mb-2 fw-bold">{{ $employee->name }}</h5>

                                    <!-- Badge Jabatan -->
                                    <span class="position-badge mb-3 d-inline-block">{{ $employee->position ?? 'Pegawai' }}</span>

                                    <!-- Status Kehadiran -->
                                    <div class="status-container" data-status="{{ $employee->presence_status }}">
                                        @if($employee->presence_status == 'ada')
                                            <div class="status-badge-ada">
                                                <i class="fas fa-check-circle pulse"></i>
                                                <span>Sedang Ada</span>
                                            </div>
                                        @else
                                            <div class="status-badge-keluar">
                                                <i class="fas fa-times-circle pulse"></i>
                                                <span>Sedang Keluar</span>
                                            </div>
                                        @endif
                                    </div>

                                    @if($employee->presence_updated_at)
                                        <small class="text-muted d-block mt-2">
                                            <i class="fas fa-clock me-1"></i>
                                            {{ \Carbon\Carbon::parse($employee->presence_updated_at)->diffForHumans() }}
                                        </small>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="row">
                <div class="col-12">
                    <div class="card" style="border-radius: 15px;">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                            <h5 class="text-muted">Tidak ada pegawai ditemukan</h5>
                            <p class="text-muted">Coba ubah filter atau kata kunci pencarian</p>
                        </div>
                    </div>
                </div>
            </div>
        @endforelse

        <!-- Auto Scroll Controls -->
        <div class="auto-scroll-controls">
            <button class="scroll-btn" id="auto-scroll-btn" title="Auto Scroll">
                <i class="fas fa-play" id="scroll-icon"></i>
            </button>
        </div>

        <!-- Footer -->
        <div class="text-center mt-4">
            <p class="text-white">
                <i class="fas fa-info-circle me-2"></i>
                Status kehadiran diperbarui secara real-time oleh resepsionis
            </p>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto refresh setiap 30 detik
        let autoRefreshEnabled = true;
        let autoScrollEnabled = false;
        let scrollInterval = null;

        function refreshStatus() {
            if (!autoRefreshEnabled) return;

            $.ajax({
                url: '{{ route("employees.status") }}',
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        response.data.forEach(function(employee) {
                            const card = $(`.employee-card[data-employee-id="${employee.id}"]`);
                            const statusContainer = card.find('.status-container');
                            
                            if (statusContainer.data('status') !== employee.presence_status) {
                                // Update status
                                const newBadge = employee.presence_status === 'ada'
                                    ? '<div class="status-badge-ada"><i class="fas fa-check-circle pulse"></i><span>Sedang Ada</span></div>'
                                    : '<div class="status-badge-keluar"><i class="fas fa-times-circle pulse"></i><span>Sedang Keluar</span></div>';
                                
                                statusContainer.html(newBadge);
                                statusContainer.data('status', employee.presence_status);
                                
                                // Animasi
                                card.addClass('border-warning');
                                setTimeout(() => card.removeClass('border-warning'), 2000);
                            }
                        });

                        // Update timestamp
                        $('#last-update').html(`<i class="fas fa-sync-alt me-1"></i>Terakhir diperbarui: ${response.updated_at} WIB`);
                    }
                },
                error: function() {
                    console.log('Failed to refresh status');
                }
            });
        }

        // Auto Scroll Functionality
        function startAutoScroll() {
            if (scrollInterval) return;
            
            scrollInterval = setInterval(function() {
                const scrollHeight = $(document).height();
                const scrollPos = $(window).scrollTop();
                const windowHeight = $(window).height();
                
                // Scroll down
                if (scrollPos + windowHeight < scrollHeight) {
                    $('html, body').animate({
                        scrollTop: scrollPos + windowHeight * 0.8
                    }, 2000);
                } else {
                    // Reset to top
                    $('html, body').animate({
                        scrollTop: 0
                    }, 1000);
                }
            }, 3000); // Scroll every 3 seconds
        }

        function stopAutoScroll() {
            if (scrollInterval) {
                clearInterval(scrollInterval);
                scrollInterval = null;
            }
        }

        // Toggle auto scroll
        $('#auto-scroll-btn').click(function() {
            autoScrollEnabled = !autoScrollEnabled;
            const btn = $(this);
            const icon = $('#scroll-icon');
            
            if (autoScrollEnabled) {
                btn.addClass('active');
                icon.removeClass('fa-play').addClass('fa-pause');
                startAutoScroll();
            } else {
                btn.removeClass('active');
                icon.removeClass('fa-pause').addClass('fa-play');
                stopAutoScroll();
            }
        });

        // Stop auto scroll on manual scroll
        let scrollTimeout;
        $(window).on('wheel touchmove', function() {
            if (autoScrollEnabled) {
                clearTimeout(scrollTimeout);
                scrollTimeout = setTimeout(function() {
                    // Resume after 2 seconds of no manual scroll
                }, 2000);
            }
        });

        // Refresh setiap 30 detik
        setInterval(refreshStatus, 30000);

        $(document).ready(function() {
            console.log('Auto-refresh enabled: Status will update every 30 seconds');
            console.log('Click the button at bottom-right to enable auto-scroll');
        });
    </script>
</body>
</html>