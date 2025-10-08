<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class konten extends Model
{
    use HasFactory;

    protected $table = 'datakonten';

    protected $fillable = [
        'judul',
        'isi',
        'detil',
    ];
}
