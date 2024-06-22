<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    use HasFactory;

    protected $table = 'achievements';
    protected $guarded = ['id'];

    protected $fillable = [
        'kode_prestasi',
        'poin_prestasi',
        'deskripsi',
        'catatan',
        'created_at',
    ];
}
