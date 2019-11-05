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

  /**
   * Create a new job instance.
   *
   * @return void
   */
  public function __construct(Carbon $startDate, Carbon $endDate)
  {
    $this->startDate = $startDate;

    $this->endDate = $endDate;
  }

  /**
   * Execute the job.
   *
   * @return void
   */
  public function handle()
  {
    $filename = "comet_stats_{$this->startDate->format('Ymd')}_{$this->endDate->format('Ymd')}.xlsx";

    $stats = (new CometReport($this->startDate, $this->endDate))
      ->process();

    Excel::store(new CometExport($stats), $filename);

    Mail::to(request()->user())->send(
      new CometStats($this->startDate, $this->endDate, $filename)
    );

    Storage::delete($filename);
  }
}
