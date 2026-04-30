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

    public function pbDewasa()
{
    return $this->belongsToMany(
        PBDewasa::class,
        'pb_dewasa_klasifikasi_hukum',
        'klasifikasi_hukum_id',
        'pb_dewasa_id'
    );
}

}
