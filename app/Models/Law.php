<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Law extends Model
{
    protected $table = 'law';

    protected $fillable = [
        'perkara_id',
        'jenis_peraturan',
        'nomor_peraturan',
        'tahun_peraturan',
        'pasal',
        'ayat'
    ];
}
