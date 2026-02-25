# نظام الإشعارات (Notifications System)

## نظرة عامة

نظام الإشعارات في CRM يسمح بإرسال إشعارات تلقائية للمستخدمين عند حدوث أحداث معينة مثل:
- فوات موعد (Appointment Missed)
- تذكير قبل الموعد بـ 15 دقيقة (Appointment Reminder)
- تعيين عميل محتمل جديد (Lead Assigned)

## المكونات الرئيسية

### 1. Notification Classes

جميع الـ Notification classes موجودة في `app/Notifications/`:

#### AppointmentMissed
إشعار يتم إرساله عند فوات موعد.

**الاستخدام:**
```php
use App\Notifications\AppointmentMissed;

$user->notify(new AppointmentMissed($appointment));
```

**البيانات المرسلة:**
- `type`: `appointment_missed`
- `appointment_id`: معرف الموعد
- `message`: رسالة الإشعار
- `appointment_date`: تاريخ ووقت الموعد
- `customer_name`: اسم العميل
- `unit_code`: رمز الوحدة (إن وجد)
- `url`: رابط صفحة المواعيد

#### AppointmentReminder
إشعار يتم إرساله قبل 15 دقيقة من الموعد.

**الاستخدام:**
```php
use App\Notifications\AppointmentReminder;

$user->notify(new AppointmentReminder($appointment));
```

**البيانات المرسلة:**
- `type`: `appointment_reminder`
- `appointment_id`: معرف الموعد
- `message`: رسالة الإشعار
- `appointment_date`: تاريخ ووقت الموعد
- `customer_name`: اسم العميل
- `unit_code`: رمز الوحدة (إن وجد)
- `url`: رابط صفحة المواعيد

#### LeadAssigned
إشعار يتم إرساله عند تعيين عميل محتمل جديد.

**الاستخدام:**
```php
use App\Notifications\LeadAssigned;

$user->notify(new LeadAssigned($lead, $assignedBy));
```

**البيانات المرسلة:**
- `type`: `lead_assigned`
- `lead_id`: معرف العميل المحتمل
- `message`: رسالة الإشعار
- `lead_name`: اسم العميل المحتمل
- `assigned_by`: اسم الشخص الذي قام بالتعيين
- `url`: رابط صفحة تفاصيل العميل المحتمل

### 2. Command للتحقق من الأحداث

**الملف:** `app/Console/Commands/CheckAppointmentsNotifications.php`

**الوصف:** يقوم هذا الـ Command بالتحقق من المواعيد كل 15 دقيقة وإرسال الإشعارات المناسبة.

**التشغيل اليدوي:**
```bash
php artisan appointments:check-notifications
```

**التشغيل التلقائي:**
تم إضافة الـ Command إلى `routes/console.php` ليعمل تلقائياً كل 15 دقيقة:
```php
Schedule::command('appointments:check-notifications')->everyFifteenMinutes();
```

**لتفعيل الـ Scheduler:**
1. في بيئة التطوير:
```bash
php artisan schedule:work
```

2. في بيئة الإنتاج (إضافة cron job):
```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

### 3. NotificationController

**الملف:** `app/Http/Controllers/NotificationController.php`

**الـ Routes:**
- `GET /notifications` - عرض جميع الإشعارات
- `GET /api/notifications/unread-count` - عدد الإشعارات غير المقروءة
- `GET /api/notifications/recent` - آخر 10 إشعارات غير مقروءة
- `POST /api/notifications/{id}/read` - تحديد إشعار كمقروء
- `POST /api/notifications/mark-all-read` - تحديد جميع الإشعارات كمقروءة

### 4. واجهة المستخدم

#### زر الإشعارات
يوجد زر الإشعارات في:
- الـ Mobile Header (الهاتف)
- الـ Desktop Sidebar (سطح المكتب)

#### Dropdown الإشعارات
عند الضغط على زر الإشعارات، يظهر dropdown يحتوي على:
- آخر 10 إشعارات غير مقروءة
- زر "Mark all as read"
- رابط "View all notifications"

#### Badge العداد
يظهر badge أحمر على زر الإشعارات يعرض عدد الإشعارات غير المقروءة.

#### صفحة الإشعارات
صفحة كاملة لعرض جميع الإشعارات في `/notifications`.

## كيفية إضافة إشعار جديد

### الخطوة 1: إنشاء Notification Class

```bash
php artisan make:notification YourNotificationName
```

### الخطوة 2: تحديث الـ Notification Class

```php
<?php

namespace App\Notifications;

