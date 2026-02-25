<?php

namespace Database\Seeders;

use App\Models\LeadSource;
use Illuminate\Database\Seeder;

class LeadSourcesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sources = [
            [
                'key' => 'facebook',
                'label_en' => 'Facebook',
                'label_ar' => 'فيسبوك',
                'description_en' => 'Generated from Facebook campaigns.',
                'description_ar' => 'قادمة من حملات فيسبوك.',
                'sort_order' => 1,
            ],
            [
                'key' => 'website',
                'label_en' => 'Website',
                'label_ar' => 'الموقع الإلكتروني',
                'description_en' => 'Submitted via website forms.',
                'description_ar' => 'تم إرسالها عبر نماذج الموقع.',
                'sort_order' => 2,
            ],
            [
                'key' => 'referral',
                'label_en' => 'Referral',
                'label_ar' => 'ترشيح',
                'description_en' => 'Referred by existing customers or partners.',
                'description_ar' => 'عن طريق عملاء أو شركاء حاليين.',
                'sort_order' => 3,
            ],
            [
                'key' => 'other',
                'label_en' => 'Other',
                'label_ar' => 'أخرى',
                'description_en' => 'Any custom or ad-hoc source.',
                'description_ar' => 'أي مصدر مخصص أو متنوع.',
                'sort_order' => 4,
            ],
        ];

        foreach ($sources as $index => $source) {
            LeadSource::updateOrCreate(
                ['key' => $source['key']],
                array_merge($source, ['is_active' => true, 'sort_order' => $source['sort_order'] ?? ($index + 1)])
            );
        }
    }
}

