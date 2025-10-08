<!DOCTYPE html>
<html>

<head>
    <title>Jadwal KBM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-primary text-center">
                        <tr>
                            <th>No</th>
                            @if(session('admin_role') === 'admin')
                                <th>Nama Guru</th>
                                <th>Mata Pelajaran</th>
                                <th>Kelas</th>
                            @elseif(session('admin_role') === 'siswa')
                                <th>Nama Guru</th>
                                <th>Mata Pelajaran</th>
                            @endif
                            @if(session('admin_role') === 'guru')
                                <th>Kelas</th>
                            @endif
                            <th>Hari</th>
                            <th>Jam Mulai</th>
                            <th>Jam Selesai</th>
                            @if(session('admin_role') === 'admin')
                                <th>Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($jadwals as $i => $jadwal)
                        <tr>
                            <td class="text-center">{{ $i + 1 }}</td>
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
                                <td class="text-center">
                                    <a href="{{ route('kbm.by-kelas', $jadwal->idwalas) }}" class="btn btn-sm btn-info">Lihat Kelas</a>
                                </td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ session('admin_role') === 'admin' ? '8' : '5' }}" class="text-center text-muted">
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