use App\Models\YourModel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class YourNotificationName extends Notification
{
    use Queueable;

    protected $model;

    public function __construct(YourModel $model)
    {
        $this->model = $model;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'your_notification_type',
            'model_id' => $this->model->id,
            'message' => __('Your notification message'),
            'url' => route('your.route'),
        ];
    }
}
```

### الخطوة 3: إرسال الإشعار

```php
use App\Notifications\YourNotificationName;

$user->notify(new YourNotificationName($model));
```

### الخطوة 4: إضافة الترجمات

في `resources/lang/en.json`:
```json
{
    "Your notification message": "Your notification message"
}
```

في `resources/lang/ar.json`:
```json
{
    "Your notification message": "رسالة الإشعار الخاصة بك"
}
```

## كيفية عمل النظام

### 1. إشعارات المواعيد

#### إشعار فوات الموعد
- يتم التحقق من المواعيد التي فاتت (في آخر ساعة)
- يتم إرسال إشعار إلى:
  - Sales Agent (صاحب الموعد)
  - Admin (جميع الأدمن)
  - Team Leader (قائد الفريق إذا كان Sales Agent)

#### إشعار تذكير الموعد
- يتم التحقق من المواعيد القادمة خلال 15 دقيقة
- يتم إرسال إشعار إلى نفس الأشخاص المذكورين أعلاه

### 2. إشعارات تعيين Leads

عند إنشاء أو تحديث Lead:
- إذا تم تعيين Lead لشخص معين
- يتم إرسال إشعار للشخص المعين
- يتم تضمين اسم الشخص الذي قام بالتعيين

## قاعدة البيانات

الإشعارات تُخزن في جدول `notifications` الذي يأتي مع Laravel:

```sql
CREATE TABLE notifications (
    id CHAR(36) PRIMARY KEY,
    type VARCHAR(255) NOT NULL,
    notifiable_type VARCHAR(255) NOT NULL,
    notifiable_id BIGINT UNSIGNED NOT NULL,
    data TEXT NOT NULL,
    read_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX (notifiable_type, notifiable_id)
);
```

## API Endpoints

### الحصول على عدد الإشعارات غير المقروءة

```javascript
fetch('/api/notifications/unread-count')
    .then(response => response.json())
    .then(data => {
        console.log(data.count); // عدد الإشعارات غير المقروءة
    });
```

### الحصول على آخر الإشعارات

```javascript
fetch('/api/notifications/recent')
    .then(response => response.json())
    .then(data => {
        console.log(data.notifications); // مصفوفة الإشعارات
    });
```

### تحديد إشعار كمقروء

```javascript
fetch(`/api/notifications/${notificationId}/read`, {
    method: 'POST',
    headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'Content-Type': 'application/json'
    }
})
    .then(response => response.json())
    .then(data => {
        console.log(data.success);
    });
```

### تحديد جميع الإشعارات كمقروءة

```javascript
fetch('/api/notifications/mark-all-read', {
    method: 'POST',
    headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'Content-Type': 'application/json'
    }
})
    .then(response => response.json())
    .then(data => {
        console.log(data.success);
    });
```

## JavaScript Functions

### تحديث عدد الإشعارات

```javascript
function updateNotificationCount() {
    fetch('/api/notifications/unread-count')
        .then(response => response.json())
        .then(data => {
            const badge = document.getElementById('notification-badge');
            if (data.count > 0) {
                badge.textContent = data.count > 99 ? '99+' : data.count;
                badge.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
            }
        });
}
```

### تحميل الإشعارات

```javascript
function loadNotifications(listId = 'notifications-list') {
    fetch('/api/notifications/recent')
        .then(response => response.json())
        .then(data => {
            const list = document.getElementById(listId);
            // عرض الإشعارات في القائمة
        });
}
```

## التخصيص

### تغيير فترة التذكير

لتغيير فترة التذكير من 15 دقيقة إلى فترة أخرى، قم بتعديل:

1. في `CheckAppointmentsNotifications.php`:
```php
$fifteenMinutesFromNow = $now->copy()->addMinutes(30); // 30 دقيقة بدلاً من 15
```

2. في `routes/console.php`:
```php
Schedule::command('appointments:check-notifications')->everyThirtyMinutes();
```

### إضافة قنوات إضافية

لإضافة قنوات إضافية مثل البريد الإلكتروني:

```php
public function via(object $notifiable): array
{
    return ['database', 'mail'];
}
```

ثم إضافة method `toMail`:
```php
public function toMail(object $notifiable): MailMessage
{
    return (new MailMessage)
        ->subject('Notification Subject')
        ->line('Notification message');
}
```

## استكشاف الأخطاء

### الإشعارات لا تظهر

1. تأكد من أن الـ Scheduler يعمل:
```bash
php artisan schedule:work
```

2. تحقق من الـ Command يدوياً:
```bash
php artisan appointments:check-notifications
```

3. تحقق من وجود المواعيد في قاعدة البيانات:
```sql
SELECT * FROM appointments WHERE status = 'scheduled';
```

### Badge لا يتحدث

1. تأكد من أن JavaScript يعمل بشكل صحيح
2. تحقق من الـ Console للأخطاء
3. تأكد من أن الـ route موجود في `routes/web.php`

### الإشعارات لا تُرسل

1. تحقق من أن المستخدم موجود وله علاقة بالموعد
2. تحقق من أن الـ Notification class موجود وصحيح
3. تحقق من الـ logs:
```bash
tail -f storage/logs/laravel.log
```

## أمثلة الاستخدام

### إرسال إشعار مخصص

```php
use Illuminate\Support\Facades\Notification;
use App\Models\User;
use App\Notifications\YourNotification;

