<?php
namespace App\Exports;
use App\Models\PayrollRecord;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SalaryRegisterExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private int $month, private int $year) {}

    public function collection()
    {
        return PayrollRecord::with(['employee.department','employee.designation'])
            ->where('month', $this->month)->where('year', $this->year)->get();
    }

    public function headings(): array
    {
        return ['Emp ID','Name','Department','Designation','Basic','HRA','TA','Other Allow','Gross','PF','PT','TDS','Deductions','Net Pay','Payment Mode','Bank Account'];
    }

    public function map($row): array
    {
        return [
            $row->employee?->employee_code,
            $row->employee?->full_name,
            $row->employee?->department?->name,
            $row->employee?->designation?->name,
            $row->basic_salary,
            $row->hra ?? 0,
            $row->transport_allowance ?? 0,
            $row->other_allowances ?? 0,
            $row->gross_salary,
            $row->pf_employee ?? 0,
            $row->professional_tax ?? 0,
            $row->tds ?? 0,
            $row->total_deductions,
            $row->net_salary,
            strtoupper($row->payment_mode ?? 'bank'),
            $row->employee?->bank_account_number,
        ];
    }
}
