<?php

namespace App\Models\Transaction;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentViolation extends Model
{
    use HasFactory;

    protected $table = 'student_violations';
    protected $guarded = ['id'];

    protected $fillable = [
        'teacher_nip',
        'student_nis',
        'violation_id',
        'catatan',
        'created_at',
    ];
}
