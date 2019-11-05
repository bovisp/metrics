<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class CometExport implements FromArray, WithMultipleSheets
{
    protected $stats;

    public function __construct($stats)
    {
      $this->stats = $stats;
    }

    public function array(): array
    {
        return $this->stats;
    }

    public function sheets(): array
    {
        return [
          new CometViewSheet(collect($this->stats['views'])),
          new CometCompletionSheet(collect($this->stats['completions']))
        ];
    }
}
