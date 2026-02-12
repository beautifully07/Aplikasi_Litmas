<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Client extends Model
{
    protected $fillable = [
        'user_id',
        'nama',
        'no_register',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'agama',
        'status_perkawinan',
        'suku',
        'kebangsaan',
        'kewarganegaraan',
        'pendidikan',
        'pekerjaan',
        'alamat',
        'ciri_khusus',
        'usia'
    ];

    protected $table = 'clients';

    // 🔗 Relasi ke petugas
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 🔢 Hitung usia otomatis
    public function getUsiaAttribute()
    {
        return $this->tanggal_lahir
            ? Carbon::parse($this->tanggal_lahir)->age
            : null;
    }
}