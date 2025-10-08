<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class kbm extends Model
{
    use HasFactory;

    protected $table = 'datakbm';
    protected $primaryKey = 'idkbm';

    protected $fillable = [
        'idguru',
        'idwalas',
        'hari',
        'mulai',
        'selesai',
    ];

    public function guru()
    {
        return $this->belongsTo(guru::class, 'idguru', 'idguru');
    }

    public function walas()
    {
        return $this->belongsTo(Walas::class, 'idwalas', 'idwalas');
    }
}
