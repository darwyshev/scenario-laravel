<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class guru extends Model
{
        use HasFactory; 
    protected $table = 'dataguru'; 
    protected $primaryKey = 'idguru'; 
    protected $fillable = ['id','nama','mapel']; 
    public function admin() {
        return $this->belongsTo(Admin::class, 'id'); // step 14
    }
    public function walas()
{
    return $this->hasOne(Walas::class, 'idguru');
}

    public function kbm()
    {
        return $this->hasMany(kbm::class, 'idguru', 'idguru');
    }

}
