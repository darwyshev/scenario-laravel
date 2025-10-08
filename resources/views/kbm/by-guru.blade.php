<!DOCTYPE html>
<html>

<head>
    <title>Jadwal KBM - {{ $guru->nama }}</title>
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
                <table class="table table-hover table-custom">
                    <thead>
                        <tr>
                            <th scope="col" width="5%">#</th>
                            <th scope="col" width="25%">Kelas</th>
                            <th scope="col" width="20%">Hari</th>
                            <th scope="col" width="25%">Jam Mulai</th>
                            <th scope="col" width="25%">Jam Selesai</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($guru->kbm as $i => $jadwal)
                            <tr>
                                <th scope="row">{{ $i + 1 }}</th>
                                <td>{{ $jadwal->walas->namakelas }}</td>
                                <td>{{ $jadwal->hari }}</td>
                                <td>{{ $jadwal->mulai }}</td>
                                <td>{{ $jadwal->selesai }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Belum ada jadwal mengajar</td>
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

</body>

</html>