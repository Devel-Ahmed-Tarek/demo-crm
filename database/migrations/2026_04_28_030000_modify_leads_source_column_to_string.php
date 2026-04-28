<?php

use App\Models\LeadSource;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * العمود كان ENUM بقيم ثابتة؛ مفاتيح lead_sources (مثل whatsapp) غير مقبولة بالضبط وتُكمّ أو تسبب Warning 1265.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE `leads` MODIFY `source` VARCHAR(191) NOT NULL DEFAULT 'website'");

        LeadSource::query()->updateOrCreate(
            ['key' => 'whatsapp'],
            [
                'label_en' => 'WhatsApp',
                'label_ar' => 'واتساب',
                'description_en' => 'Leads from WhatsApp chats and integrations.',
                'description_ar' => 'ليد من واتساب والتكاملات.',
                'is_active' => true,
                'sort_order' => 5,
            ]
        );
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `leads` MODIFY `source` ENUM('facebook', 'website', 'referral', 'other') NOT NULL DEFAULT 'website'");
    }
};
