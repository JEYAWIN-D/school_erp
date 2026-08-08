<?php
namespace App\Exports;
use App\Models\Book;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class StockRegisterExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Book::with('category')->orderBy('title')->get();
    }

    public function headings(): array
    {
        return ['Accession No','Title','Author','Publisher','ISBN','Category','Total Copies','Available','Issued','Condition'];
    }

    public function map($row): array
    {
        return [
            $row->accession_number,
            $row->title,
            $row->author,
            $row->publisher,
            $row->isbn,
            $row->category?->name,
            $row->total_copies,
            $row->available_copies,
            $row->total_copies - $row->available_copies,
            $row->condition,
        ];
    }
}
