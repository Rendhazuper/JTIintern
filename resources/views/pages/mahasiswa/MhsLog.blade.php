@extends('layouts.app', ['class' => 'bg-gray-100'])

@section('content')
    @include('layouts.navbars.mahasiswa.topnav')
    
    <div class="container-fluid px-10">
        <div class="row">
            <!-- Logbook Form Column -->
            <div class="col-lg-6">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <h6>Logbook Aktivitas Magang</h6>
                    </div>
                    <div class="card-body">
                        <form id="logbookForm" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="tanggal" class="form-label">Tanggal</label>
                                <input type="date" class="form-control" id="tanggal" name="tanggal" required>
                            </div>
                            <div class="mb-3">
                                <label for="deskripsi" class="form-label">Deskripsi</label>
                                <textarea class="form-control" id="deskripsi" name="deskripsi" rows="4" placeholder="Deskripsikan kegiatan mu" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="foto" class="form-label">Foto Aktivitas</label>
                                <div class="input-group">
                                    <input type="file" class="form-control" id="foto" name="foto" accept="image/*" onchange="previewImage(this)">
                                    <label class="input-group-text" for="foto">Upload</label>
                                </div>
                                <div class="form-text">Format yang diizinkan: JPG, PNG, JPEG. Maksimal 2MB</div>
                                <div id="imagePreview" class="mt-2 d-none">
                                    <img src="" alt="Preview" class="img-thumbnail" style="max-height: 200px;">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary" id="simpanAktivitas">
                                <i class="fas fa-save me-2"></i>Simpan Aktivitas
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Activity History Column -->
            <div class="col-lg-6">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <h6>Riwayat Aktivitas</h6>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            <div class="timeline-item">
                                <div class="timeline-date">30 Mei 2025</div>
                                <div class="timeline-content">
                                    <p>Lorem ipsum dolor sit amet consectetur. Pulvinar sapien justo diam ante. Mauris faucibus at sem cursus urna vel enim. Morbi mi velit in etiam viverra aliquam quisque pellentesque cursus. Pellentesque faucibus neque at vel proin auctor facilisis eu.</p>
                                </div>
                            </div>
                            <div class="timeline-item">
                                <div class="timeline-date">30 Mei 2025</div>
                                <div class="timeline-content">
                                    <p>Lorem ipsum dolor sit amet consectetur. Pulvinar sapien justo diam ante. Mauris faucibus at sem cursus urna vel enim. Morbi mi velit in etiam viverra aliquam quisque pellentesque cursus. Pellentesque faucibus neque at vel proin auctor facilisis eu.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
   <link rel="stylesheet" href="{{ asset('assets/css/Mahasiswa/logaktivitas.css') }}">
@endpush

@push('js')
<script>
    function previewImage(input) {
        const preview = document.getElementById('imagePreview');
        const previewImg = preview.querySelector('img');

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                preview.classList.remove('d-none');
            }
            
            reader.readAsDataURL(input.files[0]);
        } else {
            previewImg.src = '';
            preview.classList.add('d-none');
        }
    }

    document.getElementById('logbookForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        // Add validation for image size
        const foto = document.getElementById('foto').files[0];
        if (foto && foto.size > 2 * 1024 * 1024) {
            alert('Ukuran file tidak boleh lebih dari 2MB');
            return;
        }

        // Example API call with image upload
        api.post('/mahasiswa/logbook', formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        })
        .then(response => {
            if (response.data.success) {
                // Reset form and preview
                this.reset();
                document.getElementById('imagePreview').classList.add('d-none');
                // Refresh timeline or add new entry
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Gagal menyimpan aktivitas');
        });
    });
</script>
@endpush