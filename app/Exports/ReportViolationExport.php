<?php

namespace App\Exports;

use App\Models\Transaction\StudentViolation;
use Carbon\Carbon;
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
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ReportViolationExport implements FromQuery, WithHeadings, WithStyles, WithMapping, ShouldAutoSize, WithColumnFormatting
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
            'Pelapor',
            'NIS Siswa',
            'Nama Siswa',
            'Pelanggaran',
            'Jenis Pelanggaran',
            'Catatan',
            'Tanggal Laporan'
        ];
    }

    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_TEXT,
            'C' => NumberFormat::FORMAT_TEXT,
            'D' => NumberFormat::FORMAT_TEXT,
            'E' => NumberFormat::FORMAT_TEXT,
            'F' => NumberFormat::FORMAT_TEXT,
            'G' => NumberFormat::FORMAT_TEXT,
        ];
    }

    /**
     * @param StudentViolation $studentViolation
     */
    public function map($studentViolation): array
    {
        return [
            ++$this->rowNumber,
            $studentViolation->nama_guru,
            $studentViolation->nis,
            $studentViolation->nama_siswa,
            $studentViolation->nama_pelanggaran,
            $studentViolation->jenis_pelanggaran,
            $studentViolation->catatan,
            $studentViolation->created_at,
        ];
    }

    public function query()
    {
        $startDate = $this->filters['startDate'] ? Carbon::createFromFormat('d F Y', $this->filters['startDate'], 'Asia/Jakarta')->format('Y-m-d 00:00:00') : null;
        $endDate = $this->filters['endDate'] ? Carbon::createFromFormat('d F Y', $this->filters['endDate'], 'Asia/Jakarta')->format('Y-m-d 23:59:59') : null;
        $studentNis = $this->filters['nis'];
        return StudentViolation::query()
            ->select(
                'student_violations.*',
                'teachers.nama_guru',
                'students.nis',
                'students.nama_siswa',
                'violations.jenis as jenis_pelanggaran',
                'violations.nama_pelanggaran',
            )
            ->leftJoin('teachers', 'student_violations.teacher_nip', 'teachers.nip')
            ->join('students', 'student_violations.student_nis', 'students.nis')
            ->join('violations', 'student_violations.violation_id', 'violations.id')
            ->when($startDate, function ($query, $startDate) use ($endDate) {
                $query->where('student_violations.created_at', '>=', $startDate)
                    ->where('student_violations.created_at', '<=', $endDate);
            })
            ->when($studentNis, function ($query, $studentNis) {
                $query->where('students.nis', $studentNis);
            })
            ->orderBy('id', 'ASC'); // Can be customize
    }

    public function prepareRows($rows)
    {
        return $rows->transform(function ($studentViolation) {
            $studentViolation->nama_guru = $studentViolation->nama_guru ?? 'Administrator';
            $studentViolation->created_at = Carbon::parse($studentViolation->created_at)->format('Y-m-d H:i:s');

            return $studentViolation;
        });
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