// إرسال لشخص واحد
$user = User::find(1);
$user->notify(new YourNotification($data));

// إرسال لعدة أشخاص
$users = User::whereIn('id', [1, 2, 3])->get();
Notification::send($users, new YourNotification($data));
```

### إرسال إشعار عند حدث معين

```php
// في Controller
public function store(Request $request)
{
    $model = Model::create($request->all());
    
    if ($model->assigned_to) {
        $assignedUser = User::find($model->assigned_to);
        $assignedUser->notify(new ModelAssigned($model, auth()->user()));
    }
    
    return redirect()->back();
}
```

## الملفات المرتبطة

- `app/Notifications/AppointmentMissed.php`
- `app/Notifications/AppointmentReminder.php`
- `app/Notifications/LeadAssigned.php`
- `app/Console/Commands/CheckAppointmentsNotifications.php`
- `app/Http/Controllers/NotificationController.php`
- `routes/web.php` (routes للإشعارات)
- `routes/console.php` (scheduling)
- `resources/views/layouts/app.blade.php` (UI للإشعارات)
- `resources/views/notifications/index.blade.php` (صفحة الإشعارات)
- `resources/lang/en.json` (ترجمات إنجليزية)
- `resources/lang/ar.json` (ترجمات عربية)

## الدعم

للمزيد من المعلومات، راجع:
- [Laravel Notifications Documentation](https://laravel.com/docs/notifications)
- [Laravel Scheduling Documentation](https://laravel.com/docs/scheduling)


## نظرة عامة

نظام الإشعارات في CRM يسمح بإرسال إشعارات تلقائية للمستخدمين عند حدوث أحداث معينة مثل:
- فوات موعد (Appointment Missed)
- تذكير قبل الموعد بـ 15 دقيقة (Appointment Reminder)
- تعيين عميل محتمل جديد (Lead Assigned)

## المكونات الرئيسية

### 1. Notification Classes

جميع الـ Notification classes موجودة في `app/Notifications/`:

#### AppointmentMissed
إشعار يتم إرساله عند فوات موعد.

**الاستخدام:**
```php
use App\Notifications\AppointmentMissed;

$user->notify(new AppointmentMissed($appointment));
```

**البيانات المرسلة:**
- `type`: `appointment_missed`
- `appointment_id`: معرف الموعد
- `message`: رسالة الإشعار
- `appointment_date`: تاريخ ووقت الموعد
- `customer_name`: اسم العميل
- `unit_code`: رمز الوحدة (إن وجد)
- `url`: رابط صفحة المواعيد

#### AppointmentReminder
إشعار يتم إرساله قبل 15 دقيقة من الموعد.

**الاستخدام:**
```php
use App\Notifications\AppointmentReminder;

$user->notify(new AppointmentReminder($appointment));
```

**البيانات المرسلة:**
- `type`: `appointment_reminder`
- `appointment_id`: معرف الموعد
- `message`: رسالة الإشعار
- `appointment_date`: تاريخ ووقت الموعد
- `customer_name`: اسم العميل
- `unit_code`: رمز الوحدة (إن وجد)
- `url`: رابط صفحة المواعيد

#### LeadAssigned
إشعار يتم إرساله عند تعيين عميل محتمل جديد.

**الاستخدام:**
```php
use App\Notifications\LeadAssigned;

