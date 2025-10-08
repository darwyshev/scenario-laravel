<!DOCTYPE html>
<html>

<head>
    <title>Jadwal KBM - {{ $kelas->namakelas }}</title>
</head>

<body>
    <div class="container mt-5">
        <h2 class="mb-4">Jadwal Pelajaran - {{ $kelas->namakelas }}</h2>

        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">Informasi Kelas</h5>
                <p class="mb-1"><strong>Nama Kelas:</strong> {{ $kelas->namakelas }}</p>
                <p class="mb-1"><strong>Jenjang:</strong> {{ $kelas->jenjang }}</p>
                <p class="mb-0"><strong>Tahun Ajaran:</strong> {{ $kelas->tahunajaran }}</p>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th scope="col" width="5%">#</th>
                            <th scope="col" width="20%">Nama Guru</th>
                            <th scope="col" width="20%">Mata Pelajaran</th>
                            <th scope="col" width="15%">Hari</th>
                            <th scope="col" width="20%">Jam Mulai</th>
                            <th scope="col" width="20%">Jam Selesai</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($kelas->kbm as $i => $jadwal)
                            <tr>
                                <th scope="row">{{ $i + 1 }}</th>
                                <td>{{ $jadwal->guru->nama }}</td>
                                <td>{{ $jadwal->guru->mapel }}</td>
                                <td>{{ $jadwal->hari }}</td>
                                <td>{{ $jadwal->mulai }}</td>
                                <td>{{ $jadwal->selesai }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Belum ada jadwal pelajaran</td>
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