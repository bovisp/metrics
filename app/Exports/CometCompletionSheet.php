<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class CometCompletionSheet implements FromCollection, WithHeadings, WithTitle
{
  protected $views;

  public function __construct($completions)
  {
    $this->completions = $completions;
  }

  /**
  * @return \Illuminate\Support\Collection
  */
  public function collection()
  {
      return $this->completions;
  }

  public function headings(): array
  {
    return [
      'English Module Title',
      'French Module Title',
      'English',
      'French',
      'Other',
      'Total'
    ];
  }

  public function title(): string
  {
    return 'Comet Module Completions';
  }
}
