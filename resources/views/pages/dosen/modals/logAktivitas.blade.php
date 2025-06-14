<div class="modal fade" id="logAktivitasModal" tabindex="-1" aria-labelledby="logAktivitasModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <div class="d-flex align-items-center">
                    <div class="icon-box me-3"
                        style="background: linear-gradient(135deg, #5988FF, #4c7bef); width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-clipboard-list text-white"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0" id="logAktivitasModalLabel">Log Aktivitas Mahasiswa</h5>
                        <p class="text-muted mb-0 small" id="mahasiswaInfo">Memuat informasi mahasiswa...</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-0">
                <!-- Loading State -->
                <div id="logAktivitasLoading" class="p-5 text-center">
                    <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <h6>Memuat Log Aktivitas</h6>
                    <p class="text-muted">Mengambil data aktivitas mahasiswa...</p>
                </div>

                <!-- Error State -->
                <div id="logAktivitasError" class="p-5 text-center d-none">
                    <div class="mb-3">
                        <i class="fas fa-exclamation-triangle text-warning" style="font-size: 3rem;"></i>
                    </div>
                    <h6>Gagal Memuat Data</h6>
                    <p class="text-muted mb-3" id="errorMessage">Terjadi kesalahan saat memuat log aktivitas</p>
                    <button class="btn btn-primary" onclick="retryLoadLogAktivitas()">
                        <i class="fas fa-sync-alt me-2"></i>Coba Lagi
                    </button>
                </div>

                <!-- Empty State -->
                <div id="logAktivitasEmpty" class="p-5 text-center d-none">
                    <div class="mb-3">
                        <i class="fas fa-journal-text text-muted" style="font-size: 3rem;"></i>
                    </div>
                    <h6>Belum Ada Aktivitas</h6>
                    <p class="text-muted">Mahasiswa belum mencatat aktivitas magang</p>
                </div>

                <!-- Content -->
                <div id="logAktivitasContent" class="d-none">
                    <!-- Stats Cards -->
                    <div class="row g-3 p-4 pb-0">
                        <div class="col-md-3">
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fas fa-calendar-check"></i>
                                </div>
                                <div class="stat-info">
                                    <h6 class="mb-0" id="totalDays">0</h6>
                                    <small class="text-muted">Total Hari</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fas fa-tasks"></i>
                                </div>
                                <div class="stat-info">
                                    <h6 class="mb-0" id="totalActivities">0</h6>
                                    <small class="text-muted">Aktivitas</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fas fa-images"></i>
                                </div>
                                <div class="stat-info">
                                    <h6 class="mb-0" id="totalPhotos">0</h6>
                                    <small class="text-muted">Foto</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fas fa-calendar-day"></i>
                                </div>
                                <div class="stat-info">
                                    <h6 class="mb-0" id="lastActivity">-</h6>
                                    <small class="text-muted">Aktivitas Terakhir</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filter and Search -->
                    <div class="px-4 py-3 border-bottom bg-light">
                        <div class="row g-3 align-items-center">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0">
                                        <i class="fas fa-search text-muted"></i>
                                    </span>
                                    <input type="text" class="form-control border-start-0" id="searchLogAktivitas"
                                        placeholder="Cari aktivitas...">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" id="filterMonth">
                                    <option value="">Semua Bulan</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" id="filterHasPhoto">
                                    <option value="">Semua</option>
                                    <option value="with">Dengan Foto</option>
                                    <option value="without">Tanpa Foto</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Timeline Container -->
                    <div class="timeline-container-modal" style="max-height: 500px; overflow-y: auto;">
                        <div id="timelineLogAktivitas" class="timeline-dosen p-4">
                            <!-- Timeline items will be populated here -->
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-top">
                <div class="d-flex justify-content-between w-100 align-items-center">
                    <div class="text-muted small">
                        <i class="fas fa-info-circle me-1"></i>
                        Data diurutkan dari aktivitas terbaru
                    </div>
                    <div>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Photo Detail Modal -->
