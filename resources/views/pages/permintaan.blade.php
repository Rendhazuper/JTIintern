@extends('layouts.app')

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Permintaan Magang'])
    <div class="card">
        <div class="card-header px-4 py-3">
            <div class = "search_card"> 
            <div class="search-filter d-flex gap-3">
                <div class="search-box">
                    <input type="text" class="form-control search-input" placeholder="Cari Lowongan">
                    <i class="bi bi-search search-icon"></i>
                </div>
                <button class="btn filter-btn">
                    <i class="bi bi-funnel"></i>
                    <span>Periode</span>
                </button>
            </div>
            </div>
        </div>

        <div class="card-body px-4">
            <div class="permintaan-list">
                <div class="permintaan-item">
                    <div class="mahasiswa-info">
                        <h6 class="nama">Rendha Putra Rahmadya</h6>
                        <p class="nim " >NIM : 2341720010</p>
                    </div>
                    
                    <div class="posisi">
                        <span class="job-title font-weight-bold">BackEnd Developer</span>
                    </div>

                    <div class="perusahaan">
                        <span style = "border : 1px solid #5988ff" class="company-badge font-weight-bold ">PT. Indah Nusantara</span>
                    </div>

                    <div class="status">
                        <span class="status-badge ditolak font-weight-bold">Diterima</span>
                    </div>

                    <div class="action">
                        <div class="hover-actions">
                            <button class="btn btn-tolak">
                                <i class="bi bi-x"></i>
                                Tolak
                            </button>
                            <button class="btn btn-terima">
                                <i class="bi bi-check2"></i>
                                Terima
                            </button>
                            <button class="btn btn-detail" data-bs-toggle="modal" data-bs-target="#detailModal" onclick="showDetail(1)">
                                Detail
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    @include('components.modals.detail_permintaan')
@endsection

@push('css')
<link href="{{ asset('assets/css/permintaan.css') }}" rel="stylesheet" />
@endpush

@push('js')
<script>
function showDetail(id) {
    // Get CSRF token from meta tag
    const token = document.querySelector('meta[name="csrf-token"]').content;
    
    // Add show-actions class to keep buttons visible
    const hoverActions = event.target.closest('.permintaan-item').querySelector('.hover-actions');
    hoverActions.classList.add('show-actions');

    // Make AJAX request
    fetch(`/api/permintaan/${id}`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': token
        },
        credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(response => {
        if (response.success) {
            const data = response.data;
            
            // Update modal content
            document.getElementById('detail-nama').textContent = data.mahasiswa.nama;
            document.getElementById('detail-nim').textContent = `NIM: ${data.mahasiswa.nim}`;
            document.getElementById('detail-posisi').textContent = `Posisi: ${data.posisi}`;
            document.getElementById('detail-perusahaan').textContent = `Perusahaan: ${data.perusahaan.nama}`;
            
            // Update download links
            document.getElementById('download-surat').href = data.surat_url;
            document.getElementById('download-cv').href = data.cv_url;
            
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('detailModal'));
            modal.show();
        } else {
            alert('Gagal memuat detail: ' + response.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Gagal memuat detail permintaan');
        // Remove show-actions class if modal fails to open
        hoverActions.classList.remove('show-actions');
    });
}

// Add CSS class to keep hover actions visible
document.addEventListener('click', function(event) {
    if (!event.target.closest('.permintaan-item') && 
        !event.target.closest('.modal')) {
        document.querySelectorAll('.hover-actions.show-actions').forEach(el => {
            el.classList.remove('show-actions');
        });
    }
});

// Modal cleanup
document.getElementById('detailModal').addEventListener('hidden.bs.modal', function () {
    document.body.classList.remove('modal-open');
    document.body.style.removeProperty('padding-right');
    const modalBackdrop = document.querySelector('.modal-backdrop');
    if (modalBackdrop) {
        modalBackdrop.remove();
    }
    
    // Reset any scrollbar adjustments
    document.body.style.overflow = '';
});
</script>
@endpush
