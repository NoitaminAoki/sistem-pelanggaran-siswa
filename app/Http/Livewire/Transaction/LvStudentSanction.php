<?php

namespace App\Http\Livewire\Transaction;

use App\Helpers\StringHelper;
use App\Models\Master\Student;
use App\Models\Master\Teacher;
use App\Models\Master\Sanction;
use App\Models\Transaction\StudentSanction;
use App\Models\Transaction\StudentViolation;
use Barryvdh\Debugbar\Facades\Debugbar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Yajra\DataTables\Facades\DataTables;

class LvStudentSanction extends Component
{
    public $stdSancId;
    public $stdSancNip;
    public $stdSancNote;
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
        'sanction' => [
            'id' => null,
            'code' => null,
            'type' => null,
            'description' => null,
            'note' => null,
        ],
    ];

    public $viewPageType = 1;

    public $viewFormType = 1;

    private $validationRules = [
        'stdSancCode' => 'required',
        'stdSancNip' => 'required',
        'stdSancNis' => 'required',
        'stdSancNote' => 'required',
    ];

    protected $listeners = [
        'setInputStudent' => 'setStudent',
        'setInputSanction' => 'setSanction',
    ];
    public function render()
    {
        return view('livewire.transaction.lv-student-sanction')
            ->with(['pageTitle' => "Student Sanction's Record"])
            ->layout('layouts.cms.lv-main', ['menuName' => 'student_record']);
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
            ->join('sanctions', 'student_sanctions.sanction_id', 'sanctions.id');

        return DataTables::eloquent($model)
            ->order(function ($query) {
                $query->orderBy('id', 'desc');
            })
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $actions = [
                    'edit' => ["dataId" => $row->id, 'action' => 'editStudentSanction'],
                    // 'detail' => ["dataId" => $row->id],
                    'delete' => ["dataId" => $row->id, 'action' => 'deleteStudentSanction'],
                ];

                return view('rendering-components.button-datatables', $actions);
                // return view('components.standard-button-datatables', $actions);
            })
            ->editColumn('nama_guru', function ($stdSanc) {
                return $stdSanc->nama_guru ?? 'Administrator';
            })
            ->rawColumns(['action'])
            ->only([
                'id',
                'nama_guru',
                'nama_siswa',
                'jenis_sanksi',
                'deskripsi',
                'catatan',
                'action'
            ])
            ->toJson();
    }

    public function slcSanction(Request $request)
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

    public function setSanction($id)
    {
        $sanction = Sanction::find($id);

        $this->selected['sanction'] = [
            'id' => $sanction->id,
            'code' => $sanction->kode_sanksi,
            'type' => $sanction->jenis,
            'description' => $sanction->deskripsi,
            'note' => $sanction->catatan,
        ];
    }

    public function sendReportSanction(int $formType)
    {
        if ($formType == 1) {
            $this->addStudentSanction();
        } else {
            $this->updateStudentSanction();
        }
    }

    public function setStudentSanction($id)
    {
        $studentSanction = StudentSanction::find($id);

        if (!$studentSanction) {
            $this->dispatchBrowserEvent('notification:show', [
                'title' => "Maaf, data tidak ditemukan!",
                'icon' => 'error',
            ]);
        }

        $this->setStudent($studentSanction->student_nis);
        $this->setSanction($studentSanction->sanction_id);
        $this->stdSancId = $studentSanction->id;
        $this->stdSancNote = $studentSanction->catatan;

        $this->viewPageType = 2;
        $this->viewFormType = 2;

        $this->dispatchBrowserEvent('select2:set-value-server-side', [
            'target' => "#selectStudent",
            'data' => [
                'text' => "{$this->selected['student']['fullname']} ({$studentSanction->student_nis})",
                'id' => $studentSanction->student_nis
            ]
        ]);
        $this->dispatchBrowserEvent('swal-loader:close');
    }

    public function addStudentSanction()
    {
        $nip_teacher = null;
        $user = Auth::user('admin');

        if ($user->is_teacher) {
            $nip_teacher = Teacher::find($user->teacher_id)?->nip;
        }

        $this->stdSancNip = $nip_teacher;

        $this->validate([
            'selected.student.nis' => 'required|integer',
            'selected.sanction.id' => 'required|integer',
            'stdSancNip' => 'nullable',
            'stdSancNote' => 'nullable',
        ]);

        DB::beginTransaction();
        try {
            $total_poin = StudentViolation::select(DB::raw('coalesce(sum(v.bobot_poin), 0) as bobot_poin'))
                ->leftJoin('violations as v', 'v.id', '=', 'student_violations.violation_id')
                ->where('student_violations.student_nis', $this->selected['student']['nis'])
                ->groupBy('student_violations.student_nis')
                ->first();

            $total_poin = $total_poin ? $total_poin->bobot_poin : 0;

            $create_student_sanc = StudentSanction::create([
                'teacher_nip' => $this->stdSancNip,
                'student_nis' => $this->selected['student']['nis'],
                'sanction_id' => $this->selected['sanction']['id'],
                'poin_awal' => $total_poin,
                'poin_akhir' => $total_poin,
                'catatan' => $this->stdSancNote,
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

    public function updateStudentSanction()
    {
        $nip_teacher = null;
        $user = Auth::user('admin');

        if ($user->is_teacher) {
            $nip_teacher = Teacher::find($user->teacher_id)?->nip;
        }

        $this->stdSancNip = $nip_teacher;

        $this->validate([
            'selected.student.nis' => 'required|integer',
            'selected.sanction.id' => 'required|integer',
            'stdSancNip' => 'nullable',
            'stdSancNote' => 'nullable',
        ]);

        $student_sanction = StudentSanction::where('id', $this->stdSancId)->firstOrFail();

        DB::beginTransaction();
        try {
            $create_student_sanc = StudentSanction::where('id', $this->stdSancId)
                ->update([
                    'teacher_nip' => $this->stdSancNip,
                    'student_nis' => $this->selected['student']['nis'],
                    'sanction_id' => $this->selected['sanction']['id'],
                    'catatan' => $this->stdSancNote,
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

    public function deleteStudentSanction($id)
    {
        $student_sanction = StudentSanction::where('id', $id)->first();

        if (!$student_sanction)
            $this->dispatchBrowserEvent('notification:show', [
                'title' => "Gagal menghapus laporan!",
                'icon' => 'error',
            ]);

        $delete = StudentSanction::where('id', $id)->delete();
        $this->dispatchBrowserEvent('notification:show', [
            'title' => 'Berhasil menghapus laporan!',
            'icon' => 'success',
        ]);
        $this->reloadDataTables();
    }

    public function resetInput()
    {
        $this->reset([
            'stdSancId',
            'stdSancNip',
            'stdSancNote',
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
            'sanction' => [
                'id' => null,
                'code' => null,
                'type' => null,
                'description' => null,
                'note' => null,
            ],
        ];
    }

    public function reloadDataTables()
    {
        $this->dispatchBrowserEvent('datatables:refresh', ['target' => "tStdSanc"]);
    }

}
