<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class indicatorsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('indicators')->insert([

            // =====================================================
            // 🟦 VOLUNTEERS DOMAIN
            // =====================================================

            [
                'name' => 'Total Volunteers',
                'domain' => 'volunteers',
                'type' => 'numeric',
                'data_source' => 'database',
                'calculation_type' => 'count',
                'table_name' => 'users',
                'column_name' => 'id',
                'operation' => 'count',
                'priority' => 10,
            ],

            [
                'name' => 'Active Volunteers',
                'domain' => 'volunteers',
                'type' => 'numeric',
                'data_source' => 'database',
                'calculation_type' => 'count',
                'table_name' => 'volunteer_profiles',
                'column_name' => 'is_active',
                'operation' => 'filter_true',
                'priority' => 10,
            ],

            [
                'name' => 'Volunteer Attendance Rate',
                'domain' => 'volunteers',
                'type' => 'numeric',
                'data_source' => 'database',
                'calculation_type' => 'percentage',
                'table_name' => 'attendances',
                'operation' => 'attendance_ratio',
                'priority' => 10,
            ],

            [
                'name' => 'Volunteer Retention',
                'domain' => 'volunteers',
                'type' => 'numeric',
                'data_source' => 'database',
                'calculation_type' => 'percentage',
                'table_name' => 'attendances',
                'operation' => 'returning_volunteers',
                'priority' => 9,
            ],

            // =====================================================
            // 🟦 CAMPAIGNS DOMAIN
            // =====================================================

            [
                'name' => 'Campaign Completion Rate',
                'domain' => 'campaigns',
                'type' => 'numeric',
                'data_source' => 'database',
                'calculation_type' => 'percentage',
                'table_name' => 'campaigns',
                'operation' => 'completed_ratio',
                'priority' => 10,
            ],

            [
                'name' => 'Campaign Participation Rate',
                'domain' => 'campaigns',
                'type' => 'numeric',
                'data_source' => 'database',
                'calculation_type' => 'percentage',
                'table_name' => 'attendances',
                'operation' => 'participation_ratio',
                'priority' => 10,
            ],

            [
                'name' => 'Campaign Execution Efficiency',
                'domain' => 'campaigns',
                'type' => 'numeric',
                'data_source' => 'database',
                'calculation_type' => 'percentage',
                'table_name' => 'campaigns',
                'operation' => 'efficiency_score',
                'priority' => 10,
            ],

            // =====================================================
            // 🟦 ATTENDANCE DOMAIN
            // =====================================================

            [
                'name' => 'Total Attendance Hours',
                'domain' => 'attendance',
                'type' => 'numeric',
                'data_source' => 'database',
                'calculation_type' => 'sum',
                'table_name' => 'attendances',
                'column_name' => 'hours',
                'operation' => 'sum',
                'priority' => 10,
            ],

            [
                'name' => 'Average Attendance Hours',
                'domain' => 'attendance',
                'type' => 'numeric',
                'data_source' => 'database',
                'calculation_type' => 'avg',
                'table_name' => 'attendances',
                'column_name' => 'hours',
                'operation' => 'avg',
                'priority' => 8,
            ],

            // =====================================================
            // 🟦 POINTS SYSTEM
            // =====================================================

            [
                'name' => 'Total Points Distributed',
                'domain' => 'points',
                'type' => 'numeric',
                'data_source' => 'database',
                'calculation_type' => 'sum',
                'table_name' => 'point_transactions',
                'column_name' => 'points',
                'operation' => 'sum',
                'priority' => 8,
            ],

            // =====================================================
            // 🟨 QUALITATIVE KPIs (SURVEY BASED)
            // =====================================================

            [
                'name' => 'Volunteer Satisfaction',
                'domain' => 'volunteers',
                'type' => 'qualitative',
                'data_source' => 'survey',
                'calculation_type' => 'avg',
                'survey_id' => 1,
                'question_id' => 1,
                'priority' => 10,
            ],

            [
                'name' => 'Team Coordination Quality',
                'domain' => 'campaigns',
                'type' => 'qualitative',
                'data_source' => 'survey',
                'calculation_type' => 'avg',
                'survey_id' => 2,
                'question_id' => 2,
                'priority' => 10,
            ],

            [
                'name' => 'Campaign Experience Quality',
                'domain' => 'campaigns',
                'type' => 'qualitative',
                'data_source' => 'survey',
                'calculation_type' => 'avg',
                'survey_id' => 3,
                'question_id' => 3,
                'priority' => 10,
            ],
        ]);
    }
}
