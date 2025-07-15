<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class AnalyticsExport implements WithMultipleSheets
{
    protected $data;
    public function __construct($data)
    {
        $this->data = $data;
    }
    public function sheets(): array
    {
        $sheets = [];
        foreach ($this->data as $section => $rows) {
            $sheets[] = new \App\Exports\SimpleArraySheetExport($section, $rows);
        }
        return $sheets;
    }
} 