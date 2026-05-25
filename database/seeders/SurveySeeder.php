<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SurveySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('surveys')->insert([
            [
                'campaign_id' => 1,
                'title' => 'Pre Campaign Survey',
                'stage' => 'before',
                'status' => 'active',
            ],
            [
                'campaign_id' => 1,
                'title' => 'During Campaign Survey',
                'stage' => 'during',
                'status' => 'active',
            ],
            [
                'campaign_id' => 1,
                'title' => 'Post Campaign Survey',
                'stage' => 'after',
                'status' => 'active',
            ],
        ]);
}}
