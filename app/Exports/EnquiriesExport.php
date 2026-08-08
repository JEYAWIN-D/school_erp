<?php

namespace App\Exports;

use App\Models\Enquiry;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EnquiriesExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function __construct(protected array $filters = []) {}

    public function collection()
    {
        return Enquiry::with(['class', 'academicYear', 'assignedTo'])
            ->when($this->filters['status'] ?? null, fn($q, $v) => $q->where('status', $v))
            ->when($this->filters['class_id'] ?? null, fn($q, $v) => $q->where('class_id', $v))
            ->when($this->filters['search'] ?? null, fn($q, $v) => $q->where(function ($q) use ($v) {
                $q->where('student_name', 'like', "%$v%")
                  ->orWhere('parent_mobile', 'like', "%$v%")
                  ->orWhere('enquiry_number', 'like', "%$v%");
            }))
            ->latest()->get();
    }

    public function headings(): array
    {
        return [
            'Enquiry #', 'Student Name', 'DOB', 'Gender', 'Class Applied',
            'Parent Name', 'Parent Mobile', 'Parent Email', 'Address',
            'Source', 'Status', 'Follow Up Date', 'Previous School',
            'Assigned To', 'Enquiry Date',
        ];
    }

    public function map($row): array
    {
        return [
            $row->enquiry_number,
            $row->student_name,
            $row->dob?->format('d/m/Y'),
            ucfirst($row->gender ?? ''),
            $row->class?->name,
            $row->parent_name,
            $row->parent_mobile,
            $row->parent_email,
            $row->address,
            ucfirst(str_replace('_', ' ', $row->source ?? '')),
            $row->status_label,
            $row->follow_up_date?->format('d/m/Y'),
            $row->previous_school,
            $row->assignedTo?->name,
            $row->created_at->format('d/m/Y H:i'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'DBEAFE']]],
        ];
    }
}
