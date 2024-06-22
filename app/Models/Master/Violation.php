<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Violation extends Model
{
    use HasFactory;

    protected $table = 'violations';
    protected $guarded = ['id'];

    protected $fillable = [
        'kode_pelanggaran',
        'jenis',
        'nama_pelanggaran',
        'bobot_poin',
        'kategori',
        'created_at',
    ];
}
