<?php
namespace App\Exports;
use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class StudentsExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private $classId = null, private $section = null) {}

    public function collection()
    {
        return Student::with('currentEnrollment.class','currentEnrollment.section')
            ->when($this->classId, fn($q) => $q->whereHas('currentEnrollment', fn($e) => $e->where('class_id', $this->classId)))
            ->where('status', 'active')->orderBy('first_name')->get();
    }

    public function headings(): array
    {
        return ['Admission No','Name','Gender','DOB','Class','Section','Father Name','Mobile','Email','Status'];
    }

    public function map($row): array
    {
        return [
            $row->admission_number,
            $row->full_name,
            $row->gender,
            $row->dob?->format('d/m/Y'),
            $row->currentEnrollment?->class?->name,
            $row->currentEnrollment?->section?->name,
            $row->father_name,
            $row->father_mobile,
            $row->email,
            $row->status,
        ];
    }
}
