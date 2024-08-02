<?php

namespace App\Http\Livewire\Student;

use App\Helpers\StringHelper;
use App\Models\Transaction\StudentViolation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Yajra\DataTables\Facades\DataTables;

class LvReportViolation extends Component
{
    public function render()
    {
        return view('livewire.student.lv-report-violation')
            ->with(['pageTitle' => "Student Violation's Record"])
            ->layout('layouts.student.lv-main', ['menuName' => 'student_record']);
    }

    public function dtViolation(Request $request)
    {
        $search = StringHelper::escapeLike($request->input('search.value') ?? '');
        $searchParam = $request->input('search');
        $searchParam['value'] = $search;
        $request->merge(['search' => $searchParam]);

        $model = StudentViolation::query()
            ->select(
                'student_violations.*',
                'teachers.nama_guru',
                'students.nama_siswa',
                'violations.jenis as jenis_pelanggaran',
                'violations.nama_pelanggaran',
            )
            ->leftJoin('teachers', 'student_violations.teacher_nip', 'teachers.nip')
            ->join('students', 'student_violations.student_nis', 'students.nis')
            ->join('violations', 'student_violations.violation_id', 'violations.id')
            ->where('students.id', Auth::guard('studentUser')->user()->student_id);

        return DataTables::eloquent($model)
            ->order(function ($query) {
                $query->orderBy('id', 'desc');
            })
            ->addIndexColumn()
            ->editColumn('nama_guru', function ($stdVio) {
                return $stdVio->nama_guru ?? 'Administrator';
            })
            ->editColumn('created_at', function ($stdVio) {
                return $stdVio->created_at->locale('id')->settings(['formatFunction' => 'translatedFormat'])->format("d F Y");
            })
            ->only([
                'id',
                'nama_guru',
                'nama_siswa',
                'jenis_pelanggaran',
                'nama_pelanggaran',
                'catatan',
                'created_at'
            ])
            ->toJson();
    }
}
