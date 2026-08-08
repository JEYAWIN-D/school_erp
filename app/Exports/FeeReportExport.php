<?php
namespace App\Exports;
use App\Models\FeePayment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class FeeReportExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private $filters = []) {}

    public function collection()
    {
        return FeePayment::with(['student','feeHead'])
            ->where('is_cancelled', false)
            ->when($this->filters['from_date'] ?? null, fn($q,$v) => $q->whereDate('payment_date','>=',$v))
            ->when($this->filters['to_date'] ?? null, fn($q,$v) => $q->whereDate('payment_date','<=',$v))
            ->latest('payment_date')->get();
    }

    public function headings(): array
    {
        return ['Date','Receipt No','Student','Admission No','Class','Fee Head','Amount','Late Fee','Discount','Total Paid','Mode'];
    }

    public function map($row): array
    {
        return [
            $row->payment_date?->format('d/m/Y'),
            $row->receipt_number,
            $row->student?->full_name,
            $row->student?->admission_number,
            $row->student?->currentEnrollment?->class?->name,
            $row->feeHead?->name,
            $row->amount,
            $row->late_fee,
            $row->discount,
            $row->total_paid,
            strtoupper($row->payment_mode),
        ];
    }
}
