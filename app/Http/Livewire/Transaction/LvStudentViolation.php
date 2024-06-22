<?php

namespace App\Http\Livewire\Transaction;

use App\Helpers\StringHelper;
use App\Models\Transaction\StudentViolation;
use Illuminate\Http\Request;
use Livewire\Component;
use Yajra\DataTables\Facades\DataTables;

class LvStudentViolation extends Component
{
    public $stdVioId;
    public $stdVioNip;
    public $stdVioNis;
    public $stdVioIdViolation;
    public $stdVioNote;

    public $viewPageType = 1;
    public $viewFormType = 1;

    private $validationRules = [
        'stdVioCode' => 'required',
        'stdVioNip' => 'required',
        'stdVioNis' => 'required',
        'stdVioPoint' => 'required|integer',
        'stdVioNote' => 'required',
    ];

    public function render()
    {
        return view('livewire.transaction.lv-student-violation')
            ->with(['pageTitle' => "Student Violation's Record"])
            ->layout('layouts.cms.lv-main', ['menuName' => 'student_record']);
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
            ->join('teachers', 'student_violations.teacher_nip', 'teachers.nip')
            ->join('students', 'student_violations.student_nis', 'students.nis')
            ->join('violations', 'student_violations.violation_id', 'violations.id');

        return DataTables::eloquent($model)
            ->order(function ($query) {
                $query->orderBy('id', 'desc');
            })
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $actions = [
                    'edit' => ["dataId" => $row->id, 'action' => 'formType = 2; editAchievement($event);'],
                    // 'detail' => ["dataId" => $row->id],
                    'delete' => ["dataId" => $row->id, 'action' => 'deleteAchievement'],
                ];

                return view('rendering-components.button-datatables', $actions);
                // return view('components.standard-button-datatables', $actions);
            })
            ->rawColumns(['action'])
            ->only([
                'id',
                'nama_guru',
                'nama_siswa',
                'jenis_pelanggaran',
                'nama_pelanggaran',
                'catatan',
                'action'
            ])
            ->toJson();
    }
}
