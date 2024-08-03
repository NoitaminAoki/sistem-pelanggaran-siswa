<?php

namespace App\Http\Livewire\Report;

use App\Exports\ReportAchievementExport;
use App\Helpers\StringHelper;
use App\Models\Master\Student;
use App\Models\Transaction\StudentAchievement;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class LvRpAchievement extends Component
{
    public $views = [
        'reportType' => 1
    ];

    public $filters = [
        'startDate' => null,
        'endDate' =>  null,
        'nis' => null,
    ];

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
    ];

    protected $listeners = [
        'setInputStudent' => 'setStudent',
    ];

    public function mount()
    {
        $this->filters['startDate'] = Carbon::now()->setTimezone('Asia/Jakarta')->format('01 F Y');
        $this->filters['endDate'] = Carbon::now()->setTimezone('Asia/Jakarta')->format('t F Y');
    }

    public function render()
    {
        return view('livewire.report.lv-rp-achievement')
            ->with(['pageTitle' => "Report Achievement"])
            ->layout('layouts.cms.lv-main', ['menuName' => 'report_achievement']);
    }

    public function dtRpAchievementFilter()
    {
        if ($this->views['reportType'] == 1) {
            $this->filters['nis'] = null;
            $this->selected['student'] = [
                'nis' => null,
                'fullname' => null,
                'gender' => null,
                'birthPlace' => null,
                'birthDate' => null,
                'address' => null,
                'formattedDate' => null
            ];
        }
        $this->dispatchBrowserEvent('datatables:refresh', ['target' => "tStdReport", 'filter' => ['target' => 'reportFilters', 'data' => $this->filters]]);
    }

    public function dtRpAchievement(Request $request)
    {
        $search = StringHelper::escapeLike($request->input('search.value') ?? '');
        $searchParam = $request->input('search');
        $searchParam['value'] = $search;
        $request->merge(['search' => $searchParam]);

        $startDate = $request->input('filters.startDate') ? Carbon::createFromFormat('d F Y', $request->input('filters.startDate'), 'Asia/Jakarta')->format('Y-m-d 00:00:00') : Carbon::now()->setTimezone('Asia/Jakarta')->format('01 F Y');
        $endDate = $request->input('filters.endDate') ? Carbon::createFromFormat('d F Y', $request->input('filters.endDate'), 'Asia/Jakarta')->format('Y-m-d 23:59:59') : Carbon::now()->setTimezone('Asia/Jakarta')->format('t F Y');
        $studentNis = $request->input('filters.nis');

        $model = StudentAchievement::query()
            ->select(
                'student_achievements.*',
                'teachers.nama_guru',
                'students.nama_siswa',
                'achievements.deskripsi as nama_prestasi',
                'achievements.poin_prestasi',
                'student_achievements.created_at as tanggal_prestasi'
            )
            ->leftJoin('teachers', 'student_achievements.teacher_nip', 'teachers.nip')
            ->join('students', 'student_achievements.student_nis', 'students.nis')
            ->join('achievements', 'student_achievements.achievement_id', 'achievements.id')
            ->where('student_achievements.created_at', '>=', $startDate)
            ->where('student_achievements.created_at', '<=', $endDate)
            ->when($studentNis, function ($query, $studentNis) {
                $query->where('students.nis', $studentNis);
            });

        return DataTables::eloquent($model)
            ->order(function ($query) {
                $query->orderBy('id', 'desc');
            })
            ->addIndexColumn()
            ->editColumn('nama_guru', function ($stdSanc) {
                return $stdSanc->nama_guru ?? 'Administrator';
            })
            ->only([
                'id',
                'nama_guru',
                'nama_siswa',
                'nama_prestasi',
                'catatan',
                'tanggal_prestasi'
            ])
            ->toJson();
    }

    public function setStudent($id)
    {
        $student = Student::find($id);

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

        $this->filters['nis'] = $student->nis;
    }

    function resetFilter()
    {

        $this->filters['startDate'] = Carbon::now()->setTimezone('Asia/Jakarta')->format('01 F Y');
        $this->filters['endDate'] = Carbon::now()->setTimezone('Asia/Jakarta')->format('t F Y');
        $this->filters['nis'] = null;

        $this->selected['student'] = [
            'nis' => null,
            'fullname' => null,
            'gender' => null,
            'birthPlace' => null,
            'birthDate' => null,
            'address' => null,
            'formattedDate' => null
        ];

        $this->dispatchBrowserEvent('datatables:refresh', ['target' => "tStdReport", 'filter' => ['target' => 'reportFilters', 'data' => $this->filters]]);
    }

    function downloadExcel()
    {
        try {
            $response = Excel::download(new ReportAchievementExport($this->filters), "laporan Prestasi - {$this->filters['startDate']} sampai {$this->filters['endDate']}.xlsx");
            $this->dispatchBrowserEvent('swal-loader:close');
            return $response;
        } catch (\Exception $ex) {
            $this->dispatchBrowserEvent('notification:show', [
                'title' => "Ups, terjadi kesalahan! (Error: {$ex->getMessage()})",
                'icon' => 'error',
            ]);
        }
    }

    function downloadPdf()
    {
        try {
            $startDate = $this->filters['startDate'] ? Carbon::createFromFormat('d F Y', $this->filters['startDate'], 'Asia/Jakarta')->format('Y-m-d 00:00:00') : Carbon::now()->setTimezone('Asia/Jakarta')->format('01 F Y');
            $endDate = $this->filters['endDate'] ? Carbon::createFromFormat('d F Y', $this->filters['endDate'], 'Asia/Jakarta')->format('Y-m-d 23:59:59') : Carbon::now()->setTimezone('Asia/Jakarta')->format('t F Y');
            $studentNis = $this->filters['nis'];

            $model = StudentAchievement::query()
                ->select(
                    'student_achievements.*',
                    'teachers.nama_guru',
                    'students.nis',
                    'students.nama_siswa',
                    'achievements.deskripsi as nama_prestasi',
                    'achievements.poin_prestasi',
                )
                ->leftJoin('teachers', 'student_achievements.teacher_nip', 'teachers.nip')
                ->join('students', 'student_achievements.student_nis', 'students.nis')
                ->join('achievements', 'student_achievements.achievement_id', 'achievements.id')
                ->when($startDate, function ($query, $startDate) use ($endDate) {
                    $query->where('student_achievements.created_at', '>=', $startDate)
                        ->where('student_achievements.created_at', '<=', $endDate);
                })
                ->when($studentNis, function ($query, $studentNis) {
                    $query->where('students.nis', $studentNis);
                })
                ->orderBy('id', 'ASC')
                ->get();
            $data = [
                'data' => $model,
            ];
            // return view('layouts.pdf.pdf-achievement', $data);

            $pdf = Pdf::setOption('chroot', [Storage::path('images')])
                ->loadView('layouts.pdf.pdf-achievement', $data)
                ->setPaper('a4', 'portrait')->output();
            $this->dispatchBrowserEvent('swal-loader:close');
            return response()->streamDownload(
                fn () => print($pdf),
                "laporan Prestasi - {$this->filters['startDate']} sampai {$this->filters['endDate']}.pdf"
            );
        } catch (\Exception $ex) {
            $this->dispatchBrowserEvent('notification:show', [
                'title' => "Ups, terjadi kesalahan! (Error: {$ex->getMessage()})",
                'icon' => 'error',
            ]);
        }
    }
}
