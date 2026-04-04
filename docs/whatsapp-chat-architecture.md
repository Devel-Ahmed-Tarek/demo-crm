# توثيق شات واتساب — لوحة العميل + الربط مع الميكروسيرفس + REST API

> **ملاحظة عن مستودع demo-crm:** هذا المستند يصف **بنية مرجعية** (لوحة عميل + Inertia/Vue). في **هذا المشروع** يوجد الآن **مسار موازٍ** مطابق لخطوات التوثيق: **`/customer/messages`** (Blade + `WhatsAppService` + `MessagesController`) بالإضافة إلى **`/whatsapp/services/*`**. الجلسة من **`WHATSBRIDGE_*`** (أو `session_id` في الطلب). راجع [ملحق demo-crm](#ملحق-demo-crm-الوضع-الحالي-في-هذا-المستودع).

---

## 1) الصورة العامة (Architecture)

```
[متصفح العميل]  ← Inertia (Vue) + طلبات fetch (JSON)
       ↓
[Laravel — routes/customer + MessagesController]
       ↓
[WhatsAppService]  ← HTTP نحو
       ↓
[ميكروسيرفس واتساب]  (عنوانه في config: whatsapp.base_url)
```

- **لوحة الرسائل** (`/customer/messages`) لا تتصل بالميكروسيرفس مباشرة من المتصفح؛ الاتصال يمر عبر **Laravel** (جلسة مستخدم + تحقق من أن `session_id` يخص نفس المستخدم).
- **REST API** العامة (`/api/whatsapp/...`) تمر أيضاً عبر Laravel ثم نفس `WhatsAppService`، مع تحديد الجلسة عبر **مفتاح API** (`Authorization: Bearer ...`) أو `sessionId` / `api_key` في الطلب.

---

## 2) الجلسة (Session) ومفتاح API

| المفهوم | الشرح |
|--------|--------|
| **session_id** | معرف جلسة واتساب على الميكروسيرفس (يُخزَّن في جدول مفاتيح الـ API مرتبط بالمستخدم بعد ربط الرقم بـ QR). |
| **مفتاح API (Dashboard)** | سلسلة مثل `wb_...` تُنشأ من السيرفر وتُرمّز الجلسة؛ يُستخدم في `Authorization: Bearer` لطلبات `/api/whatsapp/*`. |
| **ربط الرقم** | من صفحة **API Keys** في لوحة العميل؛ بدون جلسة مفعّلة لا تُعرض شاتات فعلية. |

**مهم:** عند الإرسال من لوحة الرسائل، السيرفر يستخدم **`session_id` من قاعدة البيانات** المرتبط بمفتاح المستخدم (نفس الجلسة التي رُبطت بـ QR)، وليس قيمة عشوائية من الواجهة.

### توحيد الـ REST API مع لوحة الرسائل (نفس السلوك)

لكي يقل التباين بين **واجهة الشات** و**الـ API** وتقل الأخطاء عند الربط الخارجي:

| السلوك | التفاصيل |
|--------|-----------|
| **قائمة الشاتات** | نفس التطبيع (`WhatsAppChatsNormalizer`) في الـ API وفي الصفحة. |
| **رسائل شات** | بعد الجلب من الميكروسيرفس تُرتَّب زمنياً **من الأقدم للأحدث** (`WhatsAppMessagesHelper::sortOldestFirst`) — مثل `poll` و`load-more` في لوحة العميل. |
| **معرّف الجلسة** | في الـ API يُقبل **`sessionId`** أو **`session_id`** (query أو body) بالإضافة إلى `api_key` و`Authorization: Bearer`. |
| **معرّف الشات للرسائل** | يُقبل **`chatId`** أو **`chat_id`**. |
| **المستلم عند الإرسال** | يُقبل **`phoneNumber`** أو **`phone_number`** (نص/جروب/LID — نفس منطق `WhatsAppService`). |
| **إرسال ميديا** | يُقبل **`mimeType`** أو **`mime_type`**. |
| **مجموعات** | يُقبل **`groupId`** أو **`group_id`** حيث ينطبق. |
| **حذف ستوري** | يُقبل **`messageId`** أو **`message_id`**. |

---

## 3) مسارات لوحة العميل (Customer) — الشات

الأساس: `routes/customer.php` تحت البادئة `/customer`.

| الطريقة | المسار (name) | الوظيفة |
|--------|----------------|---------|
| `GET` | `/customer/messages` | `customer.messages` — صفحة الشات (قائمة شاتات + رسائل محددة). |
| `POST` | `/customer/messages/send` | `customer.messages.send` — إرسال نص. |
| `POST` | `/customer/messages/send-media` | `customer.messages.send-media` — إرسال ملف/صورة/صوت/فيديو. |
| `GET` | `/customer/messages/poll` | `customer.messages.poll` — جلب أحدث رسائل الشات (للتحديث التلقائي). |
| `GET` | `/customer/messages/load-more` | `customer.messages.load-more` — تحميل رسائل أقدم (ترقيم صفحات). |
| `GET` | `/customer/messages/media` | `customer.messages.media` — **بروكسي** لعرض/تشغيل ميديا رسالة (صورة/صوت/فيديو). |
| `POST` | `/customer/messages/mark-seen` | `customer.messages.mark-seen` — تعليم الشات كمقروء (إن وُجد دعم في الميكروسيرفس). |

**معاملات الصفحة الرئيسية (query):**

- `session_id` — الجلسة المختارة.
- `chat_id` — معرف المحادثة (مثل `201xxxxxxxxx@c.us` أو `@lid` أو مجموعة `@g.us`).

بدون `session_id` صالح لا تُحمَّل قائمة الشاتات؛ بدون `chat_id` تظهر القائمة فقط دون نافذة رسائل.

---

## 4) تحميل قائمة الشاتات

1. **السيرفر** (`MessagesController@index`) يستدعي الميكروسيرفس: `GET /chats` مع `sessionId`, `limit`, `offset`, `refresh`.
2. الاستجابة تمر عبر **`WhatsAppChatsNormalizer`** لتوحيد الشكل (`id`, `name`, `unreadCount`, `lastMessage`) مهما كان شكل JSON الأصلي.
3. الواجهة تعرض القائمة وتدعم **بحث محلي** (فلترة بالاسم أو المعرف).

---

## 5) تحميل الرسائل (ثلاث طرق)

### أ) التحميل الأول (فتح شات)

- عند اختيار شات، يتم `router.get` إلى `customer.messages` مع `session_id` و `chat_id` (تحميل جزئي Inertia يحدّث `messages`, `chats`, إلخ).
- السيرفر يستدعي `getMessages(sessionId, chatId, limit=50, offset=0, order=desc)` ثم يرتّب زمنياً للعرض (الأقدم → الأحدث في الواجهة).

### ب) التحديث اللحظي (Polling) — «ريال تايم» بسيط

- من الواجهة: كل **~1 ثانية** طلب `GET customer.messages.poll?session_id=...&chat_id=...` مع `credentials: include` (كوكيز الجلسة).
- الرد JSON: `{ messages: [...] }` — أحدث دفعة رسائل؛ الواجهة تدمجها في العرض.

> ليس WebSocket؛ هو **استطلاع دوري**. مناسب لتجنّب تعقيد السوكتات.

### ج) تحميل رسائل أقدم (سكرول لأعلى)

- `GET customer.messages.load-more?session_id=...&chat_id=...&offset=N`
- الرد: `{ messages, hasMore, nextOffset }`
- المنطق: نفس ترتيب الميكروسيرفس `desc` مع زيادة `offset`؛ السيرفر يحدد إن كان يوجد المزيد حسب `total` إن وُجد في الاستجابة.

---

## 6) إرسال رسالة نصية

**من الواجهة:** نموذج POST إلى `customer.messages.send` الحقول:

| الحقل | مطلوب | ملاحظة |
|-------|--------|--------|
| `session_id` | نعم | جلسة المستخدم الحالية |
| `phone_number` | نعم | **المستلم للإرسال** — غالباً `selectedChatPhoneNumber` إن رجع من الـ API، وإلا `chat_id` |
| `chat_id` | اختياري | للإعادة التوجيه بعد الإرسال والحفاظ على السياق |
| `message` | نعم | حتى 4096 حرف |

**الطبقة السفلى (`WhatsAppService::sendMessage`):**

- يرسل إلى الميكروسيرفس `POST /send-message` مع `sessionId`, `phoneNumber`, `message`.
- للأرقام العادية: تُنظَّف الأرقام وتُزال بادئة `18` إن وُجدت.
- لـ **LID** (`...@lid`): يُرسل المعرف كاملاً كما هو بدون تفريغ أرقام.

---

## 7) إرسال ملف / صورة / فيديو / تسجيل صوتي

**من الواجهة:** `POST customer.messages.send-media` — `multipart/form-data`:

| الحقل | ملاحظة |
|-------|--------|
| `session_id`, `phone_number`, `chat_id` | مثل النص |
| `media` | ملف؛ حد أقصى **16MB** (حسب التحقق في الكنترولر) |
| `caption` | اختياري |

**السيرفر:**

1. يحدد النوع من الـ MIME: `image` | `audio` | `video` | `document`.
2. يحوّل الملف إلى **base64** ويستدعي `WhatsAppService::sendMedia` → الميكروسيرفس `POST /send-media`.

**التسجيل الصوتي من المتصفح:**

- تسجيل عبر `MediaRecorder` (مثلاً webm/opus).
- يُرفع كملف صوتي عبر نفس مسار `send-media`.

---

## 8) عرض الملفات والصور والصوت داخل الشات

روابط الميديا من الميكروسيرفس قد تكون داخلية أو تحتاج رؤوس؛ لذلك **لا يُعرض الملف مباشرة من رابط الميكروسيرفس في كل الحالات**.

**الطريقة المعتمدة في الواجهة:**

- بناء رابط:  
  `route('customer.messages.media') + ?session_id=...&message_id=...`
- هذا يستدعي `MessagesController@media` الذي يعمل **بروكسي**:  
  `WhatsAppService::getMessageMedia(sessionId, messageId)` ثم يعيد الجسم مع `Content-Type` و `Content-Disposition: inline`.

**النتيجة:** وسم `<img>`, `<audio>`, `<video>` في Vue يمكن أن تشير إلى هذا المسار (نفس الدومين + كوكي الجلسة)، فيعمل التشغيل/العرض بدون كشف مفتاح الميكروسيرفس للعميل.

---

## 9) حالة الرسالة (Ack / «الصحين»)

في الواجهة تُفسَّر أرقام `ack` (إن وُجدت في كائن الرسالة) تقريباً كالتالي:

| ack | معنى تقريبي |
|-----|-------------|
| 0 | في الانتظار |
| 1 | تم الإرسال |
| 2 | تم التوصيل |
| 3 | تمت المشاهدة |
| 4 | تم الاستماع (صوت) |

يعتمد التفصيل الدقيق على ميكروسيرفس واتساب المستخدم.

---

## 10) تعليم المحادثة كمقروءة (Mark seen)

- إن كان `config('whatsapp.mark_read_supported')` مفعّلاً، يُستدعى بعد فتح الشات (وتأخير بسيط) `POST customer.messages.mark-seen` ثم الميكروسيرفس `POST /chat/seen` مع `sessionId` و `chatId`.

---

## 11) REST API الخارجية (لتطبيقات أخرى)

البادئة: **`/api/whatsapp`** (Laravel الافتراضي: **`/api`**).

**تحديد الجلسة** (أحدها مطلوب):

1. Query أو body: `sessionId`  
2. Query أو body: `api_key` (مفتاح `wb_...`)  
3. Header: `Authorization: Bearer <api_key>`

**أمثلة مسارات:**

| الغرض | طريقة | مسار تقريبي |
|--------|--------|-------------|
| صحة الميكروسيرفس | GET | `/api/whatsapp/health` |
| حالة جلسة | GET | `/api/whatsapp/status?sessionId=...` |
| قائمة الشاتات | GET | `/api/whatsapp/chats?limit=50&offset=0&refresh=false` |
| رسائل شات | GET | `/api/whatsapp/messages?chatId=...` |
| إرسال نص | POST | `/api/whatsapp/send-message` — JSON: `phoneNumber`, `message` |
| إرسال ميديا | POST | `/api/whatsapp/send-media` — JSON: `phoneNumber`, `type`, `data` (base64), `caption`, `mimeType` |

**شكل الرد الموحّد** (دالة `apiResponse`):

```json
{
  "success": true,
  "message": "...",
  "data": { ... محتوى الميكروسيرفس بعد المعالجة ... }
}
```

قائمة الشاتات في `data` تكون غالباً بعد **تطبيع** الحقول (`chats`).

---

## 12) إعدادات البيئة

- **`WHATSAPP_BASE_URL`** (أو ما يعادله في `config/whatsapp.php`) — عنوان قاعدة ميكروسيرفس واتساب (مثلاً `http://host:3000` بدون مسار `/api` الخاص بـ Laravel).

---

## 13) ملخص سريع «لصاحبك المطوّر»

1. **الشات في الداشبورد** = Inertia + مسارات تحت `/customer/messages` + جلب/إرسال عبر `WhatsAppService`.
2. **التحديث** = polling كل ثانية على `messages/poll`، وليس WebSocket.
3. **الرسائل الأقدم** = `load-more` مع `offset`.
4. **عرض الميديا** = URL بروكسي `messages/media` + `message_id`، ليس رابط الميكروسيرفس مباشرة للمتصفح إن كان التصميم يعتمد على الجلسة.
5. **الإرسال** يحتاج **مستلم صحيح**: للـ LID استخدم المعرف كاملاً؛ للأرقام العادية النظام ينظّف الرقم — ومن الواجهة يُفضَّل الاعتماد على `phoneNumber` القادم من استجابة الرسائل عند توفره.
6. **التكامل الخارجي** = نفس المنطق عبر `/api/whatsapp/*` + Bearer token.

---

*آخر تحديث للمستند: وفق كود المشروع (Laravel + Inertia + WhatsAppService). أي تغيير في عقد الميكروسيرفس يجب أن يُعكس هنا.*

---

## ملحق demo-crm (الوضع الحالي في هذا المستودع)

يصف القسم أعلاه منتجاً بلوحة عميل و Inertia؛ **demo-crm** يطبّق شات واتساب للمستخدمين المسجّلين في لوحة الإدارة كالتالي:

| الموضوع | في المستند المرجعي | في demo-crm |
|--------|---------------------|-------------|
| الواجهة | Inertia (Vue) | Blade + JavaScript في `resources/views/customer/messages.blade.php` (مسار موحّد) و`resources/views/whatsapp/services.blade.php` (تبويبات إضافية) |
| المسارات | `/customer/messages/*` | تحت `auth`: **`customer.messages.*`** في `routes/customer.php` + **`whatsapp.services.*`** في `routes/web.php` |
| الكنترولر | `MessagesController` | `App\Http\Controllers\Customer\MessagesController` + `WhatsAppServiceController` واجهات قديمة |
| خدمة الربط | `WhatsAppService` | `App\Services\WhatsAppService` (تطبّق `WhatsBridgeService` + تطبيع الشاتات + بروكسي ميديا، إلخ) |
| الجلسة | من DB مرتبط بالمستخدم | من `WHATSBRIDGE_SESSION_ID` أو استخراج من `WHATSBRIDGE_API_KEY` (مفتاح `wb_...`) أو **`session_id` / `sessionId` في الطلب** |
| التحديث التلقائي | polling ~1s | في **`/customer/messages`**: استطلاع **polling** اختياري (افتراضي مفعّل؛ `WHATSAPP_POLL_INTERVAL_MS`). في **`/whatsapp/services`** (تبويب شات): تحديث يدوي |
| بروكسي الميديا | `customer.messages.media` | `GET customer.messages.media` و`GET whatsapp.services.media` — نفس منطق `WhatsAppService::proxyMessageMedia` |
| الإعدادات | `WHATSAPP_BASE_URL` | `config/services.php` → `whatsbridge.*` + `config/whatsapp.php` (`WHATSAPP_POLL_INTERVAL_MS`, …) |

**خلاصة:** مسار **`/customer/messages`** يطابق خطوات التوثيق (poll، load-more، send-media، بروكسي ميديا، `session_id` في الاستعلام). مسار **`/whatsapp/services`** يبقى كما هو لإرسال لرقم واحد / ليدز وواجهة الشات اليدوية.
