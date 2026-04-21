<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        $campaignManager = Role::firstOrCreate(['name' => 'Campaign Manager']);
        $campaignEmployee = Role::firstOrCreate(['name' => 'Campaign Employee']);
        $evaluationManager = Role::firstOrCreate(['name' => 'Evaluation Manager']);
        $evaluationOfficer = Role::firstOrCreate(['name' => 'Evaluation Officer']);
        $volunteerManager = Role::firstOrCreate(['name' => 'Volunteer Manager']);
        $teamLeader = Role::firstOrCreate(['name' => 'Team Leader']);
        $volunteer = Role::firstOrCreate(['name' => 'Volunteer']);
        $permissions = [
            'view.user',
            'add.user',
            'edit.user',
            'delete.user',
            'ban.user',
            'show.Employee',
            'view.department',
            'view.department.details',
            'create.department',
            'edit.department',
            'assign.department.manager',

            'view.dashboard.kpi',
            'view.statistics',
            'view.statistics.details',
            'view.growth.metrics',

            'view.campaign',
            'view.campaign.details',
            'create.campaign',
            'edit.campaign',
            'archive.campaign',

            'view.donation',
            'confirm.donation',

            'view.volunteer',
            'view.volunteer.details',
            'view.top.volunteers',
            'promote.volunteer',

            'view.points',
            'view.points.record',
            'add.points',
            'remove.points',

            'view.attendance',
            'record.attendance',
            'record.checkout',
            'view.volunteer.attendance',
            'manage.team.attendance',
            'scan.volunteer.qr',

            'view.join.request',
            'approve.join.request',
            'reject.join.request',
            'join.campaign',

            'view.evaluation.request',
            'create.survey',
            'edit.survey',
            'assign.evaluation.task',
            'submit.evaluation',
            'view.evaluation.result',
            'send.evaluation.report',
            'Showdetail.Employee',

            'view.task',
            'update.task.status',
            'Update.Employee',
            'view.course',
            'enroll.course',

            'view.complaint',
            'create.complaint',
            'edit.complaint',
            'resolve.complaint',

            'view.post',
            'create.post',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web'
            ]);
        }

        $superAdmin->givePermissionTo(Permission::all());

        $campaignManager->givePermissionTo([
            'view.campaign',
            'add.user',
            'show.Employee',
            'Update.Employee',
            'Showdetail.Employee',
            'view.campaign.details',
            'create.campaign',
            'edit.campaign',
            'archive.campaign',
            'view.volunteer',
            'view.volunteer.details',
            'view.top.volunteers',
            'promote.volunteer',
            'view.attendance',
            'view.donation',
            'confirm.donation',
            'view.points.record',
            'add.points',
            'remove.points',
            'view.join.request',
            'approve.join.request',
            'reject.join.request',
            'view.complaint',
            'resolve.complaint',
        ]);

        $campaignEmployee->givePermissionTo([
            'view.campaign',
            'show.Employee',

            'view.campaign.details',
            'view.attendance',
            'view.donation',
            'view.volunteer',
            'view.volunteer.details',
            'view.top.volunteers',
            'view.complaint',
            'create.complaint',
            'view.join.request',
        ]);

        $evaluationManager->givePermissionTo([
            'view.evaluation.request',
            'show.Employee',
            'Update.Employee',
            'Showdetail.Employee',
            'create.survey',
            'edit.survey',
            'add.user',
            'assign.evaluation.task',
            'view.evaluation.result',
            'send.evaluation.report',
            'view.task',
            'update.task.status',
            'view.complaint',
            'resolve.complaint',
        ]);

        $evaluationOfficer->givePermissionTo([
            'view.task',
            'show.Employee',

            'update.task.status',
            'submit.evaluation',
            'view.evaluation.request',
            'view.complaint',
            'create.complaint',
        ]);

        $volunteerManager->givePermissionTo([
            'view.volunteer',
            'show.Employee',
            'view.volunteer.details',
            'view.top.volunteers',
            'promote.volunteer',
            'view.points.record',
            'add.points',
            'remove.points',
            'view.join.request',
            'approve.join.request',
            'reject.join.request',
            'view.campaign',
            'view.attendance',
        ]);

        $volunteer->givePermissionTo([
            'join.campaign',
            'view.course',
            'enroll.course',
            'view.post',
            'create.post',
            'view.complaint',
            'create.complaint',
            'view.attendance',
            'view.points',
        ]);

        $teamLeader->givePermissionTo([
            'record.attendance',
            'record.checkout',
            'scan.volunteer.qr',
            'view.volunteer.attendance',
            'manage.team.attendance',
            'view.campaign',
            'view.attendance',
            'view.post',
            'create.post',
            'view.complaint',
            'create.complaint',
            'join.campaign',
        ]);
        $adminUser = User::firstOrCreate(
            [
                'email' => 'RahafAlghalini1234@gmail.com',
            ],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('12345678'),
                'email_verified_at' => now(),
            ]
        );
        $adminUser->syncRoles('Super Admin');
        $ManagerCampanigUser = User::firstOrCreate([
            'email' => 'lujenchaban1234@gmail.com'
        ], [
            'name' => 'ManagerCampaignSeeder',
            'password' => Hash::make('12345678')
        ]);

        $ManagerCampanigUser->syncRoles(['Campaign Manager']);
    }
}
