<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\siswa;
use App\Models\Guru;
class admin extends Model
{
    use HasFactory;
    protected $table = 'dataadmin';
    protected $primaryKey = 'id'; // Specify the primary key if it's not 'id'
    protected $fillable = ['username', 'password', 'role']; // Added role to fillable attributes

    public function siswa() {
        return $this->hasOne(siswa::class, 'id');
    }
        public function guru() {
        return $this->hasOne(Guru::class, 'id'); 
    }
}
