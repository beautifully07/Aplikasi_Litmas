<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pasal extends Model
{
    use HasFactory;

    protected $fillable = ['nomor_pasal', 'judul'];

    public function ayats()
    {
        return $this->hasMany(Ayat::class);
    }
}
