<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sanction extends Model
{
    use HasFactory;

    protected $table = 'sanctions';
    protected $guarded = ['id'];

    protected $fillable = [
        'kode_sanksi',
        'poin_minimum',
        'poin_batasan',
        'jenis',
        'deskripsi',
        'catatan',
        'created_at',
    ];
}
