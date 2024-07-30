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

class ReportAchievementSummaryExport implements FromQuery, WithHeadings, WithStyles, WithMapping, ShouldAutoSize, WithColumnFormatting, WithStrictNullComparison
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
            'Total Prestasi',
            'Total Poin',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_TEXT,
            'C' => NumberFormat::FORMAT_TEXT,
            'D' => NumberFormat::FORMAT_NUMBER,
            'E' => NumberFormat::FORMAT_NUMBER,
        ];
    }

    /**
     * @param StudentAchievement $studentAchievement
     */
    public function map($studentAchievement): array
    {
        return [
            ++$this->rowNumber,
            $studentAchievement->nis,
            $studentAchievement->nama_siswa,
            $studentAchievement->total_prestasi,
            $studentAchievement->total_poin,
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
                DB::raw('COALESCE(COUNT(achievements.id), 0) as total_prestasi'),
                DB::raw('COALESCE(SUM(student_achievements.poin_penambahan), 0) as total_poin'),
            )
            ->leftJoin('student_achievements', function ($query) use ($startDate, $endDate) {
                $query->on('student_achievements.student_nis', 'students.nis')
                    ->where('student_achievements.created_at', '>=', $startDate)
                    ->where('student_achievements.created_at', '<=', $endDate);
            })
            ->leftJoin('achievements', 'achievements.id', 'student_achievements.achievement_id')
            ->orderBy('students.nama_siswa', 'asc')
            ->groupBy('students.id', 'students.nis', 'students.nama_siswa');
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestDataRow();

        $range = 'A1:E' . $lastRow;

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
