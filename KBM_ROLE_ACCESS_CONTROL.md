# KBM Role-Based Access Control

## Ringkasan Implementasi

Sistem KBM sekarang memiliki kontrol akses berdasarkan role pengguna (admin dan guru).

## Hak Akses Berdasarkan Role

### 1. **Admin**
**Hak Akses Penuh:**
- ✅ Melihat **semua jadwal KBM** dari semua guru
- ✅ Melihat jadwal berdasarkan guru tertentu (`/kbm/guru/{idguru}`)
- ✅ Melihat jadwal berdasarkan kelas tertentu (`/kbm/kelas/{idwalas}`)
- ✅ Akses tombol "Lihat Kelas" pada setiap jadwal

**Tampilan:**
- Tabel menampilkan kolom: No, Nama Guru, Mata Pelajaran, Kelas, Hari, Jam Mulai, Jam Selesai, Aksi
- Judul: "Jadwal Kegiatan Belajar Mengajar (KBM) - Semua Guru"

### 2. **Guru**
**Hak Akses Terbatas:**
- ✅ Melihat **hanya jadwal mengajar sendiri**
- ✅ Melihat jadwal berdasarkan kelas tertentu (`/kbm/kelas/{idwalas}`)
- ❌ **TIDAK BISA** melihat jadwal guru lain
- ❌ **TIDAK BISA** akses `/kbm/guru/{idguru}` (akan di-redirect)

**Tampilan:**
- Tabel menampilkan kolom: No, Kelas, Hari, Jam Mulai, Jam Selesai (tanpa kolom Nama Guru dan Mapel)
- Judul: "Jadwal Mengajar Saya"
- Info box menampilkan nama guru dan mata pelajaran

### 3. **Siswa**
**Hak Akses Terbatas:**
- ✅ Melihat **hanya jadwal kelas sendiri** berdasarkan `idwalas`
- ✅ Melihat semua guru yang mengajar di kelas mereka
- ❌ **TIDAK BISA** melihat jadwal kelas lain
- ❌ **TIDAK BISA** melihat jadwal guru lain di luar kelas mereka

**Tampilan:**
- Tabel menampilkan kolom: No, Nama Guru, Mata Pelajaran, Hari, Jam Mulai, Jam Selesai
- Judul: "Jadwal Pelajaran Kelas Saya"
- Info box menampilkan nama siswa, kelas, dan wali kelas
- Hanya menampilkan jadwal untuk kelas yang siswa ikuti

## Perubahan File

### 1. Controller: `app/Http/Controllers/kbmController.php`

#### Method `index()`
```php
// Cek login
if (!$role || !$adminId) {
    return redirect()->route('formLogin');
}

// Admin: lihat semua jadwal
if ($role === 'admin') {
    $jadwals = kbm::with(['guru', 'walas'])->get();
}

// Guru: lihat jadwal sendiri saja
elseif ($role === 'guru') {
    $guru = guru::where('id', $adminId)->first();
    $jadwals = kbm::where('idguru', $guru->idguru)->get();
}

// Siswa: lihat jadwal kelas sendiri berdasarkan idwalas
elseif ($role === 'siswa') {
    $siswaData = Siswa::where('id', $adminId)->with(['kelas.walas'])->first();
    if (!$siswaData || !$siswaData->kelas) {
        return redirect()->route('home')->with('error', 'Anda belum terdaftar di kelas manapun.');
    }
    $jadwals = kbm::where('idwalas', $siswaData->kelas->idwalas)->get();
}
```

#### Method `showByGuru($idguru)`
```php
// Hanya admin yang bisa akses
if ($role !== 'admin') {
    return redirect()->route('kbm.index')
        ->with('error', 'Akses ditolak. Hanya admin yang dapat melihat jadwal guru lain.');
}
```

#### Method `showByKelas($idwalas)`
```php
// Admin dan Guru bisa akses
if (!$role) {
    return redirect()->route('formLogin');
}
```

### 2. View: `resources/views/kbm/index.blade.php`

**Conditional Display:**
```blade
@if(session('admin_role') === 'admin')
    {{-- Tampilkan semua kolom: Nama Guru, Mapel, Kelas --}}
@elseif(session('admin_role') === 'guru')
    {{-- Hanya tampilkan: Kelas, Hari, Jam --}}
@elseif(session('admin_role') === 'siswa')
    {{-- Tampilkan: Nama Guru, Mapel, Hari, Jam (tanpa kolom Kelas) --}}
@endif
```

