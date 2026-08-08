<?php

namespace App\Imports;

use App\Models\Book;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;

class BooksImport implements ToModel, WithHeadingRow, SkipsEmptyRows, SkipsOnError
{
    use SkipsErrors;

    private int $rowCount = 0;

    public function model(array $row): ?Book
    {
        $title = trim($row['title'] ?? $row['book_title'] ?? '');
        if (! $title) return null;

        $this->rowCount++;

        return new Book([
            'accession_number' => $row['accession_number'] ?? $this->generateAccessionNumber(),
            'title'            => $title,
            'author'           => $row['author'] ?? null,
            'publisher'        => $row['publisher'] ?? null,
            'isbn'             => $row['isbn'] ?? null,
            'edition'          => $row['edition'] ?? null,
            'language'         => $row['language'] ?? 'English',
            'total_copies'     => (int) ($row['total_copies'] ?? $row['copies'] ?? 1),
            'available_copies' => (int) ($row['total_copies'] ?? $row['copies'] ?? 1),
            'purchase_price'   => ! empty($row['purchase_price']) ? (float) $row['purchase_price'] : null,
            'purchase_date'    => ! empty($row['purchase_date']) ? \Carbon\Carbon::parse($row['purchase_date'])->toDateString() : null,
            'location'         => $row['location'] ?? $row['shelf'] ?? null,
            'is_available'     => true,
        ]);
    }

    public function getRowCount(): int
    {
        return $this->rowCount;
    }

    private function generateAccessionNumber(): string
    {
        $last = Book::max('id') ?? 0;
        return 'ACC-' . str_pad($last + $this->rowCount + 1, 6, '0', STR_PAD_LEFT);
    }
}
