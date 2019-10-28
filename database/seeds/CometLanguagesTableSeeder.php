<?php

use Illuminate\Database\Seeder;

class CometLanguagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::table('comet_languages')->insert([
        [
          'language_id' => 1,
          'language' => 'English'
        ],
        [
          'language_id' => 2,
          'language' => 'French'
        ],
        [
          'language_id' => 3,
          'language' => 'Indonesian'
        ],
        [
          'language_id' => 4,
          'language' => 'Portuguese'
        ],
        [
          'language_id' => 5,
          'language' => 'Russian'
        ],
        [
          'language_id' => 6,
          'language' => 'Spanish'
        ],
        [
          'language_id' => 7,
          'language' => 'German'
        ],
        [
          'language_id' => 8,
          'language' => 'Chinese (Traditional)'
        ],
        [
          'language_id' => 9,
          'language' => 'Greek'
        ]
      ]);
    }
}
