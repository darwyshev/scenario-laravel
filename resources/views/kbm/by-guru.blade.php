<!DOCTYPE html>
<html>

<head>
    <title>Jadwal KBM - {{ $guru->nama }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h2 class="mb-4">Jadwal Mengajar - {{ $guru->nama }}</h2>
        
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">Informasi Guru</h5>
                <p class="mb-1"><strong>Nama:</strong> {{ $guru->nama }}</p>
                <p class="mb-0"><strong>Mata Pelajaran:</strong> {{ $guru->mapel }}</p>
            </div>
        </div>
        
        <div class="card">
            <div class="card-body">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-primary text-center">
                        <tr>
                            <th>No</th>
                            <th>Kelas</th>
                            <th>Hari</th>
                            <th>Jam Mulai</th>
                            <th>Jam Selesai</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($guru->kbm as $i => $jadwal)
                        <tr>
                            <td class="text-center">{{ $i + 1 }}</td>
                            <td>{{ $jadwal->walas->namakelas }}</td>
                            <td>{{ $jadwal->hari }}</td>
                            <td>{{ $jadwal->mulai }}</td>
                            <td>{{ $jadwal->selesai }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">Belum ada jadwal mengajar</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="mt-3">
            <a href="{{ route('kbm.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
