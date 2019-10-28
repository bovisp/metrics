<?php

namespace App\Classes;

ini_set('max_execution_time', '600');

use Goutte\Client;
use App\CometCourse;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use GuzzleHttp\Client as GuzzleClient;
use Symfony\Component\DomCrawler\Crawler;

class CometScrape
{
  protected $client;

  protected $guzzleClient;

  protected $baseUrl = 'https://www.meted.ucar.edu';

  protected $languages;

  protected $page;

  public function __construct($page)
  {
    $this->client = new Client();

    $this->page = $page;

    $this->guzzleClient = new GuzzleClient([
      'curl' => [
        CURLOPT_TIMEOUT => 60,
      ],
      'verify' => false
    ]);

    $this->client->setClient($this->guzzleClient);

    $this->languages = Cache::get('cometLanguages');
  }

  public function scrape()
  {
    $crawler = $this->client->request(
      'GET', 
      "{$this->baseUrl}/training_detail.php?page={$this->page}&languageSorting=1"
    );

    $crawler->filter('.module.list_view')->children()->each(function (Crawler $node, $i) {
      $this->findEnglishModule($node);
    });
  }

  protected function isEnglishModule($node)
  {
    return !! (substr($node->attr('href'), 0, 8) === 'training') && trim($node->text()) === 'English';
  }

  protected function findEnglishModule($node)
  {
    $node->filter('.module_title a')->each(function (Crawler $innerNode, $j) {
      if ($this->isEnglishModule($innerNode)) {
        $this->storeEnglishModule($innerNode);
      }
    });
  }

  protected function storeEnglishModule($node)
  {
    $moduleId = $node->evaluate('substring-after(@href, "=")')[0];

    $moduleCrawler = $this->scrapeModulePage($moduleId);

    $newModule = $this->storeModule($moduleCrawler, $moduleId);

    $this->getOtherModuleLanguages($moduleCrawler, $newModule->id);
  }

  protected function storeModule($crawler, $moduleId, $language = 'English')
  {
    $title = $crawler->filter('#content h3 a')->first()->text();

    $meta = $this->getModuleMeta($crawler);

    $description = $crawler->filter('.tab_content_wrap #content_01')->first()->html();

    $objectives = $crawler->filter('.tab_content_wrap #content_04')->first()->html();

    $keywords = $this->getKeywords($crawler);

    $languageId = $this->getLanguage($language)->id;

    $imageSrc = $crawler->filter('.module_image img')->first()->attr('src');

    $module = CometCourse::create([
      'title' => trim($title),
      'module_id' => $moduleId,
      'publish_date' => date('Y-m-d', strtotime($meta['publish_date'])),
      'skill_level' => $meta['skill_level'],
      'completion_time' => $meta['completion_time'],
      'topics' => serialize($meta['topics']),
      'last_updated_on' => date('Y-m-d', strtotime($meta['last_updated_on'])),
      'description' => trim($description),
      'objectives' => trim($objectives),
      'keywords' => serialize($keywords),
      'language_id' => $languageId,
      'image_src' => "{$this->baseUrl}/{$imageSrc}"
    ]);

    return $module;
  }

  protected function getModuleMeta($crawler)
  {
    $meta = [];

    $crawler->filter('.module_layout li ul')
      ->children()
      ->each(function (Crawler $node, $i) use (&$meta) {
        switch (trim($node->children()->first()->html())) {
          case 'Publish Date:':
            $meta['publish_date'] = trim(
              str_replace('Publish Date:', '', strip_tags($node->html()))
            );

            break;
          
          case 'Completion Time:':
            $meta['completion_time'] = trim(
              str_replace('Completion Time:', '', strip_tags($node->html()))
            );

            break;

          case 'Last Updated On:':
            $meta['last_updated_on'] = trim(
              str_replace('Last Updated On:', '', strip_tags($node->html()))
            );

            break;

          case 'Topics:':
            $node->filter('a')->each(function (Crawler $topicNode, $j) use (&$meta) {
              $meta['topics'][] = trim($topicNode->text());
            });

            break;

          default:
            if (Str::contains($node->children()->first()->html(), 'Skill Level:')) {
              $meta['skill_level'] = trim(
                $node->children()->first()->children()->first()->html()
              );
            }

            break;
        }
      });

      if (!Arr::has($meta, 'last_updated_on')) {
        $meta['last_updated_on'] = null;
      }

      if (!Arr::has($meta, 'skill_level')) {
        $meta['skill_level'] = null;
      }

      if (!Arr::has($meta, 'completion_time')) {
        $meta['completion_time'] = null;
      }

      if (!Arr::has($meta, 'topics')) {
        $meta['topics'] = null;
      }

      return $meta;
  }

  protected function getOtherModuleLanguages($crawler, $englishModuleId)
  {
    $crawler->filter('.module_layout li ul')
      ->children()
      ->eq(0)
      ->children()
      ->each(function (Crawler $node, $i) use ($englishModuleId) {
        $this->storeModuleLanguageAssociations($node, $englishModuleId);
      });
  }

  protected function storeModuleLanguageAssociations($node, $englishModuleId)
  {
    if ($node->nodeName() === 'a' && trim($node->html()) !== 'English') {
      $moduleId = trim($node->evaluate('substring-after(@href, "=")')[0]);

      $moduleCrawler = $this->scrapeModulePage($moduleId);

      $newModule = $this->storeModule($moduleCrawler, $moduleId, trim($node->html()));

      DB::table('comet_course_languages')->insert([
        'english_comet_course_id' => $englishModuleId,
        'comet_course_id' => $newModule->id
      ]);
    }
  }

  protected function scrapeModulePage($moduleId)
  {
    return $this->client->request(
      'GET', 
      "{$this->baseUrl}/training_module.php?id={$moduleId}"
    );
  }

  protected function getKeywords($crawler)
  {
    return explode(
      ',', str_replace(
        ', ', ',', trim($crawler->filter('.tab_content_wrap #content_05')->first()->text())
        )
    );
  }

  protected function getLanguage($language)
  {
    return $this->languages
      ->where('language', $language)
      ->first();
  }
}