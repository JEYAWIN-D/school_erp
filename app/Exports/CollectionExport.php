<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;

class CollectionExport implements FromCollection, WithTitle
{
    public function __construct(
        protected \Illuminate\Support\Collection $data,
        protected string $title = 'Sheet'
    ) {}

    public function collection()
    {
        return $this->data;
    }

    public function title(): string
    {
        return $this->title;
    }
}
