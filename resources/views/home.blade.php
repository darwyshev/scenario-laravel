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
    </style>
</head>
<body>
    <h2>Halo, {{ session('admin_role') }} {{ session('admin_username') }}</h2>
    <a href="{{ route('logout') }}">Logout</a>
    <br><br>

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
                            <p><b>Kelas :</b> {{ $siswaLogin->kelas->walas->namakelas }} ({{ $siswaLogin->kelas->walas->jenjang }})</p>
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
                <a href="{{ route('siswa.create') }}" class="btn btn-primary">
                    + Tambah Siswa
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <table class="table table-hover table-custom">
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
                            @foreach($siswa as $i => $s)
                                <tr>
                                    <th scope="row">{{ $i + 1 }}</th>
                                    <td>{{ $s->nama }}</td>
                                    <td>{{ $s->tb }} cm</td>
                                    <td>{{ $s->bb }} kg</td>
                                    <td>
                                        <a href="{{ route('siswa.edit', $s->idsiswa) }}" class="btn btn-sm btn-warning">Edit</a>
                                        <a href="{{ route('siswa.delete', $s->idsiswa) }}" 
                                           class="btn btn-sm btn-danger"
                                           onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                                    </td>
                                </tr>
                            @endforeach
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
        
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="card">
            <div class="card-body">
                <table class="table table-hover table-custom">
                    <thead>
                        <tr>
                            <th scope="col" width="5%">No</th>
                            @if(session('admin_role') === 'admin')
                                <th scope="col" width="20%">Nama Guru</th>
                                <th scope="col" width="15%">Mata Pelajaran</th>
                                <th scope="col" width="15%">Kelas</th>
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
                        @forelse ($jadwals as $i => $jadwal)
                        <tr>
                            <th scope="row">{{ $i + 1 }}</th>
                            @if(session('admin_role') === 'admin')
                                <td>{{ $jadwal->guru->nama }}</td>
                                <td>{{ $jadwal->guru->mapel }}</td>
                                <td>{{ $jadwal->walas->namakelas }}</td>
                            @elseif(session('admin_role') === 'siswa')
                                <td>{{ $jadwal->guru->nama }}</td>
                                <td>{{ $jadwal->guru->mapel }}</td>
                            @endif
                            @if(session('admin_role') === 'guru')
                                <td>{{ $jadwal->walas->namakelas }}</td>
                                <td>{{ $jadwal->walas->jenjang }}</td>
                            @endif
                            <td>{{ $jadwal->hari }}</td>
                            <td>{{ $jadwal->mulai }}</td>
                            <td>{{ $jadwal->selesai }}</td>
                            @if(session('admin_role') === 'admin')
                                <td>
                                    <a href="{{ route('kbm.by-kelas', $jadwal->idwalas) }}" class="btn btn-sm btn-info">Lihat Kelas</a>
                                </td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ session('admin_role') === 'admin' ? '8' : '5' }}" class="text-center">
                                @if(session('admin_role') === 'guru')
                                    Anda belum memiliki jadwal mengajar
                                @elseif(session('admin_role') === 'siswa')
                                    Belum ada jadwal pelajaran untuk kelas Anda
                                @else
                                    Belum ada jadwal pelajaran
                                @endif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>