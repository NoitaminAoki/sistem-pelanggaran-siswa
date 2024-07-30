<?php

namespace App\Http\Livewire\Report;

use App\Exports\ReportSanctionExport;
use App\Helpers\StringHelper;
use App\Models\Master\Student;
use App\Models\Transaction\StudentSanction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class LvRpSanction extends Component
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
        return view('livewire.report.lv-rp-sanction')
            ->with(['pageTitle' => "Report Sanction"])
            ->layout('layouts.cms.lv-main', ['menuName' => 'report_sanction']);
    }

    public function dtRpSanctionFilter()
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

    public function dtRpSanction(Request $request)
    {
        $search = StringHelper::escapeLike($request->input('search.value') ?? '');
        $searchParam = $request->input('search');
        $searchParam['value'] = $search;
        $request->merge(['search' => $searchParam]);

        $startDate = $request->input('filters.startDate') ? Carbon::createFromFormat('d F Y', $request->input('filters.startDate'), 'Asia/Jakarta')->format('Y-m-d 00:00:00') : Carbon::now()->setTimezone('Asia/Jakarta')->format('01 F Y');
        $endDate = $request->input('filters.endDate') ? Carbon::createFromFormat('d F Y', $request->input('filters.endDate'), 'Asia/Jakarta')->format('Y-m-d 23:59:59') : Carbon::now()->setTimezone('Asia/Jakarta')->format('t F Y');
        $studentNis = $request->input('filters.nis');

        $model = StudentSanction::query()
            ->select(
                'student_sanctions.*',
                'teachers.nama_guru',
                'students.nama_siswa',
                'sanctions.jenis as jenis_sanksi',
                'sanctions.deskripsi as nama_sanksi',
                'student_sanctions.created_at as tanggal_sanksi'
            )
            ->leftJoin('teachers', 'student_sanctions.teacher_nip', 'teachers.nip')
            ->join('students', 'student_sanctions.student_nis', 'students.nis')
            ->join('sanctions', 'student_sanctions.sanction_id', 'sanctions.id')
            ->where('student_sanctions.created_at', '>=', $startDate)
            ->where('student_sanctions.created_at', '<=', $endDate)
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
                'jenis_sanksi',
                'nama_sanksi',
                'catatan',
                'tanggal_sanksi'
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
            $response = Excel::download(new ReportSanctionExport($this->filters), "laporan Sanksi - {$this->filters['startDate']} sampai {$this->filters['endDate']}.xlsx");
            $this->dispatchBrowserEvent('swal-loader:close');
            return $response;
        } catch (\Exception $ex) {
            $this->dispatchBrowserEvent('notification:show', [
                'title' => "Ups, terjadi kesalahan! (Error: {$ex->getMessage()})",
                'icon' => 'error',
            ]);
        }
    }
}