<div class="modal fade" id="photoDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="photoDetailTitle">Detail Foto Aktivitas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img src="" alt="Foto Aktivitas" class="img-fluid rounded" id="photoDetailImage">
                <div class="mt-3">
                    <p class="text-muted mb-0" id="photoDetailDescription">Deskripsi aktivitas</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // ✅ GLOBAL VARIABLES untuk Log Aktivitas
    let currentMahasiswaId = null;
    let logAktivitasData = [];
    let filteredLogData = [];

    // ✅ MAIN LOG AKTIVITAS FUNCTION
    function logAktivitas(id_mahasiswa) {
        console.log('📋 Opening log aktivitas for mahasiswa:', id_mahasiswa);

        currentMahasiswaId = id_mahasiswa;

        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('logAktivitasModal'));
        modal.show();

        // Reset modal states
        resetLogAktivitasModal();

        // Load data
        loadLogAktivitasData(id_mahasiswa);
    }

    function resetLogAktivitasModal() {
        // Show loading, hide others
        document.getElementById('logAktivitasLoading').classList.remove('d-none');
        document.getElementById('logAktivitasError').classList.add('d-none');
        document.getElementById('logAktivitasEmpty').classList.add('d-none');
        document.getElementById('logAktivitasContent').classList.add('d-none');

        // Reset info
        document.getElementById('mahasiswaInfo').textContent = 'Memuat informasi mahasiswa...';

        // Reset filters
        document.getElementById('searchLogAktivitas').value = '';
        document.getElementById('filterMonth').innerHTML = '<option value="">Semua Bulan</option>';
        document.getElementById('filterHasPhoto').value = '';
    }

    async function loadLogAktivitasData(id_mahasiswa) {
        try {
            console.log('🔍 Loading log aktivitas data for:', id_mahasiswa);

            // Get mahasiswa info and log data
            const [mahasiswaResponse, logResponse] = await Promise.all([
                api.get(`/mahasiswa/${id_mahasiswa}/info`),
                api.get(`/mahasiswa/${id_mahasiswa}/logbook`)
            ]);

            // Handle mahasiswa info
            if (mahasiswaResponse.data.success) {
                const mahasiswa = mahasiswaResponse.data.data;
                document.getElementById('mahasiswaInfo').textContent =
                    `${mahasiswa.name} (${mahasiswa.nim}) - ${mahasiswa.nama_kelas}`;
            }

            // Handle log data
            if (logResponse.data.success) {
                logAktivitasData = logResponse.data.data || [];

                if (logAktivitasData.length === 0) {
                    showEmptyLogState();
                } else {
                    showLogContent();
                    populateLogTimeline(logAktivitasData);
                    updateLogStats(logAktivitasData);
                    populateMonthFilter(logAktivitasData);
                }
            } else {
                throw new Error(logResponse.data.message || 'Gagal memuat data log');
            }

        } catch (error) {
            console.error('❌ Error loading log aktivitas:', error);
            showLogError(error.message || 'Terjadi kesalahan saat memuat data');
        }
    }

    function showEmptyLogState() {
        document.getElementById('logAktivitasLoading').classList.add('d-none');
        document.getElementById('logAktivitasEmpty').classList.remove('d-none');
    }

    function showLogError(message) {
        document.getElementById('logAktivitasLoading').classList.add('d-none');
        document.getElementById('logAktivitasError').classList.remove('d-none');
        document.getElementById('errorMessage').textContent = message;
    }

    function showLogContent() {
        document.getElementById('logAktivitasLoading').classList.add('d-none');
        document.getElementById('logAktivitasContent').classList.remove('d-none');
    }

    function populateLogTimeline(data) {
        const timeline = document.getElementById('timelineLogAktivitas');
        if (!timeline || !data || data.length === 0) return;

        let timelineHTML = '';

        data.forEach(monthGroup => {
            timelineHTML += `
            <div class="timeline-month-dosen">
                <h6 class="month-label-dosen">${monthGroup.month}</h6>
                <div class="timeline-entries-dosen">
        `;

            monthGroup.entries.forEach(entry => {
                const photoHTML = entry.has_foto ? `
                <div class="timeline-photo-dosen">
                    <img src="${entry.foto}" alt="Foto aktivitas" 
                         onclick="showPhotoDetail('${entry.foto}', '${entry.tanggal_formatted}', '${entry.deskripsi}')">
                </div>
            ` : '';

                timelineHTML += `
                <div class="timeline-item-dosen" data-entry-id="${entry.id}" data-month="${monthGroup.month}" data-has-photo="${entry.has_foto ? 'yes' : 'no'}">
                    <div class="timeline-card-dosen">
                        <div class="timeline-header-dosen">
                            <div>
                                <div class="timeline-date-dosen">${entry.tanggal_formatted}</div>
                                <div class="timeline-day-dosen">${entry.tanggal_hari}</div>
                            </div>
                            <div class="timeline-time-dosen">${entry.time_ago}</div>
                        </div>
                        <div class="timeline-description-dosen">
                            ${entry.deskripsi}
                        </div>
                        ${photoHTML}
                        <div class="timeline-actions-dosen">
                            ${entry.has_foto ? `
                                <button class="btn btn-sm btn-outline-primary" onclick="showPhotoDetail('${entry.foto}', '${entry.tanggal_formatted}', '${entry.deskripsi}')">
                                    <i class="fas fa-image me-1"></i>Lihat Foto
                                </button>
                            ` : ''}
                        </div>
                    </div>
                </div>
            `;
            });

            timelineHTML += `
                </div>
            </div>
        `;
        });

        timeline.innerHTML = timelineHTML;
    }

    function updateLogStats(data) {
        let totalDays = 0;
        let totalActivities = 0;
        let totalPhotos = 0;
        let lastActivityDate = null;

        data.forEach(monthGroup => {
            monthGroup.entries.forEach(entry => {
                totalActivities++;
                if (entry.has_foto) totalPhotos++;

                if (!lastActivityDate || new Date(entry.tanggal) > new Date(lastActivityDate)) {
                    lastActivityDate = entry.tanggal_formatted;
                }
            });
            totalDays += monthGroup.entries.length;
        });

        document.getElementById('totalDays').textContent = totalDays;
        document.getElementById('totalActivities').textContent = totalActivities;
        document.getElementById('totalPhotos').textContent = totalPhotos;
        document.getElementById('lastActivity').textContent = lastActivityDate || '-';
    }

    function populateMonthFilter(data) {
        const filterMonth = document.getElementById('filterMonth');
        const months = [...new Set(data.map(monthGroup => monthGroup.month))];

        filterMonth.innerHTML = '<option value="">Semua Bulan</option>';
        months.forEach(month => {
            filterMonth.innerHTML += `<option value="${month}">${month}</option>`;
        });
    }

    function showPhotoDetail(photoUrl, tanggal, deskripsi) {
        const modal = new bootstrap.Modal(document.getElementById('photoDetailModal'));
        document.getElementById('photoDetailTitle').textContent = `Foto Aktivitas - ${tanggal}`;
        document.getElementById('photoDetailImage').src = photoUrl;
        document.getElementById('photoDetailDescription').textContent = deskripsi;
        modal.show();
    }

    function viewActivityDetail(entryId) {
        // Find the entry in data
        let entry = null;
        logAktivitasData.forEach(monthGroup => {
            const found = monthGroup.entries.find(e => e.id === entryId);
            if (found) entry = found;
        });

        if (!entry) return;

        Swal.fire({
            title: `Detail Aktivitas - ${entry.tanggal_formatted}`,
            html: `
            <div class="text-start">
                <div class="mb-3">
                    <strong>Tanggal:</strong> ${entry.tanggal_formatted} (${entry.tanggal_hari})
                </div>
                <div class="mb-3">
                    <strong>Deskripsi:</strong><br>
                    <div class="bg-light p-3 rounded mt-2">${entry.deskripsi}</div>
                </div>
                ${entry.has_foto ? `
                    <div class="mb-3">
                        <strong>Foto:</strong><br>
                        <img src="${entry.foto}" class="img-fluid rounded mt-2" style="max-height: 200px;">
                    </div>
                ` : ''}
                <div class="mb-3">
                    <strong>Dicatat:</strong> ${entry.time_ago}
                </div>
            </div>
        `,
            width: '600px',
            showConfirmButton: false,
            showCloseButton: true,
            customClass: {
                container: 'activity-detail-modal'
            }
        });
    }

    function retryLoadLogAktivitas() {
        if (currentMahasiswaId) {
            resetLogAktivitasModal();
            loadLogAktivitasData(currentMahasiswaId);
        }
    }

    function exportLogAktivitas() {
        if (!currentMahasiswaId) return;

        Swal.fire({
            title: 'Export Log Aktivitas',
            text: 'Fitur export PDF akan segera tersedia',
            icon: 'info',
            confirmButtonText: 'Ok'
        });
    }

    function filterLogActivities() {
        const searchTerm = document.getElementById('searchLogAktivitas').value.toLowerCase();
        const selectedMonth = document.getElementById('filterMonth').value;
        const photoFilter = document.getElementById('filterHasPhoto').value;

        const timelineItems = document.querySelectorAll('.timeline-item-dosen');

        timelineItems.forEach(item => {
            const description = item.querySelector('.timeline-description-dosen').textContent.toLowerCase();
            const date = item.querySelector('.timeline-date-dosen').textContent.toLowerCase();
            const month = item.getAttribute('data-month');
            const hasPhoto = item.getAttribute('data-has-photo');

            let showItem = true;

            // Search filter
            if (searchTerm && !description.includes(searchTerm) && !date.includes(searchTerm)) {
                showItem = false;
            }

            // Month filter
            if (selectedMonth && month !== selectedMonth) {
                showItem = false;
            }

            // Photo filter
            if (photoFilter) {
                if (photoFilter === 'with' && hasPhoto !== 'yes') {
                    showItem = false;
                } else if (photoFilter === 'without' && hasPhoto !== 'no') {
                    showItem = false;
                }
            }

            if (showItem) {
                item.classList.remove('filtered-out');
                item.classList.add('filtered-in');
            } else {
                item.classList.remove('filtered-in');
                item.classList.add('filtered-out');
            }
        });

        // Hide/show month groups if all items are hidden
        const monthGroups = document.querySelectorAll('.timeline-month-dosen');
        monthGroups.forEach(group => {
            const visibleItems = group.querySelectorAll('.timeline-item-dosen.filtered-in');
            if (visibleItems.length === 0) {
                group.style.display = 'none';
            } else {
                group.style.display = 'block';
            }
        });
    }

    // ✅ Event listeners for filters
    document.addEventListener('DOMContentLoaded', function() {
        // Search filter
        const searchInput = document.getElementById('searchLogAktivitas');
        if (searchInput) {
            searchInput.addEventListener('input', debounce(function() {
                filterLogActivities();
            }, 300));
        }

        // Month filter
        const monthFilter = document.getElementById('filterMonth');
        if (monthFilter) {
            monthFilter.addEventListener('change', filterLogActivities);
        }

        // Photo filter
        const photoFilter = document.getElementById('filterHasPhoto');
        if (photoFilter) {
            photoFilter.addEventListener('change', filterLogActivities);
        }
    });
</script>
