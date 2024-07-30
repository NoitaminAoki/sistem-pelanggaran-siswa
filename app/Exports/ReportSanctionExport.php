<?php

namespace App\Exports;

use App\Models\Transaction\StudentSanction;
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

class ReportSanctionExport implements FromQuery, WithHeadings, WithStyles, WithMapping, ShouldAutoSize, WithColumnFormatting
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
            'Sanksi',
            'Jenis Sanksi',
            'Total Poin Pelanggaran (saat dikenakan sanksi)',
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
            $studentSanction->nama_guru,
            $studentSanction->nis,
            $studentSanction->nama_siswa,
            $studentSanction->nama_sanksi,
            $studentSanction->jenis_sanksi,
            $studentSanction->poin_awal,
            $studentSanction->created_at,
        ];
    }

    public function query()
    {
        $startDate = $this->filters['startDate'] ? Carbon::createFromFormat('d F Y', $this->filters['startDate'], 'Asia/Jakarta')->format('Y-m-d 00:00:00') : null;
        $endDate = $this->filters['endDate'] ? Carbon::createFromFormat('d F Y', $this->filters['endDate'], 'Asia/Jakarta')->format('Y-m-d 23:59:59') : null;
        $studentNis = $this->filters['nis'];
        return StudentSanction::query()
            ->select(
                'student_sanctions.*',
                'teachers.nama_guru',
                'students.nis',
                'students.nama_siswa',
                'sanctions.jenis as jenis_sanksi',
                'sanctions.deskripsi as nama_sanksi',
            )
            ->leftJoin('teachers', 'student_sanctions.teacher_nip', 'teachers.nip')
            ->join('students', 'student_sanctions.student_nis', 'students.nis')
            ->join('sanctions', 'student_sanctions.sanction_id', 'sanctions.id')
            ->when($startDate, function ($query, $startDate) use ($endDate) {
                $query->where('student_sanctions.created_at', '>=', $startDate)
                    ->where('student_sanctions.created_at', '<=', $endDate);
            })
            ->when($studentNis, function ($query, $studentNis) {
                $query->where('students.nis', $studentNis);
            })
            ->orderBy('id', 'ASC'); // Can be customize
    }

    public function prepareRows($rows)
    {
        return $rows->transform(function ($studentSanction) {
            $studentSanction->nama_guru = $studentSanction->nama_guru ?? 'Administrator';
            $studentSanction->created_at = Carbon::parse($studentSanction->created_at)->format('Y-m-d H:i:s');

            return $studentSanction;
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