**Info Box untuk Siswa:**
```blade
@elseif(session('admin_role') === 'siswa' && isset($kelasData))
    <h2 class="mb-4">Jadwal Pelajaran Kelas Saya</h2>
    <div class="alert alert-info">
        <strong>Siswa:</strong> {{ $siswaData->nama }} | 
        <strong>Kelas:</strong> {{ $kelasData->namakelas }} ({{ $kelasData->jenjang }}) | 
        <strong>Wali Kelas:</strong> {{ $kelasData->guru->nama }}
    </div>
@endif
```

### 3. View: `resources/views/home.blade.php`

**Link KBM:**
```blade
@if(session('admin_role') === 'admin' || session('admin_role') === 'guru' || session('admin_role') === 'siswa')
    | <a href="{{ route('kbm.index') }}">Lihat Jadwal KBM</a>
@endif
```

## Routes

Semua route tetap sama, kontrol akses dilakukan di controller:

```php
// KBM (Jadwal Pelajaran)
Route::get('/kbm', [kbmController::class, 'index'])->name('kbm.index');
Route::get('/kbm/guru/{idguru}', [kbmController::class, 'showByGuru'])->name('kbm.by-guru');
Route::get('/kbm/kelas/{idwalas}', [kbmController::class, 'showByKelas'])->name('kbm.by-kelas');
```

## Contoh Skenario

### Skenario 1: Admin Login
1. Login sebagai admin
2. Klik "Lihat Jadwal KBM" di home
3. Melihat **semua jadwal** dari semua guru
4. Bisa klik "Lihat Kelas" untuk detail per kelas
5. Bisa akses `/kbm/guru/1` untuk melihat jadwal guru tertentu

### Skenario 2: Guru Login
1. Login sebagai guru (misal: Guru dengan idguru=2)
2. Klik "Lihat Jadwal KBM" di home
3. Melihat **hanya jadwal mengajar sendiri**
4. Jika coba akses `/kbm/guru/1` (guru lain) → **di-redirect** dengan pesan error
5. Bisa akses `/kbm/kelas/1` untuk melihat jadwal kelas tertentu

### Skenario 3: Siswa Login
1. Login sebagai siswa
2. Klik "Lihat Jadwal KBM" di home
3. Melihat **hanya jadwal kelas sendiri** (berdasarkan idwalas)
4. Melihat semua guru yang mengajar di kelas tersebut
5. Jika siswa belum terdaftar di kelas → **di-redirect** dengan pesan "Anda belum terdaftar di kelas manapun"

## Testing

### Test sebagai Admin:
```
1. Login: username=admin, password=admin
2. Akses: http://127.0.0.1:8000/kbm
3. Expected: Melihat semua jadwal dari semua guru
```

### Test sebagai Guru:
```
1. Login: username=guru, password=guru
2. Akses: http://127.0.0.1:8000/kbm
3. Expected: Melihat hanya jadwal mengajar sendiri
4. Akses: http://127.0.0.1:8000/kbm/guru/1
5. Expected: Redirect dengan error "Akses ditolak"
```

### Test sebagai Siswa:
```
1. Login sebagai siswa yang sudah terdaftar di kelas
2. Akses: http://127.0.0.1:8000/kbm
3. Expected: Melihat jadwal semua guru yang mengajar di kelas siswa tersebut
4. Info box menampilkan: Nama Siswa, Kelas, dan Wali Kelas
5. Tabel menampilkan: Nama Guru, Mata Pelajaran, Hari, Jam Mulai, Jam Selesai
```

## Keamanan

✅ **Session-based authentication**: Menggunakan `session('admin_role')` dan `session('admin_id')`
✅ **Controller-level protection**: Validasi di setiap method controller
✅ **View-level conditional**: Tampilan berbeda berdasarkan role
✅ **Redirect dengan pesan**: User mendapat feedback jelas saat akses ditolak

## Pesan Error

- **Belum login**: "Silakan login terlebih dahulu."
- **Guru akses jadwal guru lain**: "Akses ditolak. Hanya admin yang dapat melihat jadwal guru lain."
- **Role tidak valid**: "Akses ditolak."
- **Data guru tidak ditemukan**: "Data guru tidak ditemukan."
- **Siswa belum terdaftar di kelas**: "Anda belum terdaftar di kelas manapun."

## Alur Data Siswa ke KBM

```
Siswa (id) → Kelas (idsiswa, idwalas) → Walas (idwalas) → KBM (idwalas, idguru)
```

**Penjelasan:**
1. Siswa login dengan `admin_id`
2. Cari data siswa berdasarkan `id` di tabel `datasiswa`
3. Ambil `idwalas` dari tabel `datakelas` berdasarkan `idsiswa`
4. Query jadwal KBM berdasarkan `idwalas` tersebut
5. Tampilkan semua guru yang mengajar di kelas tersebut
