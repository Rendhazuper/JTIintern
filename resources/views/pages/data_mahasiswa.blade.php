@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Data Mahasiswa'])
    <div class="container-fluid py-4">
        <div class="card pt-4"> 
            <div class="d-flex justify-content-between mb-3 px-3">
                <!-- Filter Section -->
                <div class="d-flex gap-2">
                    <select id="prodiFilter" class="form-select form-select-sm" style="width: auto;">
                        <option value="">Semua Prodi</option>
                    </select>
                    <select id="kelasFilter" class="form-select form-select-sm" style="width: auto;">
                        <option value="">Semua Kelas</option>
                    </select>
                </div>
                <!-- Button Section -->
                <div class="d-flex gap-2">
                    <button type="button" class="btn" 
                            style="color: white; background: #02A232;" 
                            onclick="tambahMahasiswa()">
                        <i class="bi bi-plus-square-fill me-2"></i>Tambah Mahasiswa
                    </button>
                    <button type="button" class="btn" 
                            style="color: white; background: #5988FF;" 
                            onclick="importCSV()">
                        <i class="bi bi-plus-square-fill me-2"></i>Import CSV
                    </button>
                </div>
            </div>

            <div class="card-body px-0 pt-0 pb-2">
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Mahasiswa</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">NIM</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Skills</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                <th class="text-uppercase text-xxs text-secondary opacity-7">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="mahasiswa-table-body">
                            <!-- Data will be populated here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal Tambah Mahasiswa -->
<div class="modal fade" id="modalTambahMahasiswa" tabindex="-1" aria-labelledby="modalTambahMahasiswaLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="formTambahMahasiswa" onsubmit="submitTambahMahasiswa(event)">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalTambahMahasiswaLabel">Tambah Mahasiswa</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="nama" class="form-label">Nama Mahasiswa</label>
            <input type="text" id="nama" name="name" class="form-control" required>
          </div>
          <div class="mb-3">
            <label for="namaKelas" class="form-label">Pilih Kelas</label>
            <select id="namaKelas" name="namaKelas" class="form-select" required>
              <option value="">-- Pilih Kelas --</option>
              <!-- Opsi kelas akan diisi oleh JS -->
            </select>
          </div>
          <div class="mb-3">
            <label for="alamat" class="form-label">Alamat</label>
            <input type="text" id="alamat" name="alamat" class="form-control" required>
          </div>
          <div class="mb-3">
            <label for="nim" class="form-label">NIM</label>
            <input type="text" id="nim" name="nim" class="form-control" maxlength="15" required>
          </div>
          <div class="mb-3">
            <label for="ipk" class="form-label">IPK</label>
            <input type="number" step="0.01" min="0" max="4" id="ipk" name="ipk" class="form-control" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Tambah</button>
        </div>
      </div>
    </form>
  </div>
</div>


        @include('layouts.footers.auth.footer')
@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
const api = axios.create({
    baseURL: '/api',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    withCredentials: true
});

function loadFilterOptions() {
    api.get('/kelas')
        .then(function(response) {
            if (response.data.success) {
                const prodiFilter = document.getElementById('prodiFilter');
                const kelasFilter = document.getElementById('kelasFilter');
                
                // Get unique prodi
                const prodis = [...new Set(response.data.data.map(k => k.prodi.nama_prodi))];
                prodis.forEach(prodi => {
                    prodiFilter.innerHTML += `<option value="${prodi}">${prodi}</option>`;
                });
                
                // Get all kelas
                response.data.data.forEach(kelas => {
                    kelasFilter.innerHTML += `
                        <option value="${kelas.nama_kelas}">${kelas.nama_kelas}</option>
                    `;
                });
            }
        });
}

