<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class family extends Model
{
    use HasFactory;

    protected $table = 'families';

    protected $fillable = [
        'client_id',
        'p_b_dewasa_id',
        'nama',
        'tempat_lahir',
        'tanggal_lahir',
        'agama',
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

     public function pbdewasa()
    {
        return $this->belongsTo(PBDewasa::class, 'p_b_dewasa_id');
    }
}

//ujicobaPR