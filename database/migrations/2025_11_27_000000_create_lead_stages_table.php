<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('lead_stages', function (Blueprint $table): void {
            $table->id();
            $table->string('key')->unique();
            $table->string('label_en');
            $table->string('label_ar')->nullable();
            $table->text('description_en')->nullable();
            $table->text('description_ar')->nullable();
            $table->string('accent')->nullable();
            $table->string('dot')->nullable();
            $table->string('border')->nullable();
            $table->string('card_border')->nullable();
            $table->string('shadow')->nullable();
            $table->string('glow')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        DB::table('lead_stages')->insert([
            [
                'key' => 'new',
                'label_en' => 'New',
                'label_ar' => 'جديد',
                'description_en' => 'Fresh inbound leads waiting for first contact',
                'description_ar' => 'عملاء محتملون جدد في انتظار أول تواصل',
                'accent' => 'from-sky-500 via-blue-500 to-blue-600',
                'dot' => 'bg-sky-400',
                'border' => 'rgba(14, 165, 233, 0.25)',
                'card_border' => 'rgba(59, 130, 246, 0.2)',
                'shadow' => 'rgba(56, 189, 248, 0.25)',
                'glow' => 'rgba(59, 130, 246, 0.12)',
                'sort_order' => 1,
            ],
            [
                'key' => 'contacted',
                'label_en' => 'Contacted',
                'label_ar' => 'تم التواصل',
                'description_en' => 'Initial conversation started with the lead',
                'description_ar' => 'تم بدء التواصل الأول مع العميل المحتمل',
                'accent' => 'from-indigo-500 to-indigo-600',
                'dot' => 'bg-indigo-500',
                'border' => 'rgba(99, 102, 241, 0.3)',
                'card_border' => 'rgba(99, 102, 241, 0.25)',
                'shadow' => 'rgba(129, 140, 248, 0.25)',
                'glow' => 'rgba(99, 102, 241, 0.15)',
                'sort_order' => 2,
            ],
            [
                'key' => 'follow-up',
                'label_en' => 'Follow-up',
                'label_ar' => 'متابعة',
                'description_en' => 'Need reminders and planned conversations',
                'description_ar' => 'بحاجة للتذكير والاتصالات المجدولة',
                'accent' => 'from-purple-500 to-fuchsia-500',
                'dot' => 'bg-purple-500',
                'border' => 'rgba(168, 85, 247, 0.3)',
                'card_border' => 'rgba(192, 132, 252, 0.3)',
                'shadow' => 'rgba(147, 51, 234, 0.25)',
                'glow' => 'rgba(168, 85, 247, 0.15)',
                'sort_order' => 3,
            ],
            [
                'key' => 'proposal',
                'label_en' => 'Proposal',
                'label_ar' => 'عرض',
                'description_en' => 'Sent pricing or proposal, waiting for feedback',
                'description_ar' => 'تم إرسال عرض الأسعار في انتظار رد العميل',
                'accent' => 'from-amber-500 to-orange-500',
                'dot' => 'bg-amber-500',
                'border' => 'rgba(251, 191, 36, 0.35)',
                'card_border' => 'rgba(249, 115, 22, 0.25)',
                'shadow' => 'rgba(251, 191, 36, 0.2)',
                'glow' => 'rgba(251, 191, 36, 0.15)',
                'sort_order' => 4,
            ],
            [
                'key' => 'won',
                'label_en' => 'Won',
                'label_ar' => 'مكتمل',
                'description_en' => 'Successfully converted to customer',
                'description_ar' => 'تم التحويل إلى عميل ناجح',
                'accent' => 'from-emerald-500 to-green-500',
                'dot' => 'bg-emerald-500',
                'border' => 'rgba(16, 185, 129, 0.35)',
                'card_border' => 'rgba(34, 197, 94, 0.3)',
                'shadow' => 'rgba(16, 185, 129, 0.25)',
                'glow' => 'rgba(16, 185, 129, 0.18)',
                'sort_order' => 5,
            ],
            [
                'key' => 'lost',
                'label_en' => 'Lost',
                'label_ar' => 'مفقود',
                'description_en' => 'Closed without conversion',
                'description_ar' => 'تم إغلاق الفرصة دون تحويل',
                'accent' => 'from-zinc-500 to-gray-600',
                'dot' => 'bg-gray-500',
                'border' => 'rgba(156, 163, 175, 0.3)',
                'card_border' => 'rgba(107, 114, 128, 0.25)',
                'shadow' => 'rgba(75, 85, 99, 0.2)',
                'glow' => 'rgba(107, 114, 128, 0.15)',
                'sort_order' => 6,
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_stages');
    }
};

