<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pegawai - Status Kehadiran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #1B4332 0%, #1e3a8a 50%, #B8860B 100%);
            min-height: 100vh;
            padding: 0;
            overflow-x: hidden;
        }

        /* Header Modern */
        .modern-header {
            background: linear-gradient(135deg, rgba(27, 67, 50, 0.95) 0%, rgba(30, 58, 138, 0.95) 100%);
            backdrop-filter: blur(20px);
            padding: 1.5rem 0;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.3);
            position: sticky;
            top: 0;
            z-index: 999;
            border-bottom: 3px solid #B8860B;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .logo-section h1 {
            font-size: 1.8rem;
            font-weight: 800;
            color: white;
            margin: 0;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .logo-section p {
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.9);
            margin: 0;
            font-weight: 500;
        }

        .header-actions {
            display: flex;
            gap: 0.75rem;
        }

        .btn-modern {
            padding: 0.6rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.9rem;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary-modern {
            background: #B8860B;
            color: white;
            box-shadow: 0 4px 15px rgba(184, 134, 11, 0.4);
        }

        .btn-primary-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(184, 134, 11, 0.5);
            color: white;
            background: #DAA520;
        }

        .btn-outline-modern {
            background: transparent;
            color: white;
            border: 2px solid white;
        }

        .btn-outline-modern:hover {
            background: white;
            color: #1B4332;
            transform: translateY(-2px);
        }

        /* Stats Cards Modern */
        .stats-section {
            max-width: 1400px;
            margin: 2rem auto;
            padding: 0 2rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card-modern {
            background: linear-gradient(135deg, rgba(27, 67, 50, 0.9) 0%, rgba(30, 58, 138, 0.9) 100%);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 2rem;
            border: 2px solid rgba(184, 134, 11, 0.3);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card-modern::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: #B8860B;
        }

        .stat-card-modern:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.4);
            border-color: #B8860B;
        }

        .stat-icon-modern {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin-bottom: 1rem;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 800;
            color: white;
            line-height: 1;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            font-size: 0.95rem;
            color: rgba(255, 255, 255, 0.8);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Search & Filter Modern */
        .filter-section {
            background: linear-gradient(135deg, rgba(27, 67, 50, 0.9) 0%, rgba(30, 58, 138, 0.9) 100%);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            border: 2px solid rgba(184, 134, 11, 0.3);
        }

        .search-input-modern {
            border: 2px solid rgba(184, 134, 11, 0.3);
            border-radius: 14px;
            padding: 0.9rem 1.2rem 0.9rem 3rem;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.1);
            width: 100%;
            color: white;
        }

        .search-input-modern::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        .search-input-modern:focus {
            border-color: #B8860B;
            box-shadow: 0 0 0 4px rgba(184, 134, 11, 0.2);
            outline: none;
            background: rgba(255, 255, 255, 0.15);
        }

        .search-wrapper {
            position: relative;
        }

        .search-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.6);
            font-size: 1.1rem;
        }

        .select-modern {
            border: 2px solid rgba(184, 134, 11, 0.3);
            border-radius: 14px;
            padding: 0.9rem 1.2rem;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.1);
            cursor: pointer;
            color: white;
        }

        .select-modern option {
            background: #1B4332;
            color: white;
        }

        .select-modern:focus {
            border-color: #B8860B;
            box-shadow: 0 0 0 4px rgba(184, 134, 11, 0.2);
            outline: none;
        }

        /* Position Group Modern */
        .content-section {
            max-width: 1400px;
            margin: 0 auto 3rem;
            padding: 0 2rem;
        }

        .position-group-modern {
            margin-bottom: 3rem;
        }

        .position-header {
            background: linear-gradient(135deg, rgba(27, 67, 50, 0.95) 0%, rgba(30, 58, 138, 0.95) 100%);
            backdrop-filter: blur(20px);
            padding: 1.5rem 2rem;
            border-radius: 16px;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            border-left: 5px solid #B8860B;
            border: 2px solid rgba(184, 134, 11, 0.3);
        }

        .position-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: white;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .position-count {
            background: #B8860B;
            color: white;
            padding: 0.4rem 1rem;
            border-radius: 12px;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .position-description {
            background: rgba(184, 134, 11, 0.2);
            padding: 1rem 2rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            border-left: 3px solid #B8860B;
        }

        .position-description p {
            margin: 0;
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.95rem;
            line-height: 1.6;
        }

        /* Employee Card SUPER Modern - CENTERED */
        .employees-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 2rem;
            align-items: flex-start;
        }

        .employee-card-modern {
            background: linear-gradient(135deg, rgba(27, 67, 50, 0.9) 0%, rgba(30, 58, 138, 0.9) 100%);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 2rem;
            border: 2px solid rgba(184, 134, 11, 0.3);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
            cursor: pointer;
            width: 280px;
            flex-shrink: 0;
        }

        .employee-card-modern::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 100%;
            background: linear-gradient(135deg, rgba(184, 134, 11, 0.1) 0%, rgba(184, 134, 11, 0.2) 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .employee-card-modern:hover::before {
            opacity: 1;
        }

        .employee-card-modern:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
            border-color: #B8860B;
        }

        .employee-photo-wrapper {
            position: relative;
            margin-bottom: 1.5rem;
            display: flex;
            justify-content: center;
        }

        .employee-photo {
            width: 160px;
            height: 160px;
            border-radius: 20px;
            object-fit: cover;
            border: 4px solid #B8860B;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
        }

        .employee-card-modern:hover .employee-photo {
            transform: scale(1.05);
            box-shadow: 0 12px 40px rgba(184, 134, 11, 0.4);
        }

        .employee-photo-placeholder {
            width: 160px;
            height: 160px;
            background: linear-gradient(135deg, #1B4332 0%, #1e3a8a 50%, #B8860B 100%);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 4rem;
            font-weight: 800;
            border: 4px solid #B8860B;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
        }

        .employee-card-modern:hover .employee-photo-placeholder {
            transform: scale(1.05);
        }

        .employee-name {
            font-size: 1.2rem;
            font-weight: 700;
            color: white;
            text-align: center;
            margin-bottom: 0.75rem;
            min-height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .employee-position {
            background: linear-gradient(135deg, #B8860B 0%, #DAA520 100%);
            color: white;
            padding: 0.5rem 1.2rem;
            border-radius: 12px;
            font-size: 0.85rem;
            font-weight: 600;
            text-align: center;
            margin-bottom: 1rem;
            display: inline-block;
            width: 100%;
            box-shadow: 0 4px 15px rgba(184, 134, 11, 0.3);
        }

        .status-badge-modern {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem 1.2rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.9rem;
            margin-top: 1rem;
        }

        .status-ada {
            background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(72, 187, 120, 0.3);
        }

        .status-keluar {
            background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(245, 101, 101, 0.3);
        }

        .status-time {
            font-size: 0.8rem;
            color: rgba(255, 255, 255, 0.7);
            text-align: center;
            margin-top: 0.75rem;
            font-weight: 500;
        }

        /* Auto Scroll Button Modern */
        .scroll-control {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .scroll-btn-modern {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #1B4332 0%, #1e3a8a 100%);
            border: 2px solid #B8860B;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
            color: white;
        }

        .scroll-btn-modern:hover {
            transform: scale(1.1);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.4);
        }

        .scroll-btn-modern.active {
            background: linear-gradient(135deg, #B8860B 0%, #DAA520 100%);
            color: white;
        }

        .scroll-btn-modern.active::after {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            border: 3px solid #B8860B;
            animation: pulse-ring 1.5s infinite;
        }

        @keyframes pulse-ring {
            0% {
                transform: scale(1);
                opacity: 1;
            }
            100% {
                transform: scale(1.3);
                opacity: 0;
            }
        }

        /* Pulse Animation */
        .pulse {
            animation: pulse-icon 1.5s ease-in-out infinite;
        }

        @keyframes pulse-icon {
            0%, 100% {
                opacity: 1;
                transform: scale(1);
            }
            50% {
                opacity: 0.7;
                transform: scale(1.1);
            }
        }

        /* Last Update */
        .last-update-modern {
            text-align: center;
            padding: 1rem;
            color: white;
            font-size: 0.9rem;
            font-weight: 500;
            margin: 2rem 0;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
        }

        /* Empty State */
        .empty-state {
            background: linear-gradient(135deg, rgba(27, 67, 50, 0.9) 0%, rgba(30, 58, 138, 0.9) 100%);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 4rem 2rem;
            text-align: center;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            border: 2px solid rgba(184, 134, 11, 0.3);
        }

        .empty-state i {
            font-size: 5rem;
            color: rgba(184, 134, 11, 0.5);
            margin-bottom: 1.5rem;
        }

        .empty-state h5 {
            color: white;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .empty-state p {
            color: rgba(255, 255, 255, 0.7);
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            .logo-section h1 {
                font-size: 1.5rem;
            }

            .header-actions {
                width: 100%;
                justify-content: center;
            }

            .btn-modern {
                padding: 0.5rem 1rem;
                font-size: 0.85rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .stat-card-modern {
                padding: 1.5rem;
            }

            .stat-number {
                font-size: 2rem;
            }

            .employees-grid {
                gap: 1.5rem;
            }

            .employee-card-modern {
                width: 240px;
            }

            .employee-photo,
            .employee-photo-placeholder {
                width: 140px;
                height: 140px;
                font-size: 3rem;
            }

            .employee-name {
                font-size: 1.1rem;
                min-height: 50px;
            }

            .position-header {
                padding: 1rem 1.5rem;
            }

            .position-title {
                font-size: 1.2rem;
            }

            .scroll-control {
                bottom: 1rem;
                right: 1rem;
            }

            .scroll-btn-modern {
                width: 50px;
                height: 50px;
                font-size: 1.2rem;
            }
        }

        @media (max-width: 576px) {
            .stats-section,
            .content-section {
                padding: 0 1rem;
            }

            .employee-card-modern {
                width: 100%;
                max-width: 300px;
            }

            .employee-photo,
            .employee-photo-placeholder {
                width: 120px;
                height: 120px;
                font-size: 2.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Modern Header -->
    <div class="modern-header">
        <div class="header-content">
            <div class="logo-section">
                <h1><i class="fas fa-users-cog me-2"></i>Daftar Pegawai</h1>
                <p>Status Kehadiran Real-time</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('guest.index') }}" class="btn-modern btn-primary-modern">
                    <i class="fas fa-clipboard-user"></i>
                    <span class="d-none d-md-inline">Buku Tamu</span>
                </a>
                <a href="{{ route('login') }}" class="btn-modern btn-outline-modern">
                    <i class="fas fa-sign-in-alt"></i>
                    <span class="d-none d-md-inline">Login</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Section -->
    <div class="stats-section">
        <div class="stats-grid">
            <div class="stat-card-modern">
                <div class="stat-icon-modern" style="background: rgba(184, 134, 11, 0.3); color: #B8860B;">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-number">{{ $statistics['total'] }}</div>
                <div class="stat-label">Total Pegawai</div>
            </div>

            <div class="stat-card-modern">
                <div class="stat-icon-modern" style="background: rgba(72, 187, 120, 0.3); color: #48bb78;">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="stat-number">{{ $statistics['ada'] }}</div>
                <div class="stat-label">Sedang Ada</div>
            </div>

            <div class="stat-card-modern">
                <div class="stat-icon-modern" style="background: rgba(245, 101, 101, 0.3); color: #f56565;">
                    <i class="fas fa-user-times"></i>
                </div>
                <div class="stat-number">{{ $statistics['keluar'] }}</div>
                <div class="stat-label">Sedang Keluar</div>
            </div>
        </div>

        <!-- Filter & Search -->
        <div class="filter-section">
            <form action="{{ route('employees.list') }}" method="GET">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="search-wrapper">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" name="search" class="search-input-modern" 
                                   placeholder="Cari nama pegawai..." value="{{ $search }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <select name="position" class="select-modern" onchange="this.form.submit()">
                            <option value="all">Semua Jabatan</option>
                            @foreach($positions as $pos)
                                <option value="{{ $pos->position }}" {{ $positionFilter == $pos->position ? 'selected' : '' }}>
                                    {{ $pos->position }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn-modern btn-primary-modern w-100">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Last Update -->
    <div class="last-update-modern" id="last-update">
        <i class="fas fa-sync-alt me-2"></i>
        Terakhir diperbarui: {{ now()->format('d M Y, H:i') }} WIB
    </div>

    <!-- Content Section -->
    <div class="content-section">
        @forelse($mergedGroups as $groupName => $data)
            <div class="position-group-modern">
                <div class="position-header">
                    <div class="position-title">
                        <i class="fas fa-briefcase"></i>
                        {{ $groupName }}
                    </div>
                    <span class="position-count">{{ $data['employees']->count() }} orang</span>
                </div>

                @if($data['description'])
                    <div class="position-description">
                        <p><i class="fas fa-info-circle me-2"></i>{{ $data['description'] }}</p>
                    </div>
                @endif

                <div class="employees-grid">
                    @foreach($data['employees'] as $employee)
                        <div class="employee-card-modern" data-employee-id="{{ $employee->id }}">
                            <div class="employee-photo-wrapper">
                                @if($employee->photo)
                                    <img src="{{ asset('storage/' . $employee->photo) }}" 
                                         alt="{{ $employee->name }}"
                                         class="employee-photo">
                                @else
                                    <div class="employee-photo-placeholder">
                                        {{ strtoupper(substr($employee->name, 0, 1)) }}
                                    </div>
                                @endif
                            </div>

                            <div class="employee-name">{{ $employee->name }}</div>
                            <div class="employee-position">{{ $employee->position ?? 'Pegawai' }}</div>

                            <div class="status-container" data-status="{{ $employee->presence_status }}">
                                @if($employee->presence_status == 'ada')
                                    <div class="status-badge-modern status-ada">
                                        <i class="fas fa-check-circle pulse"></i>
                                        <span>Sedang Ada</span>
                                    </div>
                                @else
                                    <div class="status-badge-modern status-keluar">
                                        <i class="fas fa-times-circle pulse"></i>
                                        <span>Sedang Keluar</span>
                                    </div>
                                @endif
                            </div>

                            @if($employee->presence_updated_at)
                                <div class="status-time">
                                    <i class="fas fa-clock me-1"></i>
                                    {{ \Carbon\Carbon::parse($employee->presence_updated_at)->diffForHumans() }}
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <h5>Tidak Ada Pegawai Ditemukan</h5>
                <p>Coba ubah filter atau kata kunci pencarian</p>
            </div>
        @endforelse
    </div>

    <!-- Auto Scroll Controls -->
    <div class="scroll-control">
        <button class="scroll-btn-modern" id="auto-scroll-btn" title="Auto Scroll">
            <i class="fas fa-play" id="scroll-icon"></i>
        </button>
        <button class="scroll-btn-modern" id="scroll-to-top-btn" title="Ke Atas" style="display: none;">
            <i class="fas fa-arrow-up"></i>
        </button>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let autoRefreshEnabled = true;
        let autoScrollEnabled = false;
        let scrollAnimation = null;

        // Auto Refresh Status
        function refreshStatus() {
            if (!autoRefreshEnabled) return;

            $.ajax({
                url: '{{ route("employees.status") }}',
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        response.data.forEach(function(employee) {
                            const card = $(`.employee-card-modern[data-employee-id="${employee.id}"]`);
                            const statusContainer = card.find('.status-container');
                            
                            if (statusContainer.data('status') !== employee.presence_status) {
                                const newBadge = employee.presence_status === 'ada'
                                    ? '<div class="status-badge-modern status-ada"><i class="fas fa-check-circle pulse"></i><span>Sedang Ada</span></div>'
                                    : '<div class="status-badge-modern status-keluar"><i class="fas fa-times-circle pulse"></i><span>Sedang Keluar</span></div>';
                                
                                statusContainer.html(newBadge);
                                statusContainer.data('status', employee.presence_status);
                                
                                // Flash animation
                                card.css('background', 'rgba(184, 134, 11, 0.3)');
                                setTimeout(() => card.css('background', ''), 2000);
                            }
                        });

                        $('#last-update').html(`<i class="fas fa-sync-alt me-2"></i>Terakhir diperbarui: ${response.updated_at} WIB`);
                    }
                }
            });
        }

        // Smooth Continuous Auto Scroll
        function startContinuousScroll() {
            if (scrollAnimation) return;
            
            const scrollSpeed = 1; // pixel per frame
            const pauseAtBottom = 2000; // 2 detik pause di bawah
            let isAtBottom = false;
            let pauseTimeout = null;

            function scroll() {
                if (!autoScrollEnabled) return;

                const scrollHeight = $(document).height();
                const scrollPos = $(window).scrollTop();
                const windowHeight = $(window).height();
                
                // Cek jika sudah di bawah
                if (scrollPos + windowHeight >= scrollHeight - 10) {
                    if (!isAtBottom) {
                        isAtBottom = true;
                        // Pause sebentar di bawah
                        pauseTimeout = setTimeout(() => {
                            // Scroll smooth ke atas
                            $('html, body').animate({
                                scrollTop: 0
                            }, 2000, 'linear', function() {
                                isAtBottom = false;
                                // Lanjutkan scroll continuous setelah sampai atas
                                setTimeout(() => {
                                    scrollAnimation = requestAnimationFrame(scroll);
                                }, 500);
                            });
                        }, pauseAtBottom);
                    }
                } else if (!isAtBottom) {
                    // Scroll continuous ke bawah
                    window.scrollBy(0, scrollSpeed);
                    scrollAnimation = requestAnimationFrame(scroll);
                }
            }

            scrollAnimation = requestAnimationFrame(scroll);
        }

        function stopContinuousScroll() {
            if (scrollAnimation) {
                cancelAnimationFrame(scrollAnimation);
                scrollAnimation = null;
            }
            $('html, body').stop(); // Stop jQuery animations
        }

        // Toggle Auto Scroll
        $('#auto-scroll-btn').click(function() {
            autoScrollEnabled = !autoScrollEnabled;
            const btn = $(this);
            const icon = $('#scroll-icon');
            
            if (autoScrollEnabled) {
                btn.addClass('active');
                icon.removeClass('fa-play').addClass('fa-pause');
                startContinuousScroll();
            } else {
                btn.removeClass('active');
                icon.removeClass('fa-pause').addClass('fa-play');
                stopContinuousScroll();
            }
        });

        // Scroll to Top Button
        $('#scroll-to-top-btn').click(function() {
            $('html, body').animate({
                scrollTop: 0
            }, 1000, 'swing');
        });

        // Show/Hide Scroll to Top Button
        $(window).scroll(function() {
            if ($(this).scrollTop() > 300) {
                $('#scroll-to-top-btn').fadeIn();
            } else {
                $('#scroll-to-top-btn').fadeOut();
            }
        });

        // Pause auto scroll on manual interaction
        let userInteractionTimeout;
        $(window).on('wheel touchmove', function() {
            if (autoScrollEnabled) {
                stopContinuousScroll();
                clearTimeout(userInteractionTimeout);
                
                // Resume after 3 seconds of no interaction
                userInteractionTimeout = setTimeout(function() {
                    if (autoScrollEnabled) {
                        startContinuousScroll();
                    }
                }, 3000);
            }
        });

        // Auto refresh setiap 30 detik
        setInterval(refreshStatus, 30000);

        // Initial load
        $(document).ready(function() {
            console.log('✨ Halaman Daftar Pegawai Modern loaded');
            console.log('🔄 Auto-refresh: Setiap 30 detik');
            console.log('📜 Auto-scroll: Klik tombol untuk mengaktifkan');
        });
    </script>
</body>
</html>