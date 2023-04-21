<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class VoucherExport implements FromView, WithTitle, ShouldAutoSize, WithStyles, WithEvents
{
    public $subject;
    public $vouchers;

    public function __construct($subject, $vouchers) {
        $this->subject = $subject;
        $this->vouchers = $vouchers;
    }

    public function view(): View
    {
        return view('exports.extract-voucher', [
            'vouchers' => $this->vouchers
        ]);
    }

    public function title(): string
    {
        return $this->subject;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true]]
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $workSheet = $event->sheet->getDelegate();

                $workSheet->freezePane('A2');
                $workSheet->getStyle('A1:F1')
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);
            },
        ];
    }
}
