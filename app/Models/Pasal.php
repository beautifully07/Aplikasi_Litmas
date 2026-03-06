<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pasal extends Model
{
    use HasFactory;

    protected $fillable = ['nomor_pasal', 'klasifikasi_hukum_id'];

    public function ayats()
    {
        return $this->hasMany(Ayat::class);
    }

    // relasi ke klasifikasi hukum
    public function klasifikasiHukum()
    {
        return $this->belongsTo(KlasifikasiHukum::class);
    }
}
