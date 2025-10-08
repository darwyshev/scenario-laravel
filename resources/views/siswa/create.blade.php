<!DOCTYPE html>
<html>

<head>
    <title>Tambah Siswa</title>
</head>

<body>
    <h2>Tambah Siswa</h2>
    
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
    
    <form method="POST" action="{{ route('siswa.store') }}">
        @csrf
        <h3>Data Login</h3>
        <input type="text" name="username" placeholder="Username" required><br><br>
        <input type="password" name="password" placeholder="Password" required><br><br>
        
        <h3>Data Siswa</h3>
        <input type="text" name="nama" placeholder="Nama Lengkap" required><br><br>
        <input type="number" name="tb" placeholder="Tinggi Badan (cm)" required><br><br>
        <input type="number" name="bb" placeholder="Berat Badan (kg)" required><br><br>
        
        <button type="submit">Simpan</button>
        <a href="{{ route('home') }}">Kembali</a>
    </form>
</body>

</html>