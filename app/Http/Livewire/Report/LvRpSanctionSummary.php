<?php

namespace App\Http\Livewire\Report;

use App\Exports\ReportSanctionSummaryExport;
use App\Helpers\StringHelper;
use App\Models\Master\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class LvRpSanctionSummary extends Component
{
    public $filters = [
        'startDate' => null,
        'endDate' =>  null,
    ];

    public function mount()
    {
        $this->filters['startDate'] = Carbon::now()->setTimezone('Asia/Jakarta')->format('01 F Y');
        $this->filters['endDate'] = Carbon::now()->setTimezone('Asia/Jakarta')->format('t F Y');
    }

    public function render()
    {
        return view('livewire.report.lv-rp-sanction-summary')
            ->with(['pageTitle' => "Report Sanction (Summary)"])
            ->layout('layouts.cms.lv-main', ['menuName' => 'report_sanction']);
    }

    public function dtRpSanctionFilter()
    {
        $this->dispatchBrowserEvent('datatables:refresh', ['target' => "tStdReport", 'filter' => ['target' => 'reportFilters', 'data' => $this->filters]]);
    }

    public function dtRpSanctionSummary(Request $request)
    {
        $search = StringHelper::escapeLike($request->input('search.value') ?? '');
        $searchParam = $request->input('search');
        $searchParam['value'] = $search;
        $request->merge(['search' => $searchParam]);

        $startDate = $request->input('filters.startDate') ? Carbon::createFromFormat('d F Y', $request->input('filters.startDate'), 'Asia/Jakarta')->format('Y-m-d 00:00:00') : Carbon::now()->setTimezone('Asia/Jakarta')->format('01 F Y');
        $endDate = $request->input('filters.endDate') ? Carbon::createFromFormat('d F Y', $request->input('filters.endDate'), 'Asia/Jakarta')->format('Y-m-d 23:59:59') : Carbon::now()->setTimezone('Asia/Jakarta')->format('t F Y');

        $model = Student::query()
            ->select(
                'students.id',
                'students.nis',
                'students.nama_siswa',
                DB::raw('COALESCE(SUM(CASE WHEN sanctions.jenis = "Ringan" THEN 1 ELSE 0 END), 0) as total_sanksi_ringan'),
                DB::raw('COALESCE(SUM(CASE WHEN sanctions.jenis = "Sedang" THEN 1 ELSE 0 END), 0) as total_sanksi_sedang'),
                DB::raw('COALESCE(SUM(CASE WHEN sanctions.jenis = "Berat" THEN 1 ELSE 0 END), 0) as total_sanksi_berat'),
                DB::raw('COUNT(sanctions.id) as total_sanksi'),
            )
            ->leftJoin('student_sanctions', function ($query) use ($startDate, $endDate) {
                $query->on('student_sanctions.student_nis', 'students.nis')
                    ->where('student_sanctions.created_at', '>=', $startDate)
                    ->where('student_sanctions.created_at', '<=', $endDate);
            })
            ->leftJoin('sanctions', 'sanctions.id', 'student_sanctions.sanction_id')
            ->groupBy('students.id', 'students.nis', 'students.nama_siswa');

        return DataTables::eloquent($model)
            ->order(function ($query) {
                $query->orderBy('students.nama_siswa', 'asc');
            })
            ->addIndexColumn()
            ->only([
                'id',
                'nis',
                'nama_siswa',
                'total_sanksi_ringan',
                'total_sanksi_sedang',
                'total_sanksi_berat',
                'total_sanksi',
            ])
            ->toJson();
    }

    function resetFilter()
    {

        $this->filters['startDate'] = Carbon::now()->setTimezone('Asia/Jakarta')->format('01 F Y');
        $this->filters['endDate'] = Carbon::now()->setTimezone('Asia/Jakarta')->format('t F Y');

        $this->dispatchBrowserEvent('datatables:refresh', ['target' => "tStdReport", 'filter' => ['target' => 'reportFilters', 'data' => $this->filters]]);
    }

    function downloadExcel()
    {
        try {
            $response = Excel::download(new ReportSanctionSummaryExport($this->filters), "laporan sanksi (summary) - {$this->filters['startDate']} sampai {$this->filters['endDate']}.xlsx");
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
