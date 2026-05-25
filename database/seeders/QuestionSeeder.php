<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuestionSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('survey_questions')->insert([

            // BEFORE
            [
                'survey_id' => 1,
                'question_text' => 'How clear are the campaign objectives?',
                'type' => 'rating',
                'scale' => 5,
                'dimension' => 'planning'
            ],

            [
                'survey_id' => 1,
                'question_text' => 'How prepared do you feel?',
                'type' => 'rating',
                'scale' => 5,
                'dimension' => 'readiness'
            ],

            // DURING
            [
                'survey_id' => 2,
                'question_text' => 'How good is team coordination?',
                'type' => 'rating',
                'scale' => 5,
                'dimension' => 'organization'
            ],

            [
                'survey_id' => 2,
                'question_text' => 'Are resources available?',
                'type' => 'yes_no',
                'scale' => null,
                'dimension' => 'resources'
            ],

            // AFTER
            [
                'survey_id' => 3,
                'question_text' => 'Overall satisfaction?',
                'type' => 'rating',
                'scale' => 5,
                'dimension' => 'satisfaction'
            ],

            [
                'survey_id' => 3,
                'question_text' => 'Would you participate again?',
                'type' => 'rating',
                'scale' => 5,
                'dimension' => 'loyalty'
            ],
        ]);
    }
}
