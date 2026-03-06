<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KlasifikasiHukum extends Model
{

    protected $table = 'klasifikasi_hukums';

    protected $fillable = [
        'nama_klasifikasi',
        'deskripsi'
    ];

    public function pasals()
    {
        return $this->hasMany(Pasal::class);
    }

}
