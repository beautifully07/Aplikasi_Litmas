<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ayat extends Model
{
    use HasFactory;

    protected $fillable = ['pasal_id', 'nomor_ayat', 'isi'];

    public function pasal()
    {
        return $this->belongsTo(Pasal::class);
    }
}
