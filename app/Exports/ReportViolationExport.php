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
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class ReportViolationExport implements
    FromQuery,
    WithHeadings,
    WithStyles,
    WithMapping,
    ShouldAutoSize,
    WithColumnFormatting,
    WithCustomStartCell,
    WithDrawings,
    WithEvents
{
    use Exportable;

    protected $filters;

    private $rowNumber = 0;
    private $lastRow = 0;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    public function startCell(): string
    {
        return 'A4';
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

    public function drawings()
    {
        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('This is my logo');
        $drawing->setPath(public_path('images/logo-smkn-2-cibinong.png')); // Path ke file logo
        $drawing->setHeight(90);
        $drawing->setCoordinates('A1'); // Tentukan posisi gambar

        return $drawing;
    }

    public function registerEvents(): array
    {
        $datenow = Carbon::now()->locale('id')->settings(['formatFunction' => 'translatedFormat'])->format("d F Y");
        return [
            AfterSheet::class => function (AfterSheet $event) use ($datenow) {
                $sheet = $event->sheet->getDelegate();

                // Menggabungkan kolom dari A1 sampai H1
                $sheet->mergeCells('A1:H1');

                // Menambahkan alamat di bawah logo
                $sheet->getCell('A2')->setValue("Jl. SKB No.1, Karadenan, Kec. Cibinong\nKabupaten Bogor, Jawa Barat 16913\nTelp: (0251) 8582276");
                $sheet->getStyle('A2')->getAlignment()->setWrapText(true);
                $sheet->mergeCells('A2:H2'); // Sesuaikan rentang kolom jika diperlukan
                $sheet->getStyle('A2')->getFont()->setBold(true); // Gaya font
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); // Teks di tengah
                $sheet->getStyle('A2')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER); // Teks di tengah

                // Menempatkan teks logo di tengah
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER); // Teks di tengah

                // Mengatur tinggi baris
                $sheet->getRowDimension(1)->setRowHeight(71); // Mengatur tinggi baris pertama
                $sheet->getRowDimension(2)->setRowHeight(50); // Mengatur tinggi baris kedua

                $sheet->getCell("G{$this->lastRow}")->setValue("Bogor, {$datenow}\nMengetahui,\nKepala Sekolah\n\n\n\nSolihin Al Amin M.Pd");
                $sheet->getStyle("G{$this->lastRow}")->getAlignment()->setWrapText(true);
                $sheet->mergeCells("G{$this->lastRow}:H{$this->lastRow}");
                $sheet->getStyle("G{$this->lastRow}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); // Teks di tengah
                $sheet->getStyle("G{$this->lastRow}")->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER); // Teks di tengah
                $sheet->getRowDimension($this->lastRow)->setRowHeight(125); // Mengatur tinggi baris ttd
            },
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestDataRow();
        $this->lastRow = $lastRow + 2;

        $range = 'A4:H' . $lastRow;

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
            4    => [
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['argb' => Color::COLOR_YELLOW],
                ],
                'alignment' => [
                    'horizontal'    => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical'    => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ]
            ]
        ];
    }
}
