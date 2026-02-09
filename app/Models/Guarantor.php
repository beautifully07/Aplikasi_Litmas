<?php

namespace App\Models;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guarantor extends Model
{
    use HasFactory;

    protected $table = 'penjamins';

    protected $fillable = [
        'client_id',
        'no_kk',
        'nama',
        'tempat_lahir',
        'tanggal_lahir',
        'agama',
        'suku',
        'kewarganegaraan',
        'pendidikan_terakhir',
        'pekerjaan',
        'alamat',
        'usia',
        'hubungan_keluarga',
    ];

    protected $appends = ['usia'];

    // Relasi ke Client
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
    public function getUsiaAttribute()
    {
        return Carbon::parse($this->tanggal_lahir)->age;
    }
}
