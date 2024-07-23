<?php

namespace App\Http\Livewire\Transaction;

use App\Helpers\StringHelper;
use App\Models\Master\Student;
use App\Models\Master\Teacher;
use App\Models\Master\Achievement;
use App\Models\Transaction\StudentAchievement;
use Barryvdh\Debugbar\Facades\Debugbar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Yajra\DataTables\Facades\DataTables;

class LvStudentAchievement extends Component
{

    public $stdAchId;
    public $stdAchNip;
    public $stdAchNote;

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
        'achievement' => [
            'id' => null,
            'code' => null,
            'description' => null,
            'point' => null,
            'note' => null,
        ],
    ];

    public $viewPageType = 1;

    public $viewFormType = 1;

    private $validationRules = [
        'stdAchCode' => 'required',
        'stdAchNip' => 'required',
        'stdAchNis' => 'required',
        'stdAchPoint' => 'required|integer',
        'stdAchNote' => 'required',
    ];

    protected $listeners = [
        'setInputStudent' => 'setStudent',
        'setInputAchievement' => 'setAchievement',
    ];

    public function render()
    {
        return view('livewire.transaction.lv-student-achievement')
            ->with(['pageTitle' => "Student Achievement's Record"])
            ->layout('layouts.cms.lv-main', ['menuName' => 'student_record']);
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
            ->join('achievements', 'student_achievements.achievement_id', 'achievements.id');

        return DataTables::eloquent($model)
            ->order(function ($query) {
                $query->orderBy('id', 'desc');
            })
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $actions = [
                    'edit' => ["dataId" => $row->id, 'action' => 'editStudentAchievement'],
                    // 'detail' => ["dataId" => $row->id],
                    'delete' => ["dataId" => $row->id, 'action' => 'deleteStudentAchievement'],
                ];

                return view('rendering-components.button-datatables', $actions);
                // return view('components.standard-button-datatables', $actions);
            })
            ->editColumn('nama_guru', function ($stdAch) {
                return $stdAch->nama_guru ?? 'Administrator';
            })
            ->rawColumns(['action'])
            ->only([
                'id',
                'nama_guru',
                'nama_siswa',
                'deskripsi',
                'catatan',
                'action'
            ])
            ->toJson();
    }

    public function slcAchievement(Request $request)
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

    public function setAchievement($id)
    {
        $achievement = Achievement::find($id);

        $this->selected['achievement'] = [
            'id' => $achievement->id,
            'code' => $achievement->kode_prestasi,
            'description' => $achievement->deskripsi,
            'point' => $achievement->poin_prestasi,
            'note' => $achievement->catatan,
        ];
    }

    public function sendReportAchievement(int $formType)
    {
        if ($formType == 1) {
            $this->addStudentAchievement();
        } else {
            $this->updateStudentAchievement();
        }
    }


    public function setStudentAchievement($id)
    {
        $studentAchievement = StudentAchievement::find($id);

        if (!$studentAchievement) {
            $this->dispatchBrowserEvent('notification:show', [
                'title' => "Maaf, data tidak ditemukan!",
                'icon' => 'error',
            ]);
        }

        $this->setStudent($studentAchievement->student_nis);
        $this->setAchievement($studentAchievement->achievement_id);
        $this->stdAchId = $studentAchievement->id;
        $this->stdAchNote = $studentAchievement->catatan;

        $this->viewPageType = 2;
        $this->viewFormType = 2;

        $this->dispatchBrowserEvent('select2:set-value-server-side', [
            'target' => "#selectStudent",
            'data' => [
                'text' => "{$this->selected['student']['fullname']} ({$studentAchievement->student_nis})",
                'id' => $studentAchievement->student_nis
            ]
        ]);
        $this->dispatchBrowserEvent('swal-loader:close');
    }

    public function addStudentAchievement()
    {
        $nip_teacher = null;
        $user = Auth::user('admin');

        if ($user->is_teacher) {
            $nip_teacher = Teacher::find($user->teacher_id)?->nip;
        }

        $this->stdAchNip = $nip_teacher;

        $this->validate([
            'selected.student.nis' => 'required|integer',
            'selected.achievement.id' => 'required|integer',
            'stdAchNip' => 'nullable',
            'stdAchNote' => 'nullable',
        ]);

        DB::beginTransaction();
        try {

            // Calculate the initial points
            $poin_awal_record = StudentAchievement::select(DB::raw('coalesce(sum(a.poin_prestasi), 0) as poin_prestasi'))
                ->leftJoin('achievements as a', 'a.id', '=', 'student_achievements.achievement_id')
                ->where('student_achievements.student_nis', $this->selected['student']['nis'])
                ->groupBy('student_achievements.student_nis')
                ->first();

            $poin_awal = $poin_awal_record ? $poin_awal_record->poin_prestasi : 0;

            // Get the points for the specific achievement
            $poin_prestasi = Achievement::where('kode_prestasi', $this->selected['achievement']['code'])->value('poin_prestasi');

            // Calculate the final points
            $poin_akhir = $poin_awal + $poin_prestasi;

            $create_student_ach = StudentAchievement::create([
                'teacher_nip' => $this->stdAchNip,
                'student_nis' => $this->selected['student']['nis'],
                'achievement_id' => $this->selected['achievement']['id'],
                'poin_awal' => $poin_awal,
                'poin_akhir' => $poin_akhir,
                'poin_penambahan' => $poin_prestasi,
                'catatan' => $this->stdAchNote,
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

    public function updateStudentAchievement()
    {
        $nip_teacher = null;
        $user = Auth::user('admin');

        if ($user->is_teacher) {
            $nip_teacher = Teacher::find($user->teacher_id)?->nip;
        }

        $this->stdAchNip = $nip_teacher;

        $this->validate([
            'selected.student.nis' => 'required|integer',
            'selected.achievement.id' => 'required|integer',
            'stdAchNip' => 'nullable',
            'stdAchNote' => 'nullable',
        ]);

        $student_achievement = StudentAchievement::where('id', $this->stdAchId)->firstOrFail();

        DB::beginTransaction();
        try {

            $current_record_id = $this->stdAchId;

            // Calculate the initial points
            $poin_awal_record = StudentAchievement::select(DB::raw('coalesce(sum(a.poin_prestasi), 0) as poin_prestasi'))
                ->leftJoin('achievements as a', 'a.id', '=', 'student_achievements.achievement_id')
                ->where('student_achievements.student_nis', $this->selected['student']['nis'])
                ->where('student_achievements.id','!=', $current_record_id)
                ->groupBy('student_achievements.student_nis')
                ->first();

            $poin_awal = $poin_awal_record ? $poin_awal_record->poin_prestasi : 0;

            // Get the points for the specific achievement
            $poin_prestasi = Achievement::where('kode_prestasi', $this->selected['achievement']['code'])->value('poin_prestasi');

            // Calculate the final points
            $poin_akhir = $poin_awal + $poin_prestasi;

            $create_student_ach = StudentAchievement::where('id', $current_record_id)
                ->update([
                    'teacher_nip' => $this->stdAchNip,
                    'student_nis' => $this->selected['student']['nis'],
                    'achievement_id' => $this->selected['achievement']['id'],
                    'poin_awal' => $poin_awal,
                    'poin_akhir' => $poin_akhir,
                    'poin_penambahan' => $poin_prestasi,
                    'catatan' => $this->stdAchNote,
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

    public function deleteStudentAchievement($id)
    {
        $student_achievement = StudentAchievement::where('id', $id)->first();

        if (!$student_achievement)
            $this->dispatchBrowserEvent('notification:show', [
                'title' => "Gagal menghapus laporan!",
                'icon' => 'error',
            ]);

        $delete = StudentAchievement::where('id', $id)->delete();
        $this->dispatchBrowserEvent('notification:show', [
            'title' => 'Berhasil menghapus laporan!',
            'icon' => 'success',
        ]);
        $this->reloadDataTables();
    }

    public function resetInput()
    {
        $this->reset([
            'stdAchId',
            'stdAchNip',
            'stdAchNote',
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
            'achievement' => [
                'id' => null,
                'code' => null,
                'description' => null,
                'point' => null,
                'note' => null,
            ],
        ];
    }

    public function reloadDataTables()
    {
        $this->dispatchBrowserEvent('datatables:refresh', ['target' => "tStdAch"]);
    }
}
