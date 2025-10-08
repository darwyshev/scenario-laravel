<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;

    protected $table = 'datakelas';
    protected $primaryKey = 'idkelas';

    protected $fillable = [
        'idwalas',
        'idsiswa',
    ];

    public function walas()
{
return $this->belongsTo(walas::class, 'idwalas');
}
public function siswa()
{
return $this->belongsTo(siswa::class, 'idsiswa');
}
}

