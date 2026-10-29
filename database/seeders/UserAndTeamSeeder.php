<?php

namespace Database\Seeders;

use App\Models\SupervisorTeam;
use App\Models\Team;
use App\Models\User;
use App\Models\UserShift;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserAndTeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultPassword = Hash::make('password123');

        // 1. Admin
        $admin = User::updateOrCreate(
            ['email' => 'admin@pedago.com'],
            [
                'office_id' => 'EMP-0001',
                'name' => 'System Administrator',
                'password' => $defaultPassword,
                'phone' => '01700000001',
                'role' => 'Admin',
                'status' => 'Active',
                'joining_date' => '2024-01-01',
            ]
        );

        // 2. Supervisor
        $supervisor = User::updateOrCreate(
            ['email' => 'supervisor@pedago.com'],
            [
                'office_id' => 'EMP-0010',
                'name' => 'Dr. Rahman (Sales Supervisor)',
                'password' => $defaultPassword,
                'phone' => '01700000010',
                'role' => 'Supervisor',
                'status' => 'Active',
                'joining_date' => '2024-03-01',
            ]
        );

        // 3. Team Leaders
        $tlAlpha = User::updateOrCreate(
            ['email' => 'tl.alpha@pedago.com'],
            [
                'office_id' => 'EMP-0101',
                'name' => 'Sarah Khan (TL Alpha)',
                'password' => $defaultPassword,
                'phone' => '01700000101',
                'role' => 'Team Leader',
                'status' => 'Active',
                'joining_date' => '2024-06-01',
            ]
        );

        $tlTitan = User::updateOrCreate(
            ['email' => 'tl.titan@pedago.com'],
            [
                'office_id' => 'EMP-0102',
                'name' => 'Mahmud Hasan (TL Titan)',
                'password' => $defaultPassword,
                'phone' => '01700000102',
                'role' => 'Team Leader',
                'status' => 'Active',
                'joining_date' => '2024-06-15',
            ]
        );

        // 4. Teams
        $teamAlpha = Team::updateOrCreate(
            ['name' => 'Alpha Squad'],
            [
                'team_leader_id' => $tlAlpha->id,
                'is_active' => true,
            ]
        );

        $teamTitan = Team::updateOrCreate(
            ['name' => 'Titan Squad'],
            [
                'team_leader_id' => $tlTitan->id,
                'is_active' => true,
            ]
        );

        // 5. Supervisor Supervision Mapping (1:N)
        SupervisorTeam::firstOrCreate([
            'supervisor_id' => $supervisor->id,
            'team_id' => $teamAlpha->id,
        ]);

        SupervisorTeam::firstOrCreate([
            'supervisor_id' => $supervisor->id,
            'team_id' => $teamTitan->id,
        ]);

        // 6. Sales Executives with Shift Schedules
        $executives = [
            [
                'office_id' => 'EMP-0201',
                'name' => 'Arafat Hossain',
                'email' => 'arafat@pedago.com',
                'phone' => '01800000201',
                'role' => 'Sales Executive',
                'team_id' => $teamAlpha->id,
                'shift_start' => '10:00:00',
                'shift_end' => '18:00:00',
            ],
            [
                'office_id' => 'EMP-0202',
                'name' => 'Nusrat Jahan',
                'email' => 'nusrat@pedago.com',
                'phone' => '01800000202',
                'role' => 'Sales Executive',
                'team_id' => $teamAlpha->id,
                'shift_start' => '10:00:00',
                'shift_end' => '18:00:00',
            ],
            [
                'office_id' => 'EMP-0203',
                'name' => 'Tanvir Ahmed',
                'email' => 'tanvir@pedago.com',
                'phone' => '01800000203',
                'role' => 'Sales Executive',
                'team_id' => $teamAlpha->id,
                'shift_start' => '12:00:00',
                'shift_end' => '20:00:00',
            ],
            [
                'office_id' => 'EMP-0204',
                'name' => 'Farhana Akter',
                'email' => 'farhana@pedago.com',
                'phone' => '01800000204',
                'role' => 'Sales Executive',
                'team_id' => $teamTitan->id,
                'shift_start' => '10:00:00',
                'shift_end' => '18:00:00',
            ],
            [
                'office_id' => 'EMP-0205',
                'name' => 'Zubair Hossain',
                'email' => 'zubair@pedago.com',
                'phone' => '01800000205',
                'role' => 'Sales Executive',
                'team_id' => $teamTitan->id,
                'shift_start' => '14:00:00',
                'shift_end' => '22:00:00',
            ],
        ];

        foreach ($executives as $execData) {
            $user = User::updateOrCreate(
                ['email' => $execData['email']],
                [
                    'office_id' => $execData['office_id'],
                    'name' => $execData['name'],
                    'password' => $defaultPassword,
                    'phone' => $execData['phone'],
                    'role' => 'Sales Executive',
                    'status' => 'Active',
                    'joining_date' => '2025-01-10',
                ]
            );

            // Create individual shift roster
            UserShift::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'shift_start_time' => $execData['shift_start'],
                    'shift_end_time' => $execData['shift_end'],
                    'is_on_duty_today' => true,
                    'availability_status' => 'Available',
                ]
            );
        }

        // 7. Accounts / Finance Officer
        User::updateOrCreate(
            ['email' => 'accounts@pedago.com'],
            [
                'office_id' => 'EMP-0901',
                'name' => 'Fahim Morshed (Accounts Officer)',
                'password' => $defaultPassword,
                'phone' => '01900000901',
                'role' => 'Accounts',
                'status' => 'Active',
                'joining_date' => '2024-02-01',
            ]
        );
    }
}
