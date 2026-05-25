<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IndicatorQuestionSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('indicator_survey_question')->insert([

            // 🟢 Volunteer Satisfaction
            [
                'indicator_id' => 11, // Volunteer Satisfaction
                'survey_question_id' => 1,
                'phase' => 'before'
            ],
            [
                'indicator_id' => 11,
                'survey_question_id' => 2,
                'phase' => 'before'
            ],
            [
                'indicator_id' => 11,
                'survey_question_id' => 3,
                'phase' => 'before'
            ],

            // 🟡 Team Coordination Quality
            [
                'indicator_id' => 12,
                'survey_question_id' => 5,
                'phase' => 'during'
            ],
            [
                'indicator_id' => 12,
                'survey_question_id' => 6,
                'phase' => 'during'
            ],
            [
                'indicator_id' => 12,
                'survey_question_id' => 7,
                'phase' => 'during'
            ],

            // 🔵 Campaign Experience Quality
            [
                'indicator_id' => 13,
                'survey_question_id' => 10,
                'phase' => 'after'
            ],
            [
                'indicator_id' => 13,
                'survey_question_id' => 11,
                'phase' => 'after'
            ],
            [
                'indicator_id' => 13,
                'survey_question_id' => 12,
                'phase' => 'after'
            ],

        ]);
    }
}