$user->notify(new LeadAssigned($lead, $assignedBy));
```

**البيانات المرسلة:**
- `type`: `lead_assigned`
- `lead_id`: معرف العميل المحتمل
- `message`: رسالة الإشعار
- `lead_name`: اسم العميل المحتمل
- `assigned_by`: اسم الشخص الذي قام بالتعيين
- `url`: رابط صفحة تفاصيل العميل المحتمل

### 2. Command للتحقق من الأحداث

**الملف:** `app/Console/Commands/CheckAppointmentsNotifications.php`

**الوصف:** يقوم هذا الـ Command بالتحقق من المواعيد كل 15 دقيقة وإرسال الإشعارات المناسبة.

**التشغيل اليدوي:**
```bash
php artisan appointments:check-notifications
```

**التشغيل التلقائي:**
تم إضافة الـ Command إلى `routes/console.php` ليعمل تلقائياً كل 15 دقيقة:
```php
Schedule::command('appointments:check-notifications')->everyFifteenMinutes();
```

**لتفعيل الـ Scheduler:**
1. في بيئة التطوير:
```bash
php artisan schedule:work
```

2. في بيئة الإنتاج (إضافة cron job):
```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

### 3. NotificationController

**الملف:** `app/Http/Controllers/NotificationController.php`

**الـ Routes:**
- `GET /notifications` - عرض جميع الإشعارات
- `GET /api/notifications/unread-count` - عدد الإشعارات غير المقروءة
- `GET /api/notifications/recent` - آخر 10 إشعارات غير مقروءة
- `POST /api/notifications/{id}/read` - تحديد إشعار كمقروء
- `POST /api/notifications/mark-all-read` - تحديد جميع الإشعارات كمقروءة

### 4. واجهة المستخدم

#### زر الإشعارات
يوجد زر الإشعارات في:
- الـ Mobile Header (الهاتف)
- الـ Desktop Sidebar (سطح المكتب)

#### Dropdown الإشعارات
عند الضغط على زر الإشعارات، يظهر dropdown يحتوي على:
- آخر 10 إشعارات غير مقروءة
- زر "Mark all as read"
- رابط "View all notifications"

#### Badge العداد
يظهر badge أحمر على زر الإشعارات يعرض عدد الإشعارات غير المقروءة.

#### صفحة الإشعارات
صفحة كاملة لعرض جميع الإشعارات في `/notifications`.

## كيفية إضافة إشعار جديد

### الخطوة 1: إنشاء Notification Class

```bash
php artisan make:notification YourNotificationName
```

### الخطوة 2: تحديث الـ Notification Class

```php
<?php

namespace App\Notifications;

use App\Models\YourModel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class YourNotificationName extends Notification
{
    use Queueable;

    protected $model;

    public function __construct(YourModel $model)
    {
        $this->model = $model;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'your_notification_type',
            'model_id' => $this->model->id,
            'message' => __('Your notification message'),
            'url' => route('your.route'),
        ];
    }
}
```

### الخطوة 3: إرسال الإشعار

```php
use App\Notifications\YourNotificationName;

$user->notify(new YourNotificationName($model));
```

### الخطوة 4: إضافة الترجمات

في `resources/lang/en.json`:
```json
{
    "Your notification message": "Your notification message"
}
```

في `resources/lang/ar.json`:
```json
{
    "Your notification message": "رسالة الإشعار الخاصة بك"
}
```

## كيفية عمل النظام

### 1. إشعارات المواعيد

#### إشعار فوات الموعد
- يتم التحقق من المواعيد التي فاتت (في آخر ساعة)
- يتم إرسال إشعار إلى:
  - Sales Agent (صاحب الموعد)
  - Admin (جميع الأدمن)
  - Team Leader (قائد الفريق إذا كان Sales Agent)

#### إشعار تذكير الموعد
- يتم التحقق من المواعيد القادمة خلال 15 دقيقة
- يتم إرسال إشعار إلى نفس الأشخاص المذكورين أعلاه

### 2. إشعارات تعيين Leads

عند إنشاء أو تحديث Lead:
- إذا تم تعيين Lead لشخص معين
- يتم إرسال إشعار للشخص المعين
- يتم تضمين اسم الشخص الذي قام بالتعيين

## قاعدة البيانات

الإشعارات تُخزن في جدول `notifications` الذي يأتي مع Laravel:

```sql
CREATE TABLE notifications (
    id CHAR(36) PRIMARY KEY,
    type VARCHAR(255) NOT NULL,
    notifiable_type VARCHAR(255) NOT NULL,
    notifiable_id BIGINT UNSIGNED NOT NULL,
    data TEXT NOT NULL,
    read_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX (notifiable_type, notifiable_id)
);
```

