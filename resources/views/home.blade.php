<!DOCTYPE html>
<html>
<head>
    <title>Home</title>
</head>
<body>
    <h2>Halo, {{ session('admin_role') }} {{ session('admin_username') }}</h2>
    <a href="{{ route('logout') }}">Logout</a>
    @if(session('admin_role') === 'admin' || session('admin_role') === 'guru' || session('admin_role') === 'siswa')
        | <a href="{{ route('kbm.index') }}">Lihat Jadwal KBM</a>
    @endif
    <br><br>

    {{-- ================= GURU ================= --}}
    @if (session('admin_role') === 'guru' && isset($guru))
        <h3>Data Guru</h3>
        <p><b>Nama :</b> {{ $guru->nama }}</p>
        <p><b>Mapel :</b> {{ $guru->mapel }}</p>

        {{-- Jika guru ini walas --}}
        @if($guru->walas)
            <h4>Wali Kelas: {{ $guru->walas->namakelas }} ({{ $guru->walas->jenjang }})</h4>
            <h5>Daftar Siswa:</h5>
            <table border="1" cellpadding="8">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Tinggi Badan</th>
                        <th>Berat Badan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($guru->walas->kelas as $i => $kelas)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $kelas->siswa->nama }}</td>
                            <td>{{ $kelas->siswa->tb }}</td>
                            <td>{{ $kelas->siswa->bb }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
        <br>
    @endif

    {{-- ================= SISWA ================= --}}
    @if (session('admin_role') === 'siswa' && isset($siswaLogin))
        <h3>Data Siswa</h3>
        <p><b>Nama :</b> {{ $siswaLogin->nama }}</p>
        <p><b>BB :</b> {{ $siswaLogin->bb }}</p>
        <p><b>TB :</b> {{ $siswaLogin->tb }}</p>

        {{-- Jika siswa punya kelas --}}
        @if($siswaLogin->kelas)
            <p><b>Kelas :</b> {{ $siswaLogin->kelas->walas->namakelas }} ({{ $siswaLogin->kelas->walas->jenjang }})</p>
            <p><b>Wali Kelas :</b> {{ $siswaLogin->kelas->walas->guru->nama }}</p>
        @endif
        <br>
    @endif

    {{-- ================= TABEL SISWA (HANYA ADMIN) ================= --}}
    @if (session('admin_role') === 'admin')
        <h2>Daftar Siswa</h2>
        <a href="{{ route('siswa.create') }}">
            <button>+ Tambah Siswa</button>
        </a>
        <br><br>

        <table border="1" cellpadding="8">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Tinggi Badan</th>
                    <th>Berat Badan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($siswa as $i => $s)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $s->nama }}</td>
                        <td>{{ $s->tb }}</td>
                        <td>{{ $s->bb }}</td>
                        <td>
                            <a href="{{ route('siswa.edit', $s->idsiswa) }}">Edit</a> |
                            <a href="{{ route('siswa.delete', $s->idsiswa) }}"
                               onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>
</html>