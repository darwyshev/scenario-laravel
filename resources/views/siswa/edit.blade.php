<!DOCTYPE html>
<html>

<head>
    <title>Edit Siswa</title>
</head>

<body>
    <h2>Edit Siswa</h2>
    
    @if(session('error'))
        <div style="color: red;">{{ session('error') }}</div>
    @endif
    
    @if($errors->any())
        <div style="color: red;">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif
    
    <form method="POST" action="{{ route('siswa.update', $siswa->idsiswa) }}">
        @csrf
        <input type="text" name="nama" value="{{ $siswa->nama }}" placeholder="Nama Lengkap" required><br><br>
        <input type="number" name="tb" value="{{ $siswa->tb }}" placeholder="Tinggi Badan (cm)" required><br><br>
        <input type="number" name="bb" value="{{ $siswa->bb }}" placeholder="Berat Badan (kg)" required><br><br>
        <button type="submit">Update</button>
        <a href="{{ route('home') }}">Kembali</a>
    </form>
</body>

</html>