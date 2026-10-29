<?php

namespace Database\Seeders;

use App\Models\LeadSource;
use Illuminate\Database\Seeder;

class LeadSourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sources = [
            [
                'channel' => 'fb',
                'campaign_name' => 'Spring 2026 Meta Lead Adset',
            ],
            [
                'channel' => 'googleads',
                'campaign_name' => 'Google Search Ads - Top EdTech Courses',
            ],
            [
                'channel' => 'wp',
                'campaign_name' => 'WhatsApp Official Hotline Intake',
            ],
            [
                'channel' => 'website',
                'campaign_name' => 'Pedago Academy Organic Landing Page',
            ],
            [
                'channel' => 'googleform',
                'campaign_name' => 'University Career Fair Registration Form',
            ],
            [
                'channel' => 'reference',
                'campaign_name' => 'Alumni Referral Program',
            ],
        ];

        foreach ($sources as $source) {
            LeadSource::firstOrCreate(
                ['channel' => $source['channel'], 'campaign_name' => $source['campaign_name']],
                $source
            );
        }
    }
}
