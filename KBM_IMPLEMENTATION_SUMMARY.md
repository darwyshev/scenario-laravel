# KBM (Kegiatan Belajar Mengajar) Implementation Summary

## Files Created/Modified

### 1. Model
- **File**: `app/Models/kbm.php`
- **Configuration**:
  - Table: `datakbm`
  - Primary Key: `idkbm`
  - Fillable: `idguru`, `idwalas`, `hari`, `mulai`, `selesai`
  - Relationships:
    - `belongsTo(guru::class)` - Many KBM to One Guru
    - `belongsTo(Walas::class)` - Many KBM to One Walas

### 2. Migration
- **File**: `database/migrations/2025_10_07_013817_create_datakbm_table.php`
- **Schema** (Many-to-Many Relationship):
  ```php
  $table->id('idkbm');
  $table->unsignedBigInteger('idguru');  // No unique constraint for many-to-many
  $table->foreign('idguru')->references('idguru')->on('dataguru')->onDelete('cascade');
  $table->unsignedBigInteger('idwalas');  // No unique constraint for many-to-many
  $table->foreign('idwalas')->references('idwalas')->on('datawalas')->onDelete('cascade');
  $table->string('hari');
  $table->string('mulai');
  $table->string('selesai');
  $table->timestamps();
  ```
- **Note**: No unique constraints on `idguru` and `idwalas` to allow:
  - One guru can teach multiple classes (many KBM records with same idguru)
  - One class can be taught by multiple teachers (many KBM records with same idwalas)

### 3. Controller
- **File**: `app/Http/Controllers/kbmController.php`
- **Methods**:
  - `index()` - Display all KBM schedules
  - `showByGuru($idguru)` - Display KBM schedules by specific teacher
  - `showByKelas($idwalas)` - Display KBM schedules by specific class

### 4. Factory
- **File**: `database/factories/kbmFactory.php`
- **Configuration**: Generates random KBM data with:
  - Random guru from existing teachers
  - Random walas from existing classes
  - Random days (Senin-Jumat)
  - Random time slots

### 5. Views
Created 3 Blade templates with Bootstrap styling:
- `resources/views/kbm/index.blade.php` - All schedules
- `resources/views/kbm/by-guru.blade.php` - Schedules by teacher
- `resources/views/kbm/by-kelas.blade.php` - Schedules by class

### 6. Routes
- **File**: `routes/web.php`
- **Added Routes**:
  - `GET /kbm` → `kbm.index`
  - `GET /kbm/guru/{idguru}` → `kbm.by-guru`
  - `GET /kbm/kelas/{idwalas}` → `kbm.by-kelas`

### 7. Model Relationships Updated
- **guru.php**: Added `hasMany(kbm::class)` relationship
- **walas.php**: Added `hasMany(kbm::class)` relationship

### 8. Database Seeder
- **File**: `database/seeders/DatabaseSeeder.php`
- **Added**: `kbm::factory(5)->create();` to generate 5 sample KBM records

## Commands to Run

### Step 1: Run Migration
```bash
php artisan migrate
```

### Step 2: Seed Database (Optional - if you want sample data)
```bash
php artisan db:seed
```

Or if you want to refresh everything:
```bash
php artisan migrate:fresh --seed
```

## Usage Examples

### View All Schedules
Navigate to: `http://localhost/kelas/public/kbm`

### View Schedule by Teacher
Navigate to: `http://localhost/kelas/public/kbm/guru/{idguru}`
Example: `http://localhost/kelas/public/kbm/guru/1`

### View Schedule by Class
Navigate to: `http://localhost/kelas/public/kbm/kelas/{idwalas}`
Example: `http://localhost/kelas/public/kbm/kelas/1`

## Database Structure

### Table: datakbm
| Column | Type | Description |
|--------|------|-------------|
| idkbm | BIGINT (PK) | Primary key, auto-increment |
| idguru | BIGINT (FK) | Foreign key to dataguru.idguru |
| idwalas | BIGINT (FK) | Foreign key to datawalas.idwalas |
| hari | VARCHAR | Day of the week |
| mulai | VARCHAR | Start time |
| selesai | VARCHAR | End time |
| created_at | TIMESTAMP | Creation timestamp |
| updated_at | TIMESTAMP | Update timestamp |

## Relationships Diagram
```
dataadmin (id)
    ↓
dataguru (idguru, id) ←──┐
    ↓                     │
    └──→ datakbm (idkbm, idguru, idwalas) ←──┐
              ↑                                │
              │                                │
datawalas (idwalas, idguru) ──────────────────┘
    ↓
datakelas (idkelas, idwalas, idsiswa)
```

## Many-to-Many Relationship
- **One Guru → Many KBM**: A teacher can teach multiple classes
- **One Walas → Many KBM**: A class can be taught by multiple teachers
- **Example**: 
  - Guru A teaches Math in Class 1A, 1B, and 1C (3 KBM records)
  - Class 1A is taught by Guru A (Math), Guru B (English), Guru C (Science) (3 KBM records)

## Notes
- Each KBM record represents one teaching schedule (one guru teaching one class at specific time)
- No unique constraints on idguru and idwalas allows many-to-many relationship
- One guru can have multiple KBM schedules (hasMany)
- One walas (class) can have multiple KBM schedules (hasMany)
- Bootstrap 5.3.0 is used for styling the views
