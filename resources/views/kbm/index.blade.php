<!DOCTYPE html>
<html>

<head>
    <title>Jadwal KBM</title>
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
    <div class="container mt-5">
        @if(session('admin_role') === 'admin')
            <h2 class="mb-4">Jadwal Kegiatan Belajar Mengajar (KBM) - Semua Guru</h2>
        @elseif(session('admin_role') === 'guru' && isset($guru))
            <h2 class="mb-4">Jadwal Mengajar Saya</h2>
            <div class="alert alert-info">
                <strong>Guru:</strong> {{ $guru->nama }} | <strong>Mata Pelajaran:</strong> {{ $guru->mapel }}
            </div>
        @elseif(session('admin_role') === 'siswa' && isset($kelasData))
            <h2 class="mb-4">Jadwal Pelajaran Kelas Saya</h2>
            <div class="alert alert-info">
                <strong>Siswa:</strong> {{ $siswaData->nama }} | 
                <strong>Kelas:</strong> {{ $kelasData->namakelas }} ({{ $kelasData->jenjang }}) | 
                <strong>Wali Kelas:</strong> {{ $kelasData->guru->nama }}
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
        
        <div class="mt-3">
            <a href="{{ route('home') }}" class="btn btn-secondary">Kembali ke Home</a>
        </div>
    </div>

</body>

</html>