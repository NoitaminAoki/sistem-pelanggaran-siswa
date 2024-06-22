<?php

namespace App\Models\Transaction;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentSanction extends Model
{
    use HasFactory;

    protected $table = 'student_sanctions';
    protected $guarded = ['id'];

    protected $fillable = [
        'teacher_nip',
        'student_nis',
        'sanction_id',
        'poin_awal',
        'poin_akhir',
        'catatan',
        'created_at',
    ];
}
