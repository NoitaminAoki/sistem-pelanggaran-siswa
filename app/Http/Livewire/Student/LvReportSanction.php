<?php

namespace App\Http\Livewire\Student;

use App\Helpers\StringHelper;
use App\Models\Transaction\StudentSanction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Yajra\DataTables\Facades\DataTables;

class LvReportSanction extends Component
{
    public function render()
    {
        return view('livewire.student.lv-report-sanction')
            ->with(['pageTitle' => "Student Violation's Record"])
            ->layout('layouts.student.lv-main', ['menuName' => 'student_record']);
    }

    public function dtSanction(Request $request)
    {
        $search = StringHelper::escapeLike($request->input('search.value') ?? '');
        $searchParam = $request->input('search');
        $searchParam['value'] = $search;
        $request->merge(['search' => $searchParam]);

        $model = StudentSanction::query()
            ->select(
                'student_sanctions.*',
                'teachers.nama_guru',
                'students.nama_siswa',
                'sanctions.jenis as jenis_sanksi',
                'sanctions.deskripsi',
            )
            ->leftJoin('teachers', 'student_sanctions.teacher_nip', 'teachers.nip')
            ->join('students', 'student_sanctions.student_nis', 'students.nis')
            ->join('sanctions', 'student_sanctions.sanction_id', 'sanctions.id')
            ->where('students.id', Auth::guard('studentUser')->user()->student_id);

        return DataTables::eloquent($model)
            ->order(function ($query) {
                $query->orderBy('id', 'desc');
            })
            ->addIndexColumn()
            ->editColumn('nama_guru', function ($stdSanc) {
                return $stdSanc->nama_guru ?? 'Administrator';
            })
            ->editColumn('created_at', function ($stdVio) {
                return $stdVio->created_at->locale('id')->settings(['formatFunction' => 'translatedFormat'])->format("d F Y");
            })
            ->only([
                'id',
                'nama_guru',
                'jenis_sanksi',
                'deskripsi',
                'catatan',
                'created_at'
            ])
            ->toJson();
    }
}
