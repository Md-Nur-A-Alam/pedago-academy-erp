<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use Illuminate\Database\Seeder;

class SystemSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'key' => 'lead_distribution_engine_active',
                'value' => 'true',
                'description' => 'Master toggle to pause or activate the automated lead distribution engine.',
            ],
            [
                'key' => 'default_batch_size_n',
                'value' => '3',
                'description' => 'Default number of leads allocated to a sales executive in a single batch.',
            ],
            [
                'key' => 'shift_cutoff_minutes',
                'value' => '30',
                'description' => 'Minutes before individual employee shift ends when batch assignment halts.',
            ],
            [
                'key' => 'overdue_followup_lazy_threshold_t',
                'value' => '24',
                'description' => 'Hours of unresolved overdue follow-up before agent is marked as Lazy.',
            ],
            [
                'key' => 'transfer_auto_approval_days',
                'value' => '7',
                'description' => 'Days before pending cross-team lead transfers auto-approve.',
            ],
            [
                'key' => 'installment_followup_lead_days',
                'value' => '2',
                'description' => 'Days before installment due date (T-2) to promote to Converted Follow-up queue.',
            ],
        ];

        foreach ($settings as $setting) {
            SystemSetting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