## API Endpoints

### الحصول على عدد الإشعارات غير المقروءة

```javascript
fetch('/api/notifications/unread-count')
    .then(response => response.json())
    .then(data => {
        console.log(data.count); // عدد الإشعارات غير المقروءة
    });
```

### الحصول على آخر الإشعارات

```javascript
fetch('/api/notifications/recent')
    .then(response => response.json())
    .then(data => {
        console.log(data.notifications); // مصفوفة الإشعارات
    });
```

### تحديد إشعار كمقروء

```javascript
fetch(`/api/notifications/${notificationId}/read`, {
    method: 'POST',
    headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'Content-Type': 'application/json'
    }
})
    .then(response => response.json())
    .then(data => {
        console.log(data.success);
    });
```

### تحديد جميع الإشعارات كمقروءة

```javascript
fetch('/api/notifications/mark-all-read', {
    method: 'POST',
    headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'Content-Type': 'application/json'
    }
})
    .then(response => response.json())
    .then(data => {
        console.log(data.success);
    });
```

## JavaScript Functions

### تحديث عدد الإشعارات

```javascript
function updateNotificationCount() {
    fetch('/api/notifications/unread-count')
        .then(response => response.json())
        .then(data => {
            const badge = document.getElementById('notification-badge');
            if (data.count > 0) {
                badge.textContent = data.count > 99 ? '99+' : data.count;
                badge.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
            }
        });
}
```

### تحميل الإشعارات

```javascript
function loadNotifications(listId = 'notifications-list') {
    fetch('/api/notifications/recent')
        .then(response => response.json())
        .then(data => {
            const list = document.getElementById(listId);
            // عرض الإشعارات في القائمة
        });
}
```

## التخصيص

### تغيير فترة التذكير

لتغيير فترة التذكير من 15 دقيقة إلى فترة أخرى، قم بتعديل:

1. في `CheckAppointmentsNotifications.php`:
```php
$fifteenMinutesFromNow = $now->copy()->addMinutes(30); // 30 دقيقة بدلاً من 15
```

2. في `routes/console.php`:
```php
Schedule::command('appointments:check-notifications')->everyThirtyMinutes();
```

### إضافة قنوات إضافية

لإضافة قنوات إضافية مثل البريد الإلكتروني:

```php
public function via(object $notifiable): array
{
    return ['database', 'mail'];
}
```

ثم إضافة method `toMail`:
```php
public function toMail(object $notifiable): MailMessage
{
    return (new MailMessage)
        ->subject('Notification Subject')
        ->line('Notification message');
}
```

## استكشاف الأخطاء

### الإشعارات لا تظهر

1. تأكد من أن الـ Scheduler يعمل:
```bash
php artisan schedule:work
```

2. تحقق من الـ Command يدوياً:
```bash
php artisan appointments:check-notifications
```

3. تحقق من وجود المواعيد في قاعدة البيانات:
```sql
SELECT * FROM appointments WHERE status = 'scheduled';
```

### Badge لا يتحدث

1. تأكد من أن JavaScript يعمل بشكل صحيح
2. تحقق من الـ Console للأخطاء
3. تأكد من أن الـ route موجود في `routes/web.php`

### الإشعارات لا تُرسل

1. تحقق من أن المستخدم موجود وله علاقة بالموعد
2. تحقق من أن الـ Notification class موجود وصحيح
3. تحقق من الـ logs:
```bash
tail -f storage/logs/laravel.log
```

## أمثلة الاستخدام

### إرسال إشعار مخصص

```php
use Illuminate\Support\Facades\Notification;
use App\Models\User;
use App\Notifications\YourNotification;

// إرسال لشخص واحد
$user = User::find(1);
$user->notify(new YourNotification($data));

// إرسال لعدة أشخاص
$users = User::whereIn('id', [1, 2, 3])->get();
Notification::send($users, new YourNotification($data));
```

### إرسال إشعار عند حدث معين

```php
// في Controller
public function store(Request $request)
{
    $model = Model::create($request->all());
    
    if ($model->assigned_to) {
        $assignedUser = User::find($model->assigned_to);
        $assignedUser->notify(new ModelAssigned($model, auth()->user()));
    }
    
    return redirect()->back();
}
```

## الدعم

للمزيد من المعلومات، راجع:
- [Laravel Notifications Documentation](https://laravel.com/docs/notifications)
- [Laravel Scheduling Documentation](https://laravel.com/docs/scheduling)

