<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pasal extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_pasal',
        'klasifikasi_hukum_id'
    ];

    public function ayats()
    {
        return $this->hasMany(Ayat::class, 'pasal_id', 'id');
    }

    public function klasifikasiHukum()
    {
        return $this->belongsTo(KlasifikasiHukum::class, 'klasifikasi_hukum_id');
    }

    // App\Models\Pasal.php

public function pbDewasas()
{
    return $this->belongsToMany(
        \App\Models\PBDewasa::class,
        'p_b_dewasa_pasal',  // tabel pivot — sama dengan di PBDewasa::pasals()
        'pasal_id',          // FK kolom pasal di tabel pivot
        'p_b_dewasa_id'      // FK kolom pb_dewasa di tabel pivot
    );
}
}