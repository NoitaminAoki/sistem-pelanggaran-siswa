<?php

namespace App\Http\Livewire\Report;

use App\Exports\ReportRankBadExport;
use App\Helpers\StringHelper;
use App\Models\Master\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class LvRpRankBad extends Component
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
        return view('livewire.report.lv-rp-rank-bad')
            ->with(['pageTitle' => "Report Ranking Best Student"])
            ->layout('layouts.cms.lv-main', ['menuName' => 'report_rank']);
    }

    public function dtRpRankBadFilter()
    {
        $this->dispatchBrowserEvent('datatables:refresh', ['target' => "tStdReport", 'filter' => ['target' => 'reportFilters', 'data' => $this->filters]]);
    }

    public function dtRpRankBad(Request $request)
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
                DB::raw('COALESCE(SUM(CASE WHEN violations.jenis = "Ringan" THEN 1 ELSE 0 END), 0) as total_pelanggaran_ringan'),
                DB::raw('COALESCE(SUM(CASE WHEN violations.jenis = "Sedang" THEN 1 ELSE 0 END), 0) as total_pelanggaran_sedang'),
                DB::raw('COALESCE(SUM(CASE WHEN violations.jenis = "Berat" THEN 1 ELSE 0 END), 0) as total_pelanggaran_berat'),
                DB::raw('COALESCE(COUNT(student_violations.id), 0) as total_pelanggaran'),
                DB::raw('COALESCE(SUM(violations.bobot_poin), 0) as total_poin_pelanggaran'),
            )
            ->leftJoin('student_violations', function ($query) use ($startDate, $endDate) {
                $query->on('student_violations.student_nis', 'students.nis')
                    ->where('student_violations.created_at', '>=', $startDate)
                    ->where('student_violations.created_at', '<=', $endDate);
            })
            ->leftJoin('violations', 'violations.id', 'student_violations.violation_id')
            ->groupBy('students.id', 'students.nis', 'students.nama_siswa')
            ->having('total_poin_pelanggaran', '>', 0);

        return DataTables::eloquent($model)
            ->order(function ($query) {
                $query->orderBy('total_poin_pelanggaran', 'desc');
            })
            ->addIndexColumn()
            ->only([
                'id',
                'nis',
                'nama_siswa',
                'total_pelanggaran_ringan',
                'total_pelanggaran_sedang',
                'total_pelanggaran_berat',
                'total_pelanggaran',
                'total_poin_pelanggaran',
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
            $response = Excel::download(new ReportRankBadExport($this->filters), "laporan peringkat siswa ternakal - {$this->filters['startDate']} sampai {$this->filters['endDate']}.xlsx");
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

            $model = Student::query()
                ->select(
                    'students.id',
                    'students.nis',
                    'students.nama_siswa',
                    DB::raw('COALESCE(SUM(CASE WHEN violations.jenis = "Ringan" THEN 1 ELSE 0 END), 0) as total_pelanggaran_ringan'),
                    DB::raw('COALESCE(SUM(CASE WHEN violations.jenis = "Sedang" THEN 1 ELSE 0 END), 0) as total_pelanggaran_sedang'),
                    DB::raw('COALESCE(SUM(CASE WHEN violations.jenis = "Berat" THEN 1 ELSE 0 END), 0) as total_pelanggaran_berat'),
                    DB::raw('COALESCE(COUNT(student_violations.id), 0) as total_pelanggaran'),
                    DB::raw('COALESCE(SUM(violations.bobot_poin), 0) as total_poin_pelanggaran'),
                )
                ->leftJoin('student_violations', function ($query) use ($startDate, $endDate) {
                    $query->on('student_violations.student_nis', 'students.nis')
                        ->where('student_violations.created_at', '>=', $startDate)
                        ->where('student_violations.created_at', '<=', $endDate);
                })
                ->leftJoin('violations', 'violations.id', 'student_violations.violation_id')
                ->groupBy('students.id', 'students.nis', 'students.nama_siswa')
                ->having('total_poin_pelanggaran', '>', 0)
                ->orderBy('total_poin_pelanggaran', 'desc')
                ->limit(10)
                ->get();
            $data = [
                'data' => $model,
                'startDate' => Carbon::createFromFormat('d F Y', $this->filters['startDate'])->locale('id')->settings(['formatFunction' => 'translatedFormat'])->format("d F Y"),
                'endDate' => Carbon::createFromFormat('d F Y', $this->filters['endDate'])->locale('id')->settings(['formatFunction' => 'translatedFormat'])->format("d F Y"),
            ];
            // return view('layouts.pdf.pdf-rank-bad', $data);

            $pdf = Pdf::setOption('chroot', [Storage::path('images')])
                ->loadView('layouts.pdf.pdf-rank-bad', $data)
                ->setPaper('a4', 'portrait')->output();
            $this->dispatchBrowserEvent('swal-loader:close');
            return response()->streamDownload(
                fn () => print($pdf),
                "laporan peringkat siswa ternakal - {$this->filters['startDate']} sampai {$this->filters['endDate']}.pdf"
            );
        } catch (\Exception $ex) {
            $this->dispatchBrowserEvent('notification:show', [
                'title' => "Ups, terjadi kesalahan! (Error: {$ex->getMessage()})",
                'icon' => 'error',
            ]);
        }
    }
}
