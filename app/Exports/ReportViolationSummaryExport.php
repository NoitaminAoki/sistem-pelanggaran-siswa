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

class ReportViolationSummaryExport implements FromQuery, WithHeadings, WithStyles, WithMapping, ShouldAutoSize, WithColumnFormatting, WithStrictNullComparison
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
            'Pelanggaran (Ringan)',
            'Pelanggaran (Sedang)',
            'Pelanggaran (Berat)',
            'Total Pelanggaran',
            'Total Poin',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_TEXT, // Assuming the VARCHAR column is in column 'E'
            'C' => NumberFormat::FORMAT_TEXT,
            'D' => NumberFormat::FORMAT_NUMBER,
            'E' => NumberFormat::FORMAT_NUMBER,
            'F' => NumberFormat::FORMAT_NUMBER,
            'G' => NumberFormat::FORMAT_NUMBER,
            'H' => NumberFormat::FORMAT_NUMBER,
        ];
    }

    /**
     * @param StudentViolation $studentViolation
     */
    public function map($studentViolation): array
    {
        return [
            ++$this->rowNumber,
            $studentViolation->nis,
            $studentViolation->nama_siswa,
            $studentViolation->total_pelanggaran_ringan,
            $studentViolation->total_pelanggaran_sedang,
            $studentViolation->total_pelanggaran_berat,
            $studentViolation->total_pelanggaran,
            $studentViolation->total_poin,
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
                DB::raw('COALESCE(SUM(CASE WHEN violations.jenis = "Ringan" THEN 1 ELSE 0 END), 0) as total_pelanggaran_ringan'),
                DB::raw('COALESCE(SUM(CASE WHEN violations.jenis = "Sedang" THEN 1 ELSE 0 END), 0) as total_pelanggaran_sedang'),
                DB::raw('COALESCE(SUM(CASE WHEN violations.jenis = "Berat" THEN 1 ELSE 0 END), 0) as total_pelanggaran_berat'),
                DB::raw('COALESCE(COUNT(violations.id), 0) as total_pelanggaran'),
                DB::raw('COALESCE(SUM(violations.bobot_poin), 0) as total_poin'),
            )
            ->leftJoin('student_violations', function ($query) use ($startDate, $endDate) {
                $query->on('student_violations.student_nis', 'students.nis')
                    ->where('student_violations.created_at', '>=', $startDate)
                    ->where('student_violations.created_at', '<=', $endDate);
            })
            ->leftJoin('violations', 'violations.id', 'student_violations.violation_id')
            ->groupBy('students.id', 'students.nis', 'students.nama_siswa');
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestDataRow();

        $range = 'A1:H' . $lastRow;

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
