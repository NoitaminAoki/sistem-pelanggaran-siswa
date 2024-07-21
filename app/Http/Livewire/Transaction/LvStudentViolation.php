<?php

namespace App\Http\Livewire\Transaction;

use App\Helpers\StringHelper;
use App\Models\Master\Student;
use App\Models\Master\Teacher;
use App\Models\Master\Violation;
use App\Models\Transaction\StudentViolation;
use Barryvdh\Debugbar\Facades\Debugbar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Yajra\DataTables\Facades\DataTables;

class LvStudentViolation extends Component
{
    public $stdVioId;
    public $stdVioNip;
    public $stdVioNote;

    public $selected = [
        'student' => [
            'nis' => null,
            'fullname' => null,
            'gender' => null,
            'birthPlace' => null,
            'birthDate' => null,
            'address' => null,
            'formattedDate' => null
        ],
        'violation' => [
            'id' => null,
            'code' => null,
            'type' => null,
            'name' => null,
            'point' => null,
            'category' => null,
        ],
    ];

    public $viewPageType = 1;
    public $viewFormType = 1;

    private $validationRules = [
        'stdVioCode' => 'required',
        'stdVioNip' => 'required',
        'stdVioNis' => 'required',
        'stdVioPoint' => 'required|integer',
        'stdVioNote' => 'required',
    ];

