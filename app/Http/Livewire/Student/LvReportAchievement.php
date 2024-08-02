<?php

namespace App\Http\Livewire\Student;

use App\Helpers\StringHelper;
use App\Models\Transaction\StudentAchievement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Yajra\DataTables\Facades\DataTables;

class LvReportAchievement extends Component
{
    public function render()
    {
        return view('livewire.student.lv-report-achievement')
            ->with(['pageTitle' => "Student Violation's Record"])
            ->layout('layouts.student.lv-main', ['menuName' => 'student_record']);
    }

    public function dtAchievement(Request $request)
    {
        $search = StringHelper::escapeLike($request->input('search.value') ?? '');
        $searchParam = $request->input('search');
        $searchParam['value'] = $search;
        $request->merge(['search' => $searchParam]);

        $model = StudentAchievement::query()
            ->select(
                'student_achievements.*',
                'teachers.nama_guru',
                'students.nama_siswa',
                'achievements.deskripsi',
            )
            ->leftJoin('teachers', 'student_achievements.teacher_nip', 'teachers.nip')
            ->join('students', 'student_achievements.student_nis', 'students.nis')
            ->join('achievements', 'student_achievements.achievement_id', 'achievements.id')
            ->where('students.id', Auth::guard('studentUser')->user()->student_id);

        return DataTables::eloquent($model)
            ->order(function ($query) {
                $query->orderBy('id', 'desc');
            })
            ->addIndexColumn()
            ->editColumn('nama_guru', function ($stdAch) {
                return $stdAch->nama_guru ?? 'Administrator';
            })
            ->editColumn('created_at', function ($stdVio) {
                return $stdVio->created_at->locale('id')->settings(['formatFunction' => 'translatedFormat'])->format("d F Y");
            })
            ->only([
                'id',
                'nama_guru',
                'poin_penambahan',
                'deskripsi',
                'catatan',
                'created_at'
            ])
            ->toJson();
    }
}