function loadMahasiswaData(filters = {}) {
    api.get('/mahasiswa', { params: filters })
        .then(function(response) {
            if (response.data.success) {
                const tableBody = document.getElementById('mahasiswa-table-body');
                tableBody.innerHTML = '';
                
                response.data.data.forEach(mahasiswa => {
                    // Only show if matches filters
                    if ((filters.prodi && mahasiswa.kelas.prodi !== filters.prodi) ||
                        (filters.kelas && mahasiswa.kelas.nama !== filters.kelas)) {
                        return;
                    }
                    
                    tableBody.innerHTML += `
                        <tr>
                            <td>
                                <div class="d-flex px-2 py-1">
                                    <div class="d-flex flex-column justify-content-center">
                                        <h6 class="mb-0 text-sm">${mahasiswa.user.name}</h6>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <p class="text-xs font-weight-bold mb-0">${mahasiswa.nim}</p>
                            </td>
                            <td class="align-middle text-center text-sm">
                                ${mahasiswa.skills.map(skill => 
                                    `<span class="badge badge-sm bg-gradient-info">${skill.nama_skill}</span>`
                                ).join(' ') || '-'}
                            </td>
                            <td class="align-middle text-center">
                                <span class="badge badge-sm ${mahasiswa.status_magang === 'Sedang Magang' ? 'bg-gradient-success' : 'bg-gradient-secondary'}">
                                    ${mahasiswa.status_magang === 'Sedang Magang' ? 'Sedang Magang' : 'Belum Magang'}
                                </span>
                            </td>
                            <td class="align-middle">
                                <div style=" padding-top: 10px; padding-bottom: 10px; background: white; display: inline-flex; align-items: center; gap: 10px;">
                                    <a href="javascript:;" onclick="editMahasiswa(${mahasiswa.id_mahasiswa})" 
                                       style="padding: 5px 25px; background: #5988FF; border-radius: 15px; text-decoration: none; display: flex; justify-content: center; align-items: center;">
                                        <span style="color: white; font-size: 10px; font-family: 'Open Sans', sans-serif; font-weight: 700;">Detail</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    `;
                });
            }
        })
        .catch(function(error) {
            console.error('Error:', error);
            alert('Gagal memuat data mahasiswa');
        });
}

// Load data when page loads
document.addEventListener('DOMContentLoaded', function() {
    loadFilterOptions();
    loadMahasiswaData();
});

// Add event listeners for filters
document.getElementById('prodiFilter').addEventListener('change', function(e) {
    loadMahasiswaData({ prodi: e.target.value });
});

document.getElementById('kelasFilter').addEventListener('change', function(e) {
    loadMahasiswaData({ kelas: e.target.value });
});

function tambahMahasiswa() {
    // Isi dropdown kelas dulu jika belum terisi
    const selectKelas = document.getElementById('namaKelas');
    if (selectKelas.options.length <= 1) {
        api.get('/kelas')
            .then(res => {
                if (res.data.success) {
                    res.data.data.forEach(kelas => {
                        const option = document.createElement('option');
                        option.value = kelas.nama_kelas;
                        option.text = `${kelas.nama_kelas} - ${kelas.prodi.nama_prodi} (${kelas.tahun_masuk})`;
                        selectKelas.appendChild(option);
                    });
                }
            });
    }
    // Tampilkan modal
    var modal = new bootstrap.Modal(document.getElementById('modalTambahMahasiswa'));
    modal.show();
}

function submitTambahMahasiswa(event) {
    event.preventDefault();
    const form = event.target;

    const data = {
        nama: form.nama.value,
        nama_kelas: form.namaKelas.value,
        alamat: form.alamat.value,
        nim: form.nim.value,
        ipk: form.ipk.value
    };

    api.post('/mahasiswa', data)
        .then(res => {
            if (res.data.success) {
                alert('Mahasiswa berhasil ditambahkan!');
                // Tutup modal
                var modal = bootstrap.Modal.getInstance(document.getElementById('modalTambahMahasiswa'));
                modal.hide();

                // Reset form
                form.reset();

                // Reload data mahasiswa
                loadMahasiswaData();
            } else {
                alert('Gagal menambahkan mahasiswa: ' + (res.data.message || 'Error tidak diketahui'));
            }
        })
        .catch(err => {
            console.error(err);
            alert('Terjadi kesalahan saat menambahkan mahasiswa.');
        });
}

</script>
@endpush