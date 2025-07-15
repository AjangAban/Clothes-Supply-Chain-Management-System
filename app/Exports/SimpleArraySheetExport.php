<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;

class SimpleArraySheetExport implements FromArray, WithTitle
{
    protected $title;
    protected $rows;
    public function __construct($title, $rows)
    {
        $this->title = $title;
        $this->rows = $rows;
    }
    public function array(): array
    {
        if (empty($this->rows)) return [];
        $header = array_keys((array)$this->rows[0]);
        $data = [ $header ];
        foreach ($this->rows as $row) {
            $data[] = array_values((array)$row);
        }
        return $data;
    }
    public function title(): string
    {
        return $this->title;
    }
} 