<?php

namespace App\Http\Livewire\Report;

use App\Exports\ReportRankBestExport;
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

class LvRpRankBest extends Component
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
        return view('livewire.report.lv-rp-rank-best')
            ->with(['pageTitle' => "Report Ranking Best Student"])
            ->layout('layouts.cms.lv-main', ['menuName' => 'report_rank']);
    }

    public function dtRpRankBestFilter()
    {
        $this->dispatchBrowserEvent('datatables:refresh', ['target' => "tStdReport", 'filter' => ['target' => 'reportFilters', 'data' => $this->filters]]);
    }

    public function dtRpRankBest(Request $request)
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
                DB::raw('COALESCE(COUNT(student_achievements.id), 0) as total_prestasi'),
                DB::raw('COALESCE(SUM(student_achievements.poin_penambahan), 0) as total_poin_prestasi'),
                DB::raw('COALESCE(COUNT(student_violations.id), 0) as total_pelanggaran'),
                DB::raw('COALESCE(SUM(violations.bobot_poin), 0) as total_poin_pelanggaran'),
                DB::raw('(COALESCE(SUM(student_achievements.poin_penambahan), 0) - COALESCE(SUM(violations.bobot_poin), 0)) as poin_akhir'),
            )
            ->leftJoin('student_achievements', function ($query) use ($startDate, $endDate) {
                $query->on('student_achievements.student_nis', 'students.nis')
                    ->where('student_achievements.created_at', '>=', $startDate)
                    ->where('student_achievements.created_at', '<=', $endDate);
            })
            ->leftJoin('student_violations', function ($query) use ($startDate, $endDate) {
                $query->on('student_violations.student_nis', 'students.nis')
                    ->where('student_violations.created_at', '>=', $startDate)
                    ->where('student_violations.created_at', '<=', $endDate);
            })
            ->leftJoin('violations', 'violations.id', 'student_violations.violation_id')
            ->groupBy('students.id', 'students.nis', 'students.nama_siswa')
            ->having('poin_akhir', '>=', 0);

        return DataTables::eloquent($model)
            ->order(function ($query) {
                $query->orderBy('poin_akhir', 'desc');
            })
            ->addIndexColumn()
            ->only([
                'id',
                'nis',
                'nama_siswa',
                'total_prestasi',
                'total_poin_prestasi',
                'total_pelanggaran',
                'total_poin_pelanggaran',
                'poin_akhir',
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
            $response = Excel::download(new ReportRankBestExport($this->filters), "laporan peringkat siswa terbaik - {$this->filters['startDate']} sampai {$this->filters['endDate']}.xlsx");
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
                    DB::raw('COALESCE(COUNT(student_achievements.id), 0) as total_prestasi'),
                    DB::raw('COALESCE(SUM(student_achievements.poin_penambahan), 0) as total_poin_prestasi'),
                    DB::raw('COALESCE(COUNT(student_violations.id), 0) as total_pelanggaran'),
                    DB::raw('COALESCE(SUM(violations.bobot_poin), 0) as total_poin_pelanggaran'),
                    DB::raw('(COALESCE(SUM(student_achievements.poin_penambahan), 0) - COALESCE(SUM(violations.bobot_poin), 0)) as poin_akhir'),
                )
                ->leftJoin('student_achievements', function ($query) use ($startDate, $endDate) {
                    $query->on('student_achievements.student_nis', 'students.nis')
                        ->where('student_achievements.created_at', '>=', $startDate)
                        ->where('student_achievements.created_at', '<=', $endDate);
                })
                ->leftJoin('student_violations', function ($query) use ($startDate, $endDate) {
                    $query->on('student_violations.student_nis', 'students.nis')
                        ->where('student_violations.created_at', '>=', $startDate)
                        ->where('student_violations.created_at', '<=', $endDate);
                })
                ->leftJoin('violations', 'violations.id', 'student_violations.violation_id')
                ->groupBy('students.id', 'students.nis', 'students.nama_siswa')
                ->having('poin_akhir', '>=', 0)
                ->orderBy('poin_akhir', 'desc')
                ->limit(10)
                ->get();
            $data = [
                'data' => $model,
                'startDate' => Carbon::createFromFormat('d F Y', $this->filters['startDate'])->locale('id')->settings(['formatFunction' => 'translatedFormat'])->format("d F Y"),
                'endDate' => Carbon::createFromFormat('d F Y', $this->filters['endDate'])->locale('id')->settings(['formatFunction' => 'translatedFormat'])->format("d F Y"),
            ];
            // return view('layouts.pdf.pdf-rank-best', $data);

            $pdf = Pdf::setOption('chroot', [Storage::path('images')])
                ->loadView('layouts.pdf.pdf-rank-best', $data)
                ->setPaper('a4', 'portrait')->output();
            $this->dispatchBrowserEvent('swal-loader:close');
            return response()->streamDownload(
                fn () => print($pdf),
                "laporan peringkat siswa terbaik - {$this->filters['startDate']} sampai {$this->filters['endDate']}.pdf"
            );
        } catch (\Exception $ex) {
            $this->dispatchBrowserEvent('notification:show', [
                'title' => "Ups, terjadi kesalahan! (Error: {$ex->getMessage()})",
                'icon' => 'error',
            ]);
        }
    }
}
