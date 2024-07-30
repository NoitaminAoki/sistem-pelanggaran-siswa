<?php

namespace App\Exports;

use App\Models\Master\Student;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ReportSanctionSummaryExport implements FromQuery, WithHeadings, WithStyles, WithMapping, ShouldAutoSize, WithColumnFormatting, WithStrictNullComparison
{
    use Exportable;

    protected $filters;

    private $rowNumber = 0;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    public function headings(): array
    {
        return [
            'No',
            'NIS',
            'Nama Siswa',
            'Sanksi (Ringan)',
            'Sanksi (Sedang)',
            'Sanksi (Berat)',
            'Total Sanksi',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_TEXT,
            'C' => NumberFormat::FORMAT_TEXT,
            'D' => NumberFormat::FORMAT_NUMBER,
            'E' => NumberFormat::FORMAT_NUMBER,
            'F' => NumberFormat::FORMAT_NUMBER,
            'G' => NumberFormat::FORMAT_NUMBER,
        ];
    }

    /**
     * @param StudentSanction $studentSanction
     */
    public function map($studentSanction): array
    {
        return [
            ++$this->rowNumber,
            $studentSanction->nis,
            $studentSanction->nama_siswa,
            $studentSanction->total_sanksi_ringan,
            $studentSanction->total_sanksi_sedang,
            $studentSanction->total_sanksi_berat,
            $studentSanction->total_sanksi,
        ];
    }

    public function query()
    {
        $startDate = $this->filters['startDate'] ? Carbon::createFromFormat('d F Y', $this->filters['startDate'], 'Asia/Jakarta')->format('Y-m-d 00:00:00') : null;
        $endDate = $this->filters['endDate'] ? Carbon::createFromFormat('d F Y', $this->filters['endDate'], 'Asia/Jakarta')->format('Y-m-d 23:59:59') : null;
        return Student::query()
            ->select(
                'students.id',
                'students.nis',
                'students.nama_siswa',
                DB::raw('COALESCE(SUM(CASE WHEN sanctions.jenis = "Ringan" THEN 1 ELSE 0 END), 0) as total_sanksi_ringan'),
                DB::raw('COALESCE(SUM(CASE WHEN sanctions.jenis = "Sedang" THEN 1 ELSE 0 END), 0) as total_sanksi_sedang'),
                DB::raw('COALESCE(SUM(CASE WHEN sanctions.jenis = "Berat" THEN 1 ELSE 0 END), 0) as total_sanksi_berat'),
                DB::raw('COALESCE(COUNT(sanctions.id), 0) as total_sanksi'),
            )
            ->leftJoin('student_sanctions', function ($query) use ($startDate, $endDate) {
                $query->on('student_sanctions.student_nis', 'students.nis')
                    ->where('student_sanctions.created_at', '>=', $startDate)
                    ->where('student_sanctions.created_at', '<=', $endDate);
            })
            ->leftJoin('sanctions', 'sanctions.id', 'student_sanctions.sanction_id')
            ->orderBy('students.nama_siswa', 'asc')
            ->groupBy('students.id', 'students.nis', 'students.nama_siswa');
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestDataRow();

        $range = 'A1:G' . $lastRow;

        $sheet->getStyle($range)->applyFromArray([
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
                'inside' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
            ],
        ]);

        return [
            // Style the first row.
            1    => [
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['argb' => Color::COLOR_YELLOW],
                ]
            ]
        ];
    }
}
