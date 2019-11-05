<?php

namespace App\Jobs;

use Carbon\Carbon;
use App\Mail\CometStats;
use App\Classes\CometReport;
use App\Exports\CometExport;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessCometStats implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
  protected $startDate;

  protected $endDate;

  protected $shouldMail;

  /**
   * Create a new job instance.
   *
   * @return void
   */
  public function __construct(Carbon $startDate, Carbon $endDate, $shouldMail)
  {
    $this->startDate = $startDate;

    $this->endDate = $endDate;

    $this->shouldMail = $shouldMail;
  }

  /**
   * Execute the job.
   *
   * @return void
   */
  public function handle()
  {
    $stats = (new CometReport($this->startDate, $this->endDate))
      ->process();

    if ($this->shouldMail) {
      $this->mail($stats);

      return;
    }

    return $stats;
  }

  protected function mail($stats)
  {
    $filename = "comet_stats_{$this->startDate->format('Ymd')}_{$this->endDate->format('Ymd')}.xlsx";

    Excel::store(new CometExport($stats), $filename);

    Mail::to(request()->user())->send(
      new CometStats($this->startDate, $this->endDate, $filename)
    );

    Storage::delete($filename);
  }
}
