<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
</head>
<body>
    <h2>Register</h2>

    @if(session('error'))
        <p style="color:red">{{ session('error') }}</p>
    @endif
    @if(session('success'))
        <p style="color:green">{{ session('success') }}</p>
    @endif

    <form method="POST" action="{{ route('prosesRegister') }}">
        @csrf
        <input type="text" name="username" placeholder="Username" required><br>
        <input type="password" name="password" placeholder="Password" required><br>

        <label>
            <input type="radio" name="role" value="admin" onclick="showChoice(this.value)" required> Admin
        </label>
        <label>
            <input type="radio" name="role" value="guru" onclick="showChoice(this.value)"> Guru
        </label>
        <label>
            <input type="radio" name="role" value="siswa" onclick="showChoice(this.value)"> Siswa
        </label>

        <div id="extra"></div>

        <button type="submit">Register</button>
    </form>

    <script>
        function showChoice(role) {
            let extra = document.getElementById('extra');
            extra.innerHTML = '';

            if (role === 'guru') {
                extra.innerHTML = `
                    <input type="text" name="nama" placeholder="Nama Guru" required><br>
                    <input type="text" name="mapel" placeholder="Mata Pelajaran" required><br>
                `;
            } else if (role === 'siswa') {
                extra.innerHTML = `
                    <input type="text" name="nama" placeholder="Nama Siswa" required><br>
                    <input type="number" name="tb" placeholder="Tinggi Badan (cm)" required><br>
                    <input type="number" name="bb" placeholder="Berat Badan (kg)" required><br>
                `;
            }
        }
    </script>
</body>
</html>
