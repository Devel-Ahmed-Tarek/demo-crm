# دمج الإرسال المجدول للواتساب مع الـ CRM

## الفكرة

إرسال رسائل واتساب تلقائية في **مواعيد معيّنة**، مثلاً:
- تذكير قبل الموعد (Appointment) بـ 24 ساعة أو يوم.
- تذكير للعميل/الليد في تاريخ معيّن.
- إشعارات دورية (مثلاً كل جمعة: رسالة لليدز في مرحلة معينة).

---

## طرق الدمج المقترحة

### ١) ربط مباشر بالمواعيد (Appointments)

- **الفكرة:** قبل كل موعد (مثلاً 24 ساعة) يتبعت رسالة واتساب تلقائية للعميل/الليد المرتبط بالموعد.
- **التنفيذ:**
  - جدول `appointments` موجود فعلاً ومرتبط بعملاء/ليدز.
  - نضيف حقل اختياري في الإعدادات أو في جدول الإعدادات: `whatsapp_reminder_hours` (مثلاً 24).
  - **Laravel Scheduler:** مرة كل ساعة (أو يومياً) نشغّل Command يبحث عن المواعيد اللي بعدها خلال `whatsapp_reminder_hours` ساعة ولم يُرسل لها تذكير بعد → يرسل رسالة عبر WhatsBridge ويحفظ أن التذكير اتبعت (حقل في `appointments` مثل `reminder_sent_at` أو جدول صغير `appointment_reminders`).

**مميزات:** مرتبط مباشرة بمواعيدك، بدون واجهة معقدة.  
**عيب:** فقط تذكير مواعيد، مش رسائل في تاريخ حر.

---

### ٢) رسائل مجدولة حرة (تاريخ + وقت + مستقبلين + نص)

- **الفكرة:** المستخدم يحدد "في تاريخ كذا، الساعة كذا، ابعت هذه الرسالة لهؤلاء العملاء/الليدز (أو مرحلة/مصدر معين)".
- **التنفيذ:**
  - جدول جديد مثل: `scheduled_whatsapp_messages`  
    (مثلاً: `send_at`, `message`, `recipient_type` [lead/customer], `recipient_ids` أو مرحلة/مصدر، `status` [pending/sent/failed], `sent_at`).
  - صفحة في الـ CRM: "رسائل واتساب مجدولة" → إضافة رسالة جديدة (تاريخ، وقت، اختيار مستقبلين من ليدز/عملاء أو فلتر مرحلة/مصدر، نص الرسالة).
  - **Laravel Scheduler:** كل دقيقة أو كل 5 دقائق نشغّل Command يبحث عن `scheduled_whatsapp_messages` اللي `send_at <= now()` و status = pending → يرسل عبر نفس خدمة WhatsBridge اللي عندك ويحدّث الحالة.

**مميزات:** مرونة كاملة (أي تاريخ، أي وقت، أي جمهور).  
**عيب:** محتاج واجهة إدارة بسيطة للجدولة.

---

### ٣) خلط الاثنين (مواعيد + رسائل مجدولة حرة)

- تذكير تلقائي للمواعيد (كما في ١).
- بالإضافة إلى جدول رسائل مجدولة حرة (كما في ٢) لمن يريد حملات أو تذكيرات في تواريخ محددة.

---

## التوصية العملية

- **مرحلة أولى:** تنفيذ **(١) تذكير المواعيد** فقط:  
  حقل `reminder_sent_at` (أو جدول تذكيرات)، Command يشتغل كل ساعة، ويرسل لرقم العميل/الليد المرتبط بالموعد باستخدام نفس `WhatsAppServiceController::sendSingleMessage` أو خدمة WhatsBridge الحالية.
- **مرحلة ثانية:** إضافة **(٢) رسائل مجدولة حرة** مع جدول + صفحة إدارة بسيطة + Command للتنفيذ.

---

## متطلبات تقنية مشتركة

- **Laravel Scheduler** يعمل على السيرفر (Cron: `* * * * * php artisan schedule:run`).
- استدعاء نفس الـ API الحالية (WhatsBridge) من الـ Command عبر نفس الإعدادات (`config('services.whatsbridge')`).
- تخزين حالة "تم الإرسال" حتى لا يُعاد إرسال نفس التذكير/الرسالة أكثر من مرة.

---

## ما تم تنفيذه (تذكير المواعيد)

- **Migration:** عمود `whatsapp_reminder_sent_at` في جدول `appointments` لتسجيل أن التذكير أُرسل.
- **الإعداد:** في `config/services.php` تحت `whatsbridge`:
  - `appointment_reminder_hours`: عدد الساعات قبل الموعد (افتراضي 24). يمكن تخصيصه عبر `WHATSAPP_APPOINTMENT_REMINDER_HOURS` في `.env`.
  - `appointment_reminder_message`: نص الرسالة. المتغيرات المتاحة: `:name`، `:date`، `:time`. يمكن تخصيصه عبر `WHATSAPP_APPOINTMENT_REMINDER_MESSAGE` في `.env`.
- **Command:** `php artisan appointments:send-whatsapp-reminders` يبحث عن المواعيد المجدولة خلال الـ X ساعة القادمة والتي لم يُرسل لها تذكير، ويرسل واتساب لرقم العميل (من `customer.phone`) ثم يحدّث `whatsapp_reminder_sent_at`.
- **الجدولة:** الـ Command مُسجّل في `routes/console.php` ليعمل **كل ساعة** (توقيت Africa/Cairo).
- **الاعتماد:** خدمة `App\Services\WhatsBridgeService` للإرسال؛ تعتمد على نفس إعدادات WhatsBridge (Base URL + API Key).

لتشغيل التذكيرات يدوياً مرة واحدة:
```bash
php artisan appointments:send-whatsapp-reminders
```

### كيفية تجربة التذكير

1. **إنشاء موعد تجريبي** (خلال ساعة من الآن حتى يقع ضمن نافذة الـ 24 ساعة):
   ```bash
   php artisan appointments:create-test-reminder
   ```
   - لاستخدام رقم واتساب معيّن (عميل جديد أو موجود):
     ```bash
   php artisan appointments:create-test-reminder --phone=201234567890
   ```
   - لتأجيل الموعد لـ 2 ساعة من الآن:
     ```bash
   php artisan appointments:create-test-reminder --hours=2
   ```

2. **إرسال التذكيرات يدوياً** (يرسل واتساب للعميل المرتبط بالموعد):
   ```bash
   php artisan appointments:send-whatsapp-reminders
   ```

3. تأكد من وجود `WHATSBRIDGE_BASE_URL` و `WHATSBRIDGE_API_KEY` في `.env` وأن العميل له رقم هاتف صالح.
