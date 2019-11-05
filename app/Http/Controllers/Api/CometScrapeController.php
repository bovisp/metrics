<?php

namespace App\Http\Controllers\Api;

ini_set('max_execution_time', '600');

use Goutte\Client;
use App\CometCourse;
use App\Classes\CometScrape;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client as GuzzleClient;

class CometScrapeController extends Controller
{
  protected $client;

  protected $guzzleClient;

  protected $baseUrl = 'https://www.meted.ucar.edu';

  public function __construct()
  {
    $this->client = new Client();

    $this->guzzleClient = new GuzzleClient([
      'curl' => [
        CURLOPT_TIMEOUT => 60,
      ],
      'verify' => false
    ]);

    $this->client->setClient($this->guzzleClient);
  }

  public function scrape()
  {
    CometCourse::truncate();

    $crawler = $this->client
      ->request(
        'GET', 
        "{$this->baseUrl}/training_detail.php?topicSorting=%25&languageSorting=1&module_sorting=publishDateDesc"
      );

    return $crawler->filter('.page_nav span')
      ->eq(1)
      ->previousAll()
      ->text();
  }

  public function scrapeModuleListingPage($page)
  {
    (new CometScrape($page))->scrape();  

    return $page;
  }
}