<!DOCTYPE html>
<html>

<head>
    <title>Home</title>
    <style>
        .table-custom {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
        }

        .table-custom th,
        .table-custom td {
            border: 1px solid #dee2e6;
            padding: 12px;
            vertical-align: middle;
        }

        .table-custom thead th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
        }

        .kbm-filter {
            width: 100%;
            padding: 4px 8px;
            margin-bottom: 10px;
            border: 1px solid #dee2e6;
            border-radius: 4px;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>
    <h2>Halo, {{ session('admin_role') }} {{ session('admin_username') }}</h2>
    <a href="{{ route('logout') }}">Logout</a>
    <br><br>
    <div id="alert-container"></div>

    {{-- ================= GURU ================= --}}
    @if (session('admin_role') === 'guru' && $guru)
        <div class="container mt-4">
            <div class="card">
                <div class="card-body">
                    <h3>Data Guru</h3>
                    <p><b>Nama :</b> {{ $guru->nama }}</p>
                    <p><b>Mapel :</b> {{ $guru->mapel }}</p>
                </div>
            </div>
        </div>

        {{-- Jika guru ini walas --}}
        @if($guru->walas)
            <div class="container mt-5">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Wali Kelas: {{ $guru->walas->namakelas }} ({{ $guru->walas->jenjang }})</h4>
                    </div>
                    <div class="card-body">
                        <h5 class="mb-3">Daftar Siswa:</h5>

                        <div class="card">
                            <div class="card-body">
                                <table class="table table-hover table-custom">
                                    <thead>
                                        <tr>
                                            <th scope="col" width="5%">No</th>
                                            <th scope="col" width="45%">Nama</th>
                                            <th scope="col" width="25%">Tinggi Badan</th>
                                            <th scope="col" width="25%">Berat Badan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($guru->walas->kelas as $i => $kelas)
                                            <tr>
                                                <th scope="row">{{ $i + 1 }}</th>
                                                <td>{{ $kelas->siswa->nama }}</td>
                                                <td>{{ $kelas->siswa->tb }} cm</td>
                                                <td>{{ $kelas->siswa->bb }} kg</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
        @endif
                <br>
    @endif

            
            {{-- ================= SISWA ================= --}}
            @if (session('admin_role') === 'siswa' && $siswaLogin)
                <div class="container mt-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="mb-0">Data Siswa</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><b>Nama :</b> {{ $siswaLogin->nama }}</p>
                                    <p><b>Berat Badan :</b> {{ $siswaLogin->bb }} kg</p>
                                    <p><b>Tinggi Badan :</b> {{ $siswaLogin->tb }} cm</p>
                                </div>
                                @if($siswaLogin->kelas)
                                    <div class="col-md-6">
                                        <p><b>Kelas :</b> {{ $siswaLogin->kelas->walas->namakelas }}
                                            ({{ $siswaLogin->kelas->walas->jenjang }})</p>
                                        <p><b>Wali Kelas :</b> {{ $siswaLogin->kelas->walas->guru->nama }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- ================= TABEL SISWA (HANYA ADMIN) ================= --}}
            @if (session('admin_role') === 'admin')
                <div class="container mt-5">
                    <h2 class="mb-4">Daftar Siswa</h2>
                    <div class="mb-3">
                        <a href="{{ route('siswa.create') }}">+ Tambah Siswa</a>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="mb-3">
                                <label>Cari Siswa: </label>
                                <input type="text" id="search" placeholder="Ketik nama...">
                            </div>
                            <table id="tabel-siswa" class="table table-hover table-custom">
                                <thead>
                                    <tr>
                                        <th scope="col" width="5%">No</th>
                                        <th scope="col" width="35%">Nama</th>
                                        <th scope="col" width="20%">Tinggi Badan</th>
                                        <th scope="col" width="20%">Berat Badan</th>
                                        <th scope="col" width="20%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <script>
                                        $(document).ready(function () {
                                            function renderTable(data) {
                                                let rows = '';
                                                if (data.length === 0) {

                                                    rows = '<tr><td colspan="5">Tidak ada data ditemukan</td></tr>';
                                                } else {
                                                    data.forEach((s, index) => {
                                                        rows += `
                                                                        <tr>
                                                                        <td>${index + 1}</td>
                                                                        <td>${s.nama}</td>
                                                                        <td>${s.tb}</td>
                                                                        <td>${s.bb}</td>
                                                                        @if (session('admin_role') === 'admin')
                                                                            <td>
                                                                            <a href="/siswa/${s.idsiswa}/edit">Edit</a> |
                                                                            <a href="#" class="delete-siswa" data-id="${s.idsiswa}">Hapus</a>
                                                                            </td>
                                                                        @endif
                                                                        </tr>
                                                                    `;
                                                    });
                                                }
                                                $('#tabel-siswa tbody').html(rows);
                                            }
                                            function loadSiswa() {

                                                $.ajax({
                                                    url: "{{ route('siswa.data') }}",
                                                    method: "GET",
                                                    success: function (response) {
                                                        renderTable(response);
                                                    },
                                                    error: function () {
                                                        alert('Gagal memuat data siswa.');
                                                    }
                                                });
                                            }
                                            loadSiswa();

                                            function searchSiswa(keyword) {
                                                $.ajax({
                                                    url: "{{ route('siswa.search') }}",
                                                    method: "GET",
                                                    data: { q: keyword },
                                                    success: function (response) {
                                                        renderTable(response);
                                                    },
                                                    error: function () {
                                                        console.error('Gagal mencari data siswa.');
                                                    }
                                                });
                                            }
                                            $('#search').on('keyup', function () {
                                                const keyword = $(this).val().trim();
                                                if (keyword.length > 0) {
                                                    searchSiswa(keyword);
                                                } else {
                                                    loadSiswa();
                                                }
                                            });

                                            // Handle delete siswa with AJAX
                                            $(document).on('click', '.delete-siswa', function(e) {
                                                e.preventDefault();
                                                const id = $(this).data('id');
                                                
                                                if (confirm('Yakin ingin menghapus?')) {
                                                    $.ajax({
                                                        url: `/siswa/${id}/delete`,
                                                        method: 'DELETE',
                                                        headers: {
                                                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                                        },
                                                        success: function(response) {
                                                            showAlert('success', 'Siswa berhasil dihapus');
                                                            loadSiswa();
                                                        },
                                                        error: function() {
                                                            showAlert('error', 'Gagal menghapus siswa');
                                                        }
                                                    });
                                                }
                                            });

                                            // Function to show alert messages
                                            function showAlert(type, message) {
                                                const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
                                                const alert = `<div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                                                    ${message}
                                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>`;
                                                $('#alert-container').html(alert);
                                                
                                                // Auto hide after 5 seconds
                                                setTimeout(() => {
                                                    $('#alert-container .alert').alert('close');
                                                }, 5000);
                                            }
                                        });
                                    </script>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            {{-- ================= TABEL KBM ================= --}}
            <div class="container mt-5">
                @if(session('admin_role') === 'admin')
                    <h2 class="mb-4">Jadwal Kegiatan Belajar Mengajar (KBM) - Semua Guru</h2>
                @elseif(session('admin_role') === 'guru' && isset($guru))
                    <h2 class="mb-4">Jadwal Mengajar Saya</h2>
                @elseif(session('admin_role') === 'siswa' && isset($kelasData))
                    <h2 class="mb-4">Jadwal Pelajaran Kelas Saya</h2>
                    <div class="alert alert-info">
                        <strong>Siswa:</strong> {{ $siswaLogin->nama }} |
                        <strong>Kelas:</strong> {{ $kelasData->namakelas }} ({{ $kelasData->jenjang }}) |
                        <strong>Wali Kelas:</strong> {{ $waliKelas->nama }}
                    </div>
                @endif

                <div id="alert-container"></div>

                <div class="card">
                    <div class="card-body">
                        @if(session('admin_role') === 'admin')
                        <div class="mb-3 row">
                            <div class="col">
                                <input type="text" class="form-control form-control-sm kbm-filter" data-column="guru" placeholder="Cari Guru...">
                            </div>
                            <div class="col">
                                <input type="text" class="form-control form-control-sm kbm-filter" data-column="mapel" placeholder="Cari Mapel...">
                            </div>
                            <div class="col">
                                <input type="text" class="form-control form-control-sm kbm-filter" data-column="kelas" placeholder="Cari Kelas...">
                            </div>
                            <div class="col">
                                <input type="text" class="form-control form-control-sm kbm-filter" data-column="jenjang" placeholder="Cari Jenjang...">
                            </div>
                            <div class="col">
                                <input type="text" class="form-control form-control-sm kbm-filter" data-column="hari" placeholder="Cari Hari...">
                            </div>
                        </div>
                        @endif
                        <table id="tabel-kbm" class="table table-hover table-custom">
                            <thead>
                                <tr>
                                    <th scope="col" width="5%">No</th>
                                    @if(session('admin_role') === 'admin')
                                        <th scope="col" width="20%">Nama Guru</th>
                                        <th scope="col" width="15%">Mata Pelajaran</th>
                                        <th scope="col" width="15%">Kelas</th>
                                        <th scope="col" width="25%">Jenjang</th>
                                    @elseif(session('admin_role') === 'siswa')
                                        <th scope="col" width="25%">Nama Guru</th>
                                        <th scope="col" width="25%">Mata Pelajaran</th>
                                    @endif
                                    @if(session('admin_role') === 'guru')
                                        <th scope="col" width="25%">Kelas</th>
                                        <th scope="col" width="25%">Jenjang</th>
                                    @endif
                                    <th scope="col" width="15%">Hari</th>
                                    <th scope="col" width="15%">Jam Mulai</th>
                                    <th scope="col" width="15%">Jam Selesai</th>
                                    @if(session('admin_role') === 'admin')
                                        <th scope="col" width="10%">Aksi</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>

                        <script>
                        $(document).ready(function() {
                            let kbmData = []; // Store the original data
                            
                            function filterKBMData() {
                                const filters = {};
                                $('.kbm-filter').each(function() {
                                    const column = $(this).data('column');
                                    const value = $(this).val().toLowerCase();
                                    if (value) {
                                        filters[column] = value;
                                        console.log(`Filter ${column}:`, value); // Debug log
                                    }
                                });
                                console.log('Active filters:', filters); // Debug log

                                const filteredData = kbmData.filter(jadwal => {
                                    return Object.entries(filters).every(([column, value]) => {
                                        try {
                                            // Convert both the search value and the data value to lowercase for comparison
                                            const searchValue = value.toLowerCase();
                                            
                                            switch(column) {
                                                case 'guru':
                                                    const guruNama = (jadwal.guru.nama || '').toString().toLowerCase();
                                                    return guruNama.includes(searchValue);
                                                case 'mapel':
                                                    const mapel = (jadwal.guru.mapel || '').toString().toLowerCase();
                                                    return mapel.includes(searchValue);
                                                case 'kelas':
                                                    const kelas = (jadwal.walas.namakelas || '').toString().toLowerCase();
                                                    return kelas.includes(searchValue);
                                                case 'jenjang':
                                                    const jenjang = (jadwal.walas.jenjang || '').toString().toLowerCase();
                                                    console.log('Comparing jenjang:', jenjang, 'with search:', searchValue);
                                                    return jenjang.includes(searchValue);
                                                case 'hari':
                                                    const hari = (jadwal.hari || '').toString().toLowerCase();
                                                    return hari.includes(searchValue);
                                                default:
                                                    return true;
                                            }
                                        } catch (error) {
                                            console.error('Filtering error:', error, 'Column:', column, 'Data:', jadwal);
                                            return false;
                                        }
                                    });
                                });

                                renderKBMTable(filteredData);
                            }

                            function renderKBMTable(data) {
                                let rows = '';
                                const role = '{{ session('admin_role') }}';
                                
                                if (data.length === 0) {
                                    const colSpan = role === 'admin' ? '8' : '5';
                                    let message = 'Belum ada jadwal pelajaran';
                                    if (role === 'guru') {
                                        message = 'Anda belum memiliki jadwal mengajar';
                                    } else if (role === 'siswa') {
                                        message = 'Belum ada jadwal pelajaran untuk kelas Anda';
                                    }
                                    rows = `<tr><td colspan="${colSpan}" class="text-center">${message}</td></tr>`;
                                } else {
                                    data.forEach((jadwal, index) => {
                                        rows += `<tr>
                                            <td>${index + 1}</td>`;
                                            
                                        if (role === 'admin') {
                                            rows += `
                                                <td>${jadwal.guru.nama}</td>
                                                <td>${jadwal.guru.mapel}</td>
                                                <td>${jadwal.walas.namakelas}</td>
                                                <td>${jadwal.walas.jenjang}</td>`;
                                        } else if (role === 'siswa') {
                                            rows += `
                                                <td>${jadwal.guru.nama}</td>
                                                <td>${jadwal.guru.mapel}</td>`;
                                        } else if (role === 'guru') {
                                            rows += `
                                                <td>${jadwal.walas.namakelas}</td>
                                                <td>${jadwal.walas.jenjang}</td>`;
                                        }
                                        
                                        rows += `
                                            <td>${jadwal.hari}</td>
                                            <td>${jadwal.mulai}</td>
                                            <td>${jadwal.selesai}</td>`;
                                            
                                        if (role === 'admin') {
                                            rows += `
                                                <td>
                                                    <a href="/kbm/kelas/${jadwal.idwalas}">Lihat Kelas</a>
                                                </td>`;
                                        }
                                        
                                        rows += `</tr>`;
                                    });
                                }
                                $('#tabel-kbm tbody').html(rows);
                            }

                            function loadKBM() {
                                $.ajax({
                                    url: "{{ route('kbm.data') }}",
                                    method: "GET",
                                    success: function(response) {
                                        console.log('KBM Data:', response); // Debug log
                                        kbmData = response; // Store the original data
                                        renderKBMTable(response);
                                    },
                                    error: function(xhr, status, error) {
                                        console.error('KBM Load Error:', error);
                                        showAlert('error', 'Gagal memuat data KBM.');
                                    }
                                });
                            }

                            // Add filter event handlers
                            $('.kbm-filter').on('keyup', function() {
                                filterKBMData();
                            });

                            // Show alert message
                            function showAlert(type, message) {
                                const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
                                const alert = `<div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                                    ${message}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>`;
                                $('#alert-container').html(alert);
                                
                                // Auto hide after 5 seconds
                                setTimeout(() => {
                                    $('#alert-container .alert').alert('close');
                                }, 5000);
                            }

                            // Initial load
                            loadKBM();
                        });
                        </script>
                    </div>
                </div>
            </div>
</body>

</html>