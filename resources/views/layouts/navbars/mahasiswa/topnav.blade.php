{{-- filepath: d:\laragon\www\JTIintern\resources\views\layouts\navbars\mahasiswa\topnav.blade.php --}}

<!-- Navbar -->
<nav class="navbar navbar-expand-lg px-9 ">
    <div class="container-fluid px-4">
        <div class="d-flex align-items-center">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="/img/Jti_polinema.png" alt="Logo" style="height: 32px;">
                <span class="ms-2" style="color: #2D2D2D; font-size: 20px; font-weight: 600;">JTIintern</span>
            </a>
        </div>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbar">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item px-2">
                    <a href="{{ route('mahasiswa.dashboard') }}"
                        class="nav-link {{ request()->routeIs('mahasiswa.dashboard') ? 'active' : '' }} fw-medium">
                        Dashboard
                    </a>
                </li>
                <li class="nav-item px-2">
                    <a href="{{ route('mahasiswa.lowongan') }}"
                        class="nav-link {{ request()->routeIs('mahasiswa.lowongan') ? 'active' : '' }} fw-medium">
                        Lowongan Magang
                    </a>
                </li>
                <li class="nav-item px-2">
                    <a href="{{ route('mahasiswa.lamaran') }}"
                        class="nav-link {{ request()->routeIs('mahasiswa.lamaran') ? 'active' : '' }} fw-medium">
                        Lamaran Saya
                    </a>
                </li>
                <li class="nav-item px-2">
                    <a href="{{ route('mahasiswa.logaktivitas') }}"
                        class="nav-link {{ request()->routeIs('mahasiswa.logaktivitas') ? 'active' : '' }} fw-medium">
                        Log Aktivitas
                    </a>
                </li>
                <li class="nav-item px-2">
                    <a href="{{ route('mahasiswa.evaluasi') }}"
                        class="nav-link {{ request()->routeIs('mahasiswa.evaluasi') ? 'active' : '' }} fw-medium">
                        Evaluasi
                    </a>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-3">
                <!-- ✅ ENHANCED: Modern Notification Bell -->
                <div class="dropdown position-relative">
                    <button class="btn p-0 border-0 notification-bell-btn" type="button" data-bs-toggle="dropdown"
                        data-bs-auto-close="outside" id="notificationDropdown">
                        <div class="notification-bell-container">
                            <i class="bi bi-bell notification-bell-icon"></i>
                            <span class="notification-badge" id="notificationCount" style="display: none;">0</span>
                        </div>
                    </button>

                    <!-- ✅ MODERN: Notification Dropdown -->
                    <div class="dropdown-menu dropdown-menu-end modern-notification-dropdown">
                        <!-- Header -->
                        <div class="notification-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="notification-title">Notifikasi</h6>
                                    <small class="notification-subtitle" id="notificationSubtitle">Terbaru untuk
                                        Anda</small>
                                </div>
                                <div class="notification-actions">
                                    <button class="btn-action btn-mark-all" id="markAllRead"
                                        title="Tandai semua dibaca">
                                        <i class="bi bi-check2-all"></i>
                                    </button>
                                    <div class="dropdown">
                                        <button class="btn-action btn-options" data-bs-toggle="dropdown"
                                            title="Opsi lainnya">
                                            <i class="bi bi-three-dots"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end options-menu">
                                            <li>
                                                <button class="dropdown-item option-item"
                                                    onclick="notificationSystem.clearRead()">
                                                    <i class="bi bi-check-circle text-success"></i>
                                                    <span>Hapus yang Dibaca</span>
                                                </button>
                                            </li>
                                            <li>
                                                <button class="dropdown-item option-item"
                                                    onclick="notificationSystem.clearExpired()">
                                                    <i class="bi bi-clock-history text-warning"></i>
                                                    <span>Hapus Kedaluwarsa</span>
                                                </button>
                                            </li>
                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>
                                            <li>
                                                <button class="dropdown-item option-item text-danger"
                                                    onclick="notificationSystem.clearAll()">
                                                    <i class="bi bi-trash"></i>
                                                    <span>Hapus Semua</span>
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Loading State -->
                        <div class="notification-loading" id="notificationLoading">
                            <div class="loading-container">
                                <div class="loading-spinner"></div>
                                <span class="loading-text">Memuat notifikasi...</span>
                            </div>
                        </div>

                        <!-- Notification List -->
                        <div class="notification-list-container" id="notificationList">
                            <!-- Will be populated by JavaScript -->
                        </div>

                        <!-- Empty State -->
                        <div class="notification-empty" id="notificationEmpty" style="display: none;">
                            <div class="empty-container">
                                <div class="empty-icon">
                                    <i class="bi bi-bell-slash"></i>
                                </div>
                                <h6 class="empty-title">Tidak ada notifikasi</h6>
                                <p class="empty-subtitle">Semua notifikasi akan muncul di sini</p>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="notification-footer">
                            <button class="btn-footer btn-refresh" onclick="notificationSystem.loadNotifications()">
                                <i class="bi bi-arrow-clockwise"></i>
                                <span>Refresh</span>
                            </button>
                            <div class="footer-divider"></div>
                            <span class="auto-refresh-text">Auto-refresh: 30s</span>
                        </div>
                    </div>
                </div>

                <!-- ✅ Profile Dropdown (existing) -->
                <div class="dropdown">
                    <button class="btn rounded-circle profile-button" type="button" data-bs-toggle="dropdown"
                        data-bs-auto-close="true" style="width: 32px; height: 32px; background: #EFF6FF;">
                        <span class="me-2 fw-medium">{{ substr(auth()->user()->name, 0, 1) }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="{{ route('mahasiswa.profile') }}">
                                <i class="bi bi-person me-2"></i>Profile
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}" class="dropdown-item p-0">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>

@push('css')
    <link href="{{ asset('assets/css/topnav.css') }}" rel="stylesheet" />
    <!-- ✅ TAMBAHKAN: SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        /* ✅ MODERN NOTIFICATION STYLES */
        .notification-bell-btn {
            background: none !important;
            position: relative;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .notification-bell-container {
            position: relative;
            padding: 8px;
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .notification-bell-btn:hover .notification-bell-container {
            background: rgba(59, 130, 246, 0.1);
            transform: scale(1.05);
        }

        .notification-bell-icon {
            font-size: 20px;
            color: #374151;
            transition: color 0.3s ease;
        }

        .notification-bell-btn:hover .notification-bell-icon {
            color: #3B82F6;
        }

        .notification-badge {
            position: absolute;
            top: 2px;
            right: 2px;
            background: linear-gradient(135deg, #EF4444, #DC2626);
            color: white;
            border-radius: 50%;
            min-width: 18px;
            height: 18px;
            font-size: 10px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid white;
            animation: pulse-badge 2s infinite;
        }

        @keyframes pulse-badge {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }
        }

        /* Dropdown Styles */
        .modern-notification-dropdown {
            width: 420px;
            max-height: 600px;
            border: none;
            border-radius: 16px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            padding: 0;
            overflow: hidden;
            background: white;
        }

        /* Header */
        .notification-header {
            background: linear-gradient(135deg, #F8FAFC 0%, #F1F5F9 100%);
            padding: 20px 24px 16px;
            border-bottom: 1px solid #E2E8F0;
        }

        .notification-title {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
            color: #1E293B;
        }

        .notification-subtitle {
            color: #64748B;
            font-size: 13px;
            margin: 0;
        }

        .notification-actions {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .btn-action {
            width: 32px;
            height: 32px;
            border: none;
            border-radius: 8px;
            background: rgba(59, 130, 246, 0.1);
            color: #3B82F6;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-action:hover {
            background: rgba(59, 130, 246, 0.2);
            transform: scale(1.05);
        }

        .btn-mark-all {
            background: rgba(34, 197, 94, 0.1) !important;
            color: #22C55E !important;
        }

        .btn-mark-all:hover {
            background: rgba(34, 197, 94, 0.2) !important;
        }

        .btn-options {
            background: rgba(107, 114, 128, 0.1) !important;
            color: #6B7280 !important;
        }

        .btn-options:hover {
            background: rgba(107, 114, 128, 0.2) !important;
        }

        /* Options Menu */
        .options-menu {
            border: none;
            border-radius: 12px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            padding: 8px;
            min-width: 200px;
        }

        .option-item {
            border-radius: 8px;
            padding: 12px 16px;
            border: none;
            background: none;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 12px;
            width: 100%;
        }

        .option-item:hover {
            background: #F8FAFC;
            transform: translateX(4px);
        }

        .option-item i {
            font-size: 16px;
        }

        /* Loading */
        .notification-loading {
            padding: 48px 24px;
        }

        .loading-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 16px;
        }

        .loading-spinner {
            width: 32px;
            height: 32px;
            border: 3px solid #E2E8F0;
            border-top: 3px solid #3B82F6;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .loading-text {
            color: #64748B;
            font-size: 14px;
        }

        /* Notification List */
        .notification-list-container {
            max-height: 400px;
            overflow-y: auto;
            padding: 8px 0;
        }

        .notification-list-container::-webkit-scrollbar {
            width: 4px;
        }

        .notification-list-container::-webkit-scrollbar-track {
            background: transparent;
        }

        .notification-list-container::-webkit-scrollbar-thumb {
            background: #CBD5E1;
            border-radius: 2px;
        }

        .notification-list-container::-webkit-scrollbar-thumb:hover {
            background: #94A3B8;
        }

        /* Notification Item */
        .notification-item {
            padding: 16px 24px;
            border-bottom: 1px solid #F1F5F9;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            background: white;
        }

        .notification-item:hover {
            background: #F8FAFC;
            transform: translateX(4px);
        }

        .notification-item.unread {
            background: linear-gradient(135deg, #EBF8FF 0%, #DBEAFE 100%);
            border-left: 4px solid #3B82F6;
        }

        .notification-item.unread:hover {
            background: linear-gradient(135deg, #DBEAFE 0%, #BFDBFE 100%);
        }

        .notification-icon-wrapper {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        .notification-content {
            flex: 1;
            min-width: 0;
        }

        .notification-main-title {
            font-size: 14px;
            font-weight: 600;
            color: #1E293B;
            margin: 0 0 4px 0;
            line-height: 1.4;
        }

        .notification-message {
            font-size: 13px;
            color: #64748B;
            margin: 0 0 8px 0;
            line-height: 1.5;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .notification-time {
            font-size: 12px;
            color: #94A3B8;
            font-weight: 500;
        }

        .notification-actions-column {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            flex-shrink: 0;
        }

        .notification-dot {
            width: 8px;
            height: 8px;
            background: #3B82F6;
            border-radius: 50%;
            animation: pulse-dot 2s infinite;
        }

        @keyframes pulse-dot {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
        }

        .btn-delete-notification {
            width: 24px;
            height: 24px;
            border: none;
            border-radius: 6px;
            background: rgba(239, 68, 68, 0.1);
            color: #EF4444;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            opacity: 0;
        }

        .notification-item:hover .btn-delete-notification {
            opacity: 1;
        }

        .btn-delete-notification:hover {
            background: rgba(239, 68, 68, 0.2);
            transform: scale(1.1);
        }

        /* Category Icons */
        .icon-lamaran {
            background: rgba(59, 130, 246, 0.1);
            color: #3B82F6;
        }

        .icon-magang {
            background: rgba(34, 197, 94, 0.1);
            color: #22C55E;
        }

        .icon-sistem {
            background: rgba(107, 114, 128, 0.1);
            color: #6B7280;
        }

        .icon-pengumuman {
            background: rgba(249, 115, 22, 0.1);
            color: #F97316;
        }

        .icon-evaluasi {
            background: rgba(168, 85, 247, 0.1);
            color: #A855F7;
        }

        .icon-deadline {
            background: rgba(239, 68, 68, 0.1);
            color: #EF4444;
        }

        /* Empty State */
        .notification-empty {
            padding: 48px 24px;
        }

        .empty-container {
            text-align: center;
        }

        .empty-icon {
            width: 64px;
            height: 64px;
            background: #F1F5F9;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
        }

        .empty-icon i {
            font-size: 28px;
            color: #94A3B8;
        }

        .empty-title {
            font-size: 16px;
            font-weight: 600;
            color: #374151;
            margin: 0 0 8px 0;
        }

        .empty-subtitle {
            font-size: 14px;
            color: #9CA3AF;
            margin: 0;
        }

        /* Footer */
        .notification-footer {
            background: #F8FAFC;
            padding: 12px 24px;
            border-top: 1px solid #E2E8F0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .btn-refresh {
            display: flex;
            align-items: center;
            gap: 8px;
            background: none;
            border: none;
            color: #3B82F6;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-refresh:hover {
            color: #2563EB;
            transform: translateX(2px);
        }

        .footer-divider {
            width: 1px;
            height: 16px;
            background: #E2E8F0;
        }

        .auto-refresh-text {
            font-size: 12px;
            color: #94A3B8;
            font-weight: 500;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .modern-notification-dropdown {
                width: 360px;
                margin-right: -20px;
            }

            .notification-header {
                padding: 16px 20px 12px;
            }

            .notification-item {
                padding: 12px 20px;
            }
        }

        @media (max-width: 480px) {
            .modern-notification-dropdown {
                width: 320px;
                margin-right: -40px;
            }
        }
    </style>
@endpush

@push('js')
    <!-- ✅ SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        class NotificationSystem {
            constructor() {
                console.log('🔔 Modern NotificationSystem initialized');
                this.init();
                this.loadNotifications();
                this.startPolling();
            }

            init() {
                // Event listeners
                document.getElementById('markAllRead')?.addEventListener('click', () => {
                    this.markAllAsRead();
                });

                document.getElementById('notificationDropdown')?.addEventListener('click', () => {
                    this.loadNotifications();
                });
            }

            async loadNotifications() {
                const loading = document.getElementById('notificationLoading');
                const list = document.getElementById('notificationList');
                const empty = document.getElementById('notificationEmpty');
                const subtitle = document.getElementById('notificationSubtitle');

                if (!loading || !list || !empty || !subtitle) {
                    console.error('Required elements not found');
                    return;
                }

                loading.style.display = 'block';
                list.innerHTML = '';
                empty.style.display = 'none';

                try {
                    const response = await fetch('/api/mahasiswa/notifications', {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    const data = await response.json();

                    if (data.success && data.data.length > 0) {
                        this.renderNotifications(data.data);
                        subtitle.textContent = `${data.data.length} notifikasi`;
                    } else {
                        empty.style.display = 'block';
                        subtitle.textContent = 'Tidak ada notifikasi';
                    }
                } catch (error) {
                    console.error('Error loading notifications:', error);
                    list.innerHTML = `
                                <div style="padding: 32px 24px; text-align: center;">
                                    <div style="color: #EF4444; font-size: 14px;">
                                        <i class="bi bi-exclamation-triangle"></i>
                                        Gagal memuat notifikasi
                                    </div>
                                </div>
                            `;
                } finally {
                    loading.style.display = 'none';
                }
            }

            renderNotifications(notifications) {
                const list = document.getElementById('notificationList');

                list.innerHTML = notifications.map(notif => `
            <div class="notification-item ${!notif.is_read ? 'unread' : ''}" 
                 data-id="${notif.id_notifikasi}">
                <div class="d-flex align-items-start gap-3">
                    <div class="notification-icon-wrapper ${this.getIconClass(notif.kategori)}">
                        <i class="bi ${this.getIcon(notif.kategori)}"></i>
                    </div>
                    <div class="notification-content" onclick="notificationSystem.markAsRead(${notif.id_notifikasi})">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <h6 class="notification-main-title">${notif.judul}</h6>
                            ${notif.is_important ? '<i class="bi bi-star-fill" style="color: #F59E0B; font-size: 12px;"></i>' : ''}
                        </div>
                        <p class="notification-message">${notif.pesan}</p>
                        <span class="notification-time">${notif.time_ago}</span>
                    </div>
                    <div class="notification-actions-column">
                        ${!notif.is_read ? '<div class="notification-dot"></div>' : '<div style="height: 8px;"></div>'}
                        <button class="btn-delete-notification" 
                                onclick="event.stopPropagation(); notificationSystem.deleteNotification(${notif.id_notifikasi})"
                                title="Hapus notifikasi">
                            <i class="bi bi-x" style="font-size: 12px;"></i>
                        </button>
                    </div>
                </div>
            </div>
        `).join('');
            }

            // Helper methods untuk icons
            getIcon(kategori) {
                const icons = {
                    'lamaran': 'bi-file-earmark-text',
                    'magang': 'bi-briefcase',
                    'sistem': 'bi-gear',
                    'pengumuman': 'bi-megaphone',
                    'evaluasi': 'bi-clipboard-check',
                    'deadline': 'bi-clock-history'
                };
                return icons[kategori] || 'bi-bell';
            }

            getIconClass(kategori) {
                return `icon-${kategori}`;
            }

            // ✅ FIXED: deleteNotification
            async deleteNotification(id) {
                // Prevent event bubbling
                event.stopPropagation();

                const result = await Swal.fire({
                    title: 'Hapus Notifikasi?',
                    text: 'Notifikasi yang dihapus tidak dapat dikembalikan.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#EF4444',
                    cancelButtonColor: '#6B7280',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    customClass: {
                        popup: 'rounded-3',
                        confirmButton: 'rounded-2',
                        cancelButton: 'rounded-2'
                    }
                });

                if (!result.isConfirmed) return;

                try {
                    // Show loading state
                    const item = document.querySelector(`[data-id="${id}"]`);
                    if (item) {
                        item.style.opacity = '0.5';
                        item.style.pointerEvents = 'none';
                    }

                    const response = await fetch(`/api/mahasiswa/notifications/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const resultData = await response.json();
                    console.log('Delete response:', resultData); // Debug log

                    if (resultData.success) {
                        // Animate removal
                        if (item) {
                            item.style.transition = 'all 0.3s ease';
                            item.style.opacity = '0';
                            item.style.transform = 'translateX(-100%)';
                            item.style.maxHeight = '0';
                            item.style.padding = '0 24px';
                            item.style.margin = '0';

                            setTimeout(() => {
                                item.remove();

                                // Check if list is empty after removal
                                const list = document.getElementById('notificationList');
                                const empty = document.getElementById('notificationEmpty');
                                const subtitle = document.getElementById('notificationSubtitle');

                                if (list && list.children.length === 0) {
                                    if (empty) empty.style.display = 'block';
                                    if (subtitle) subtitle.textContent = 'Tidak ada notifikasi';
                                } else if (subtitle) {
                                    subtitle.textContent = `${list.children.length} notifikasi`;
                                }

                                // Update count after DOM manipulation
                                this.updateCount();
                            }, 300);
                        }

                        this.showToast('Notifikasi berhasil dihapus', 'success');
                    } else {
                        // Restore item state on failure
                        if (item) {
                            item.style.opacity = '1';
                            item.style.pointerEvents = 'auto';
                        }

                        Swal.fire({
                            title: 'Gagal!',
                            text: resultData.message || 'Gagal menghapus notifikasi',
                            icon: 'error',
                            customClass: { popup: 'rounded-3' }
                        });
                    }
                } catch (error) {
                    console.error('Error deleting notification:', error);

                    // Restore item state on error
                    const item = document.querySelector(`[data-id="${id}"]`);
                    if (item) {
                        item.style.opacity = '1';
                        item.style.pointerEvents = 'auto';
                    }

                    Swal.fire({
                        title: 'Error!',
                        text: 'Terjadi kesalahan saat menghapus notifikasi. Silakan coba lagi.',
                        icon: 'error',
                        customClass: { popup: 'rounded-3' }
                    });
                }
            }

            // ✅ FIXED: clearAll
            async clearAll() {
                const result = await Swal.fire({
                    title: 'Hapus Semua Notifikasi?',
                    html: `
                                <p>Semua notifikasi akan dihapus permanen.</p>
                                <p class="text-danger"><strong>Tindakan ini tidak dapat dibatalkan!</strong></p>
                            `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#EF4444',
                    cancelButtonColor: '#6B7280',
                    confirmButtonText: 'Ya, Hapus Semua!',
                    cancelButtonText: 'Batal',
                    customClass: {
                        popup: 'rounded-3',
                        confirmButton: 'rounded-2',
                        cancelButton: 'rounded-2'
                    }
                });

                if (!result.isConfirmed) return;

                try {
                    const response = await fetch('/api/mahasiswa/notifications', {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    const resultData = await response.json();

                    if (resultData.success) {
                        this.loadNotifications();
                        this.updateCount();

                        await Swal.fire({
                            title: 'Berhasil!',
                            text: `${resultData.deleted_count} notifikasi berhasil dihapus`,
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false,
                            customClass: { popup: 'rounded-3' }
                        });
                    } else {
                        Swal.fire({
                            title: 'Gagal!',
                            text: 'Gagal menghapus semua notifikasi',
                            icon: 'error',
                            customClass: { popup: 'rounded-3' }
                        });
                    }
                } catch (error) {
                    console.error('Error clearing all notifications:', error);
                    Swal.fire({
                        title: 'Error!',
                        text: 'Terjadi kesalahan saat menghapus notifikasi',
                        icon: 'error',
                        customClass: { popup: 'rounded-3' }
                    });
                }
            }

            // ✅ FIXED: clearRead
            async clearRead() {
                const result = await Swal.fire({
                    title: 'Hapus Notifikasi yang Dibaca?',
                    text: 'Semua notifikasi yang sudah dibaca akan dihapus.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3B82F6',
                    cancelButtonColor: '#6B7280',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    customClass: {
                        popup: 'rounded-3',
                        confirmButton: 'rounded-2',
                        cancelButton: 'rounded-2'
                    }
                });

                if (!result.isConfirmed) return;

                try {
                    const response = await fetch('/api/mahasiswa/notifications/read', {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    const resultData = await response.json();

                    if (resultData.success) {
                        this.loadNotifications();
                        this.updateCount();

                        if (resultData.deleted_count > 0) {
                            await Swal.fire({
                                title: 'Berhasil!',
                                text: `${resultData.deleted_count} notifikasi yang dibaca berhasil dihapus`,
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false,
                                customClass: { popup: 'rounded-3' }
                            });
                        } else {
                            Swal.fire({
                                title: 'Info',
                                text: 'Tidak ada notifikasi yang dibaca untuk dihapus',
                                icon: 'info',
                                customClass: { popup: 'rounded-3' }
                            });
                        }
                    } else {
                        Swal.fire({
                            title: 'Info',
                            text: 'Tidak ada notifikasi yang dibaca untuk dihapus',
                            icon: 'info',
                            customClass: { popup: 'rounded-3' }
                        });
                    }
                } catch (error) {
                    console.error('Error clearing read notifications:', error);
                    Swal.fire({
                        title: 'Error!',
                        text: 'Terjadi kesalahan saat menghapus notifikasi',
                        icon: 'error',
                        customClass: { popup: 'rounded-3' }
                    });
                }
            }

            // ✅ FIXED: clearExpired (hanya satu definisi)
            async clearExpired() {
                const result = await Swal.fire({
                    title: 'Hapus Notifikasi Kedaluwarsa?',
                    text: 'Notifikasi yang sudah kedaluwarsa akan dihapus.',
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonColor: '#F59E0B',
                    cancelButtonColor: '#6B7280',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    customClass: {
                        popup: 'rounded-3',
                        confirmButton: 'rounded-2',
                        cancelButton: 'rounded-2'
                    }
                });

                if (!result.isConfirmed) return;

                try {
                    const response = await fetch('/api/mahasiswa/notifications/expired', {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    const resultData = await response.json();

                    if (resultData.success) {
                        this.loadNotifications();
                        this.updateCount();

                        if (resultData.deleted_count > 0) {
                            await Swal.fire({
                                title: 'Berhasil!',
                                text: `${resultData.deleted_count} notifikasi kedaluwarsa berhasil dihapus`,
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false,
                                customClass: { popup: 'rounded-3' }
                            });
                        } else {
                            Swal.fire({
                                title: 'Info',
                                text: 'Tidak ada notifikasi kedaluwarsa untuk dihapus',
                                icon: 'info',
                                customClass: { popup: 'rounded-3' }
                            });
                        }
                    } else {
                        Swal.fire({
                            title: 'Info',
                            text: 'Tidak ada notifikasi kedaluwarsa untuk dihapus',
                            icon: 'info',
                            customClass: { popup: 'rounded-3' }
                        });
                    }
                } catch (error) {
                    console.error('Error clearing expired notifications:', error);
                    Swal.fire({
                        title: 'Error!',
                        text: 'Terjadi kesalahan saat menghapus notifikasi kedaluwarsa',
                        icon: 'error',
                        customClass: { popup: 'rounded-3' }
                    });
                }
            }

            // ✅ FIXED: updateCount
            async updateCount() {
                try {
                    const response = await fetch('/api/mahasiswa/notifications/count', {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    const data = await response.json();
                    const countElement = document.getElementById('notificationCount');

                    if (countElement) {
                        if (data.success && data.count > 0) {
                            countElement.textContent = data.count > 99 ? '99+' : data.count;
                            countElement.style.display = 'flex';
                        } else {
                            countElement.style.display = 'none';
                        }
                    }
                } catch (error) {
                    console.error('Error updating notification count:', error);
                }
            }

            async markAsRead(id) {
                try {
                    const response = await fetch(`/api/mahasiswa/notifications/${id}/read`, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    if (response.ok) {
                        const item = document.querySelector(`[data-id="${id}"]`);
                        if (item) {
                            item.classList.remove('unread');
                            const dot = item.querySelector('.notification-dot');
                            if (dot) {
                                dot.style.transition = 'all 0.3s ease';
                                dot.style.opacity = '0';
                                setTimeout(() => dot.remove(), 300);
                            }
                        }
                        this.updateCount();
                    }
                } catch (error) {
                    console.error('Error marking notification as read:', error);
                }
            }

            async markAllAsRead() {
                try {
                    const response = await fetch('/api/mahasiswa/notifications/mark-all-read', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    if (response.ok) {
                        document.querySelectorAll('.notification-item.unread').forEach(item => {
                            item.classList.remove('unread');
                            const dot = item.querySelector('.notification-dot');
                            if (dot) {
                                dot.style.transition = 'all 0.3s ease';
                                dot.style.opacity = '0';
                                setTimeout(() => dot.remove(), 300);
                            }
                        });
                        this.updateCount();
                        this.showToast('Semua notifikasi ditandai sebagai dibaca', 'success');
                    }
                } catch (error) {
                    console.error('Error marking all notifications as read:', error);
                }
            }

            // ✅ FIXED: showToast menggunakan SweetAlert2
            showToast(message, type = 'info') {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                });

                const iconMap = {
                    'success': 'success',
                    'error': 'error',
                    'info': 'info',
                    'warning': 'warning'
                };

                Toast.fire({
                    icon: iconMap[type] || 'info',
                    title: message
                });
            }

            // ✅ ADDED: Missing startPolling method
            startPolling() {
                // Update count immediately
                this.updateCount();

                // Then update every 30 seconds
                setInterval(() => {
                    this.updateCount();
                }, 30000);
            }
        }

        // ✅ FIXED: Single initialization pada DOMContentLoaded
        document.addEventListener('DOMContentLoaded', function () {
            window.notificationSystem = new NotificationSystem();
        });
    </script>
@endpush