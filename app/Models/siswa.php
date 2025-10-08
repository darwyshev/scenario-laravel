<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class siswa extends Model
{
use HasFactory;
    protected $table = 'datasiswa';
    protected $primaryKey = 'idsiswa'; 
    protected $fillable = ['id','nama','tb','bb']; 
    public function admin() {
        return $this->belongsTo(Admin::class, 'id'); // step 7
    }
    public function kelas()
{
    return $this->hasOne(Kelas::class, 'idsiswa');
}

}
