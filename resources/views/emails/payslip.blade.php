<x-mail::message>
# Payslip for {{ $month }}

Dear {{ $employee->full_name }},

Please find your payslip for **{{ $month }}** attached to this email.

<x-mail::table>
| Details | Amount |
|:--------|-------:|
| Gross Salary | ₹{{ number_format($payroll->gross_salary ?? 0, 2) }} |
| Total Deductions | ₹{{ number_format($payroll->total_deductions ?? 0, 2) }} |
| **Net Pay** | **₹{{ number_format($payroll->net_salary ?? 0, 2) }}** |
</x-mail::table>

The payslip PDF is attached to this email for your records.

If you have any queries regarding your payslip, please contact the HR/Accounts department.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
