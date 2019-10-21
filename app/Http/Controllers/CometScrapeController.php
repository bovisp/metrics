<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CometScrapeController extends Controller
{
    //
}

// function() {
//   $client = new Client();

//   $guzzleClient = new \GuzzleHttp\Client(array(
//     'curl' => array(
//         CURLOPT_TIMEOUT => 60,
//     ),
//     'verify' => false
//   ));

//   $client->setClient($guzzleClient);

//   $crawler = $client->request('GET', 'https://www.meted.ucar.edu/training_detail.php?page=1&languageSorting=%');

  // $arr = [
  //   'thumbnail' => $crawler->filter('.module.list_view')->children()->first()
  // ];

  // dd($crawler->filter('.page_nav span')->eq(1)->previousAll()->text());
  // $crawler->filter('.module.list_view')->children()->each(function(Crawler $node, $i) {
  //   dd($node->filter('.thumbnail a img')->attr('src'));
  // });
// });
