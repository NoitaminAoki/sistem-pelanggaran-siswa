<?php

namespace App\Models\Transaction;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAchievement extends Model
{
    use HasFactory;

    protected $table = 'student_achievements';
    protected $guarded = ['id'];

    protected $fillable = [
        'teacher_nip',
        'student_nis',
        'achievement_id',
        'poin_awal',
        'poin_akhir',
        'poin_penambahan',
        'catatan',
        'created_at',
    ];
}
