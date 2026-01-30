<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'nama',
        'no_register',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'agama',
        'suku',
        'kebangsaan',
        'kewarganegaraan',
        'status_perkawinan',
        'pendidikan',
        'pekerjaan',
        'alamat',
        'ciri_khusus'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