    protected $listeners = [
        'setInputStudent' => 'setStudent',
        'setInputViolation' => 'setViolation',
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
            ->leftJoin('teachers', 'student_violations.teacher_nip', 'teachers.nip')
            ->join('students', 'student_violations.student_nis', 'students.nis')
            ->join('violations', 'student_violations.violation_id', 'violations.id');

        return DataTables::eloquent($model)
            ->order(function ($query) {
                $query->orderBy('id', 'desc');
            })
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $actions = [
                    'edit' => ["dataId" => $row->id, 'action' => 'editStudentViolation'],
                    // 'detail' => ["dataId" => $row->id],
                    'delete' => ["dataId" => $row->id, 'action' => 'deleteStudentViolation'],
                ];

                return view('rendering-components.button-datatables', $actions);
                // return view('components.standard-button-datatables', $actions);
            })
            ->editColumn('nama_guru', function ($stdVio) {
                return $stdVio->nama_guru ?? 'Administrator';
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

    public function slcViolation(Request $request)
    {
        $search = StringHelper::escapeLike($request->input('search') ?? '');
        $data = Student::query()
            ->select('nis', 'nama_siswa')
            ->where('nis', 'like', "%{$search}%")
            ->orWhere('nama_siswa', 'LIKE', '%' . $search . '%')
            ->orderBy('nama_siswa', 'asc')
            ->limit(10)
            ->get();

        return $data->toJson();
    }

    public function setStudent($nis)
    {
        $student = Student::where('nis', $nis)->first();

        $formattedDate = $student->tanggal_lahir->format('d-m-Y');
        $this->selected['student'] = [
            'nis' => $student->nis,
            'fullname' => $student->nama_siswa,
            'gender' => $student->jenis_kelamin,
            'birthPlace' => $student->tempat_lahir,
            'birthDate' => $student->tanggal_lahir,
            'address' => $student->alamat,
            'formattedDate' => $formattedDate
        ];
    }

    public function setViolation($id)
    {
        $violation = Violation::find($id);

        $this->selected['violation'] = [
            'id' => $violation->id,
            'code' => $violation->kode_pelanggaran,
            'type' => $violation->jenis,
            'name' => $violation->nama_pelanggaran,
            'point' => $violation->bobot_poin,
            'category' => $violation->kategori,
        ];
    }

    public function sendReportViolation(int $formType)
    {
        if ($formType == 1) {
            $this->addStudentViolation();
        } else {
            $this->updateStudentViolation();
        }
    }


    public function setStudentViolation($id)
    {
        $studentViolation = StudentViolation::find($id);

        if (!$studentViolation) {
            $this->dispatchBrowserEvent('notification:show', [
                'title' => "Maaf, data tidak ditemukan!",
                'icon' => 'error',
            ]);
        }

        $this->setStudent($studentViolation->student_nis);
        $this->setViolation($studentViolation->violation_id);
        $this->stdVioId = $studentViolation->id;
        $this->stdVioNote = $studentViolation->catatan;

        $this->viewPageType = 2;
        $this->viewFormType = 2;

        $this->dispatchBrowserEvent('select2:set-value-server-side', [
            'target' => "#selectStudent",
            'data' => [
                'text' => "{$this->selected['student']['fullname']} ({$studentViolation->student_nis})",
                'id' => $studentViolation->student_nis
            ]
        ]);
        $this->dispatchBrowserEvent('swal-loader:close');
    }

    public function addStudentViolation()
    {
        $nip_teacher = null;
        $user = Auth::user('admin');

        if ($user->is_teacher) {
            $nip_teacher = Teacher::find($user->teacher_id)?->nip;
        }

        $this->stdVioNip = $nip_teacher;

        $this->validate([
            'selected.student.nis' => 'required|integer',
            'selected.violation.id' => 'required|integer',
            'stdVioNip' => 'nullable',
            'stdVioNote' => 'nullable',
        ]);

        DB::beginTransaction();
        try {
            $create_student_vio = StudentViolation::create([
                'teacher_nip' => $this->stdVioNip,
                'student_nis' => $this->selected['student']['nis'],
                'violation_id' => $this->selected['violation']['id'],
                'catatan' => $this->stdVioNote,
            ]);

            DB::commit();
            $this->dispatchBrowserEvent('notification:show', [
                'title' => 'Berhasil menambahkan laporan!',
                'icon' => 'success',
            ]);
            $this->resetInput();
            $this->reloadDataTables();
        } catch (\Exception $ex) {
            DB::rollBack();
            $this->dispatchBrowserEvent('notification:show', [
                'title' => "Gagal menambahkan laporan! (Error: {$ex->getMessage()})",
                'icon' => 'error',
            ]);
        } finally {
            $this->resetInput();
        }
    }

    public function updateStudentViolation()
    {
        $nip_teacher = null;
        $user = Auth::user('admin');

        if ($user->is_teacher) {
            $nip_teacher = Teacher::find($user->teacher_id)?->nip;
        }

        $this->stdVioNip = $nip_teacher;

        $this->validate([
            'selected.student.nis' => 'required|integer',
            'selected.violation.id' => 'required|integer',
            'stdVioNip' => 'nullable',
            'stdVioNote' => 'nullable',
        ]);

        $student_violation = StudentViolation::where('id', $this->stdVioId)->firstOrFail();

        DB::beginTransaction();
        try {
            $create_student_vio = StudentViolation::where('id', $this->stdVioId)
                ->update([
                    'teacher_nip' => $this->stdVioNip,
                    'student_nis' => $this->selected['student']['nis'],
                    'violation_id' => $this->selected['violation']['id'],
                    'catatan' => $this->stdVioNote,
                ]);

            DB::commit();
            $this->dispatchBrowserEvent('notification:show', [
                'title' => 'Berhasil memperbarui laporan!',
                'icon' => 'success',
            ]);
            $this->resetInput();
            $this->reloadDataTables();
        } catch (\Exception $ex) {
            DB::rollBack();
            $this->dispatchBrowserEvent('notification:show', [
                'title' => "Gagal memperbarui laporan! (Error: {$ex->getMessage()})",
                'icon' => 'error',
            ]);
        } finally {
            $this->resetInput();
        }
    }

    public function deleteStudentViolation($id)
    {
        $student_violation = StudentViolation::where('id', $id)->first();

        if (!$student_violation)
            $this->dispatchBrowserEvent('notification:show', [
                'title' => "Gagal menghapus laporan!",
                'icon' => 'error',
            ]);

        $delete = StudentViolation::where('id', $id)->delete();
        $this->dispatchBrowserEvent('notification:show', [
            'title' => 'Berhasil menghapus laporan!',
            'icon' => 'success',
        ]);
        $this->reloadDataTables();
    }

    public function resetInput()
    {
        $this->reset([
            'stdVioId',
            'stdVioNip',
            'stdVioNote',
        ]);
        $this->viewPageType = 1;
        $this->viewFormType = 1;
        $this->selected = [
            'student' => [
                'nis' => null,
                'fullname' => null,
                'gender' => null,
                'birthPlace' => null,
                'birthDate' => null,
                'address' => null,
                'formattedDate' => null
            ],
            'violation' => [
                'id' => null,
                'code' => null,
                'type' => null,
                'name' => null,
                'point' => null,
                'category' => null,
            ],
        ];
    }

    public function reloadDataTables()
    {
        $this->dispatchBrowserEvent('datatables:refresh', ['target' => "tStdVio"]);
    }
}
