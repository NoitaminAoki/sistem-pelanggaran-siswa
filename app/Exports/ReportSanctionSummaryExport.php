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
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class ReportSanctionSummaryExport implements
    FromQuery,
    WithHeadings,
    WithStyles,
    WithMapping,
    ShouldAutoSize,
    WithColumnFormatting,
    WithStrictNullComparison,
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
                $sheet->mergeCells('A1:G1');

                // Menambahkan alamat di bawah logo
                $sheet->getCell('A2')->setValue("Jl. SKB No.1, Karadenan, Kec. Cibinong\nKabupaten Bogor, Jawa Barat 16913\nTelp: (0251) 8582276");
                $sheet->getStyle('A2')->getAlignment()->setWrapText(true);
                $sheet->mergeCells('A2:G2'); // Sesuaikan rentang kolom jika diperlukan
                $sheet->getStyle('A2')->getFont()->setBold(true); // Gaya font
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); // Teks di tengah
                $sheet->getStyle('A2')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER); // Teks di tengah

                // Menempatkan teks logo di tengah
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER); // Teks di tengah

                // Mengatur tinggi baris
                $sheet->getRowDimension(1)->setRowHeight(71); // Mengatur tinggi baris pertama
                $sheet->getRowDimension(2)->setRowHeight(50); // Mengatur tinggi baris kedua

                $sheet->getCell("F{$this->lastRow}")->setValue("Bogor, {$datenow}\nMengetahui,\nKepala Sekolah\n\n\n\nSolihin Al Amin M.Pd");
                $sheet->getStyle("F{$this->lastRow}")->getAlignment()->setWrapText(true);
                $sheet->mergeCells("F{$this->lastRow}:G{$this->lastRow}");
                $sheet->getStyle("F{$this->lastRow}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); // Teks di tengah
                $sheet->getStyle("F{$this->lastRow}")->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER); // Teks di tengah
                $sheet->getRowDimension($this->lastRow)->setRowHeight(125); // Mengatur tinggi baris ttd
            },
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestDataRow();
        $this->lastRow = $lastRow + 2;

        $range = 'A4:G' . $lastRow;

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
