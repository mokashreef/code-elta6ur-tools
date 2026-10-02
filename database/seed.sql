-- ============================================
-- Code Elta6ur Tools - البيانات الأولية
-- ============================================
-- ملاحظة: شغّل هذا الملف بعد schema.sql
-- ============================================

-- ============================================
-- حساب الأدمن الافتراضي
-- كلمة المرور: Admin@2026
-- ============================================
INSERT INTO `users` (`name`, `email`, `password`, `role`) VALUES
('مدير النظام', 'contact@gmail.com', 'PLACEHOLDER_WILL_BE_SET_BY_INSTALL', 'admin');

-- ============================================
-- الأدوات (25 أداة)
-- ============================================
INSERT INTO `tools` (`name`, `slug`, `description`, `icon`, `category`, `status`, `sort_order`) VALUES
-- أدوات التقديم والعمل الحر
('مولد سيرة ذاتية', 'cv-generator', 'إنشاء سيرة ذاتية احترافية بالعربي جاهزة للإرسال', 'fa-file-alt', 'career', 1, 1),
('مولد بورتفوليو نصي', 'portfolio-generator', 'إنشاء صفحة بورتفوليو نصية تعرض مشاريعك وخبراتك', 'fa-briefcase', 'career', 1, 2),
('مولد رسالة تقديم', 'proposal-generator', 'إنشاء رسالة تقديم احترافية للعمل الحر', 'fa-envelope-open-text', 'career', 1, 3),
('مولد README', 'readme-generator', 'إنشاء ملف README احترافي لمشاريع GitHub', 'fa-book', 'career', 1, 4),
('مولد Cover Letter', 'cover-letter', 'إنشاء رسالة تعريفية للتقدم للوظائف', 'fa-file-signature', 'career', 1, 5),
('مولد نبذة احترافية', 'bio-generator', 'إنشاء نبذة تعريفية قصيرة لحساباتك المهنية', 'fa-id-card', 'career', 1, 6),
('مولد أفكار مشاريع', 'project-ideas', 'اقتراح أفكار مشاريع برمجية حسب تخصصك ومستواك', 'fa-lightbulb', 'career', 1, 7),

-- أدوات التوليد
('مولد أسماء مستخدمين', 'username-generator', 'توليد أسماء مستخدمين إبداعية ومميزة', 'fa-user-tag', 'generators', 1, 8),
('مولد أسماء مشاريع', 'project-name-generator', 'توليد أسماء جذابة لمشاريعك البرمجية', 'fa-project-diagram', 'generators', 1, 9),
('اقتراح أسماء دومينات', 'domain-suggester', 'اقتراح أسماء نطاقات متاحة لمشروعك', 'fa-globe', 'generators', 1, 10),
('مولد كلمات مرور', 'password-generator', 'توليد كلمات مرور قوية وآمنة', 'fa-key', 'generators', 1, 11),
('مولد ألوان', 'color-palette', 'إنشاء لوحات ألوان متناسقة لمشاريعك', 'fa-palette', 'generators', 1, 12),

-- أدوات النصوص والأكواد
('منسق JSON', 'json-formatter', 'تنسيق وتجميل أكواد JSON بشكل مقروء', 'fa-code', 'text', 1, 13),
('تحويل Base64', 'base64-converter', 'تشفير وفك تشفير النصوص بصيغة Base64', 'fa-exchange-alt', 'text', 1, 14),
('تحويل النص إلى Slug', 'slug-converter', 'تحويل النصوص العربية والإنجليزية إلى روابط URL', 'fa-link', 'text', 1, 15),
('عداد الكلمات', 'word-counter', 'حساب عدد الكلمات والأحرف والأسطر في النص', 'fa-sort-numeric-up', 'text', 1, 16),
('تنسيق النص', 'text-formatter', 'تحويل النص بين أنماط مختلفة (كبير/صغير/عناوين)', 'fa-font', 'text', 1, 17),
('مولد أكواد جاهزة', 'code-templates', 'قوالب أكواد جاهزة للاستخدام بلغات مختلفة', 'fa-file-code', 'text', 1, 18),
('مولد صفحة HTML', 'html-page-generator', 'إنشاء صفحة HTML بسيطة جاهزة للاستخدام', 'fa-html5', 'text', 1, 19),

-- أدوات الإنتاجية
('مولد فاتورة', 'invoice-generator', 'إنشاء فواتير احترافية لعملائك', 'fa-file-invoice-dollar', 'productivity', 1, 20),
('محرر Markdown', 'markdown-editor', 'كتابة وتحرير Markdown مع معاينة فورية', 'fa-edit', 'productivity', 1, 21),
('قائمة مهام', 'todo-list', 'إدارة مهامك اليومية بسهولة', 'fa-tasks', 'productivity', 1, 22),
('حفظ ملاحظات', 'notes', 'حفظ وتنظيم ملاحظاتك الشخصية', 'fa-sticky-note', 'productivity', 1, 23),
('تتبع الوقت', 'time-tracker', 'تتبع وقت العمل على مشاريعك', 'fa-clock', 'productivity', 1, 24),
('حاسبة ميزانية', 'budget-calculator', 'حساب وتتبع ميزانية مشاريعك', 'fa-calculator', 'productivity', 1, 25);

-- ============================================
-- القوالب النصية
-- ============================================

-- قوالب السيرة الذاتية
INSERT INTO `templates` (`tool_slug`, `type`, `name`, `content`, `language`) VALUES
('cv-generator', 'professional', 'قالب احترافي', '# {{name}}
## {{title}}

### معلومات التواصل
- 📧 البريد الإلكتروني: {{email}}
- 📱 الهاتف: {{phone}}
- 📍 الموقع: {{location}}
- 🔗 LinkedIn: {{linkedin}}
- 💻 GitHub: {{github}}

---

### الملخص المهني
{{summary}}

---

### الخبرات العملية
{{experience}}

---

### التعليم
{{education}}

---

### المهارات التقنية
{{skills}}

---

### اللغات
{{languages}}

---

### المشاريع
{{projects}}', 'ar'),

('cv-generator', 'minimal', 'قالب بسيط', '{{name}} | {{title}}
================================
{{email}} | {{phone}} | {{location}}

الملخص: {{summary}}

الخبرات: {{experience}}

التعليم: {{education}}

المهارات: {{skills}}

اللغات: {{languages}}', 'ar'),

('cv-generator', 'modern', 'قالب عصري', '╔══════════════════════════════════════╗
║  {{name}}
║  {{title}}
╚══════════════════════════════════════╝

◆ التواصل
  {{email}} • {{phone}} • {{location}}

◆ نبذة
  {{summary}}

◆ الخبرات
  {{experience}}

◆ المهارات
  {{skills}}

◆ التعليم
  {{education}}

◆ اللغات
  {{languages}}', 'ar');

-- قوالب البورتفوليو
INSERT INTO `templates` (`tool_slug`, `type`, `name`, `content`, `language`) VALUES
('portfolio-generator', 'default', 'قالب افتراضي', '# 👋 مرحباً، أنا {{name}}

## {{title}}

{{bio}}

---

## 🚀 مشاريعي

{{projects}}

---

## 💼 خبراتي

{{experience}}

---

## 🛠️ مهاراتي

{{skills}}

---

## 📬 تواصل معي

- البريد: {{email}}
- GitHub: {{github}}
- LinkedIn: {{linkedin}}', 'ar');

-- قوالب رسالة التقديم
INSERT INTO `templates` (`tool_slug`, `type`, `name`, `content`, `language`) VALUES
('proposal-generator', 'freelance', 'رسالة عمل حر', 'مرحباً {{client_name}}،

قرأت وصف مشروعك "{{project_title}}" بعناية، وأعتقد أنني الشخص المناسب لتنفيذه.

**لماذا أنا؟**
{{why_me}}

**خطة العمل:**
{{work_plan}}

**المدة المتوقعة:** {{duration}}
**الميزانية المقترحة:** {{budget}}

**أعمال سابقة مشابهة:**
{{portfolio_links}}

أتطلع للعمل معك!

مع أطيب التحيات،
{{name}}', 'ar'),

('proposal-generator', 'formal', 'رسالة رسمية', 'السيد/السيدة {{client_name}} المحترم/ة،

تحية طيبة وبعد،

بالإشارة إلى مشروعكم "{{project_title}}"، يسرني أن أقدم لكم عرضي لتنفيذ هذا المشروع.

**الخبرة:**
{{why_me}}

**منهجية العمل:**
{{work_plan}}

**الجدول الزمني:** {{duration}}
**التكلفة:** {{budget}}

**نماذج من أعمالي:**
{{portfolio_links}}

في انتظار ردكم الكريم.

مع فائق الاحترام،
{{name}}', 'ar');

-- قوالب README
INSERT INTO `templates` (`tool_slug`, `type`, `name`, `content`, `language`) VALUES
('readme-generator', 'standard', 'قالب معياري', '# {{project_name}}

{{description}}

## 🚀 المميزات

{{features}}

## 📋 المتطلبات

{{requirements}}

## ⚙️ التثبيت

```bash
{{installation}}
```

## 🔧 الاستخدام

{{usage}}

## 📸 لقطات شاشة

{{screenshots}}

## 🤝 المساهمة

{{contributing}}

## 📝 الرخصة

{{license}}

## 📬 التواصل

{{contact}}

---
صنع بـ ❤️ في سوريا', 'ar');

-- قوالب Cover Letter
INSERT INTO `templates` (`tool_slug`, `type`, `name`, `content`, `language`) VALUES
('cover-letter', 'professional', 'رسالة احترافية', '{{name}}
{{email}} | {{phone}}
{{date}}

إلى: فريق التوظيف في {{company}}

الموضوع: التقدم لوظيفة {{position}}

السادة المحترمون،

أتقدم بطلبي لشغل وظيفة {{position}} في شركتكم الموقرة {{company}}.

{{intro_paragraph}}

**خبراتي ومؤهلاتي:**
{{qualifications}}

**لماذا {{company}}؟**
{{why_company}}

أتطلع لفرصة مناقشة كيف يمكنني المساهمة في نجاح فريقكم.

مع خالص التقدير،
{{name}}', 'ar');

-- قوالب النبذة الاحترافية
INSERT INTO `templates` (`tool_slug`, `type`, `name`, `content`, `language`) VALUES
('bio-generator', 'short', 'نبذة قصيرة', '{{name}} | {{title}} متخصص في {{specialization}} مع خبرة {{years}} سنوات. {{achievement}} 🚀', 'ar'),
('bio-generator', 'medium', 'نبذة متوسطة', '👋 مرحباً! أنا {{name}}، {{title}} سوري متخصص في {{specialization}}.

لدي خبرة {{years}} سنوات في {{field}}. {{achievement}}

أعمل حالياً على {{current_work}} وأهتم بـ {{interests}}.

📬 تواصل معي: {{email}}', 'ar'),
('bio-generator', 'professional', 'نبذة احترافية', '{{name}} هو {{title}} سوري ذو خبرة تمتد لـ {{years}} سنوات في مجال {{field}}. متخصص في {{specialization}}، وقد عمل على {{projects_count}} مشروع ناجح.

{{achievement}}

يمتلك مهارات متقدمة في {{skills}}، ويسعى دائماً لتقديم حلول مبتكرة وعالية الجودة.

{{current_work}}

للتواصل: {{email}} | {{linkedin}}', 'ar');

-- قوالب أكواد جاهزة
INSERT INTO `templates` (`tool_slug`, `type`, `name`, `content`, `language`) VALUES
('code-templates', 'html-boilerplate', 'قالب HTML أساسي', '<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{title}}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: ''Cairo'', sans-serif; direction: rtl; }
    </style>
</head>
<body>
    <h1>{{title}}</h1>
</body>
</html>', 'ar'),

('code-templates', 'php-crud', 'قالب PHP CRUD', '<?php
// الاتصال بقاعدة البيانات
$pdo = new PDO("mysql:host=localhost;dbname={{db_name}};charset=utf8mb4", "root", "");

// إضافة
function create($data) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO {{table}} ({{columns}}) VALUES ({{placeholders}})");
    return $stmt->execute($data);
}

// قراءة الكل
function readAll() {
    global $pdo;
    return $pdo->query("SELECT * FROM {{table}}")->fetchAll(PDO::FETCH_ASSOC);
}

// قراءة واحد
function readOne($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM {{table}} WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// تعديل
function update($id, $data) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE {{table}} SET {{set_clause}} WHERE id = ?");
    $data[] = $id;
    return $stmt->execute($data);
}

// حذف
function delete($id) {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM {{table}} WHERE id = ?");
    return $stmt->execute([$id]);
}', 'ar'),

('code-templates', 'css-grid', 'قالب CSS Grid', '.container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
    padding: 2rem;
}

.card {
    background: #1a1a2e;
    border-radius: 12px;
    padding: 1.5rem;
    border: 1px solid rgba(255,255,255,0.1);
    transition: transform 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
}', 'ar'),

('code-templates', 'js-fetch', 'قالب JavaScript Fetch', '// إرسال بيانات POST
async function postData(url, data) {
    try {
        const response = await fetch(url, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(data)
        });
        if (!response.ok) throw new Error("خطأ في الطلب");
        return await response.json();
    } catch (error) {
        console.error("خطأ:", error);
        throw error;
    }
}

// جلب بيانات GET
async function getData(url) {
    try {
        const response = await fetch(url);
        if (!response.ok) throw new Error("خطأ في الطلب");
        return await response.json();
    } catch (error) {
        console.error("خطأ:", error);
        throw error;
    }
}', 'ar'),

('code-templates', 'express-api', 'قالب Express.js API', 'const express = require("express");
const app = express();
const PORT = {{port}} || 3000;

app.use(express.json());

// المسارات
app.get("/api/{{resource}}", (req, res) => {
    res.json({ message: "جلب جميع العناصر" });
});

app.get("/api/{{resource}}/:id", (req, res) => {
    res.json({ message: `جلب العنصر ${req.params.id}` });
});

app.post("/api/{{resource}}", (req, res) => {
    res.status(201).json({ message: "تم الإنشاء", data: req.body });
});

app.put("/api/{{resource}}/:id", (req, res) => {
    res.json({ message: `تم التحديث ${req.params.id}` });
});

app.delete("/api/{{resource}}/:id", (req, res) => {
    res.json({ message: `تم الحذف ${req.params.id}` });
});

app.listen(PORT, () => console.log(`Server running on port ${PORT}`));', 'ar');

-- قوالب الفاتورة
INSERT INTO `templates` (`tool_slug`, `type`, `name`, `content`, `language`) VALUES
('invoice-generator', 'simple', 'فاتورة بسيطة', '═══════════════════════════════════════
              فاتورة
═══════════════════════════════════════

رقم الفاتورة: {{invoice_number}}
التاريخ: {{date}}

من: {{from_name}}
     {{from_email}}

إلى: {{to_name}}
      {{to_email}}

───────────────────────────────────────
البنود:
{{items}}
───────────────────────────────────────

المجموع الفرعي: {{subtotal}}
الضريبة ({{tax_rate}}%): {{tax}}
═══════════════════════════════════════
المجموع الكلي: {{total}}
═══════════════════════════════════════

ملاحظات: {{notes}}', 'ar');

-- أفكار المشاريع
INSERT INTO `templates` (`tool_slug`, `type`, `name`, `content`, `language`) VALUES
('project-ideas', 'web', 'مشاريع ويب مبتدئ', '[
    {"name": "موقع بورتفوليو شخصي", "description": "موقع لعرض مشاريعك ومهاراتك", "tech": "HTML, CSS, JavaScript", "difficulty": "مبتدئ", "duration": "3-5 أيام"},
    {"name": "مدونة شخصية", "description": "مدونة بسيطة مع نظام مقالات", "tech": "PHP, MySQL", "difficulty": "مبتدئ", "duration": "أسبوع"},
    {"name": "قائمة مهام", "description": "تطبيق لإدارة المهام اليومية", "tech": "HTML, CSS, JavaScript", "difficulty": "مبتدئ", "duration": "2-3 أيام"},
    {"name": "آلة حاسبة متقدمة", "description": "آلة حاسبة علمية بواجهة جميلة", "tech": "HTML, CSS, JavaScript", "difficulty": "مبتدئ", "duration": "يومين"},
    {"name": "صفحة هبوط", "description": "صفحة تسويقية لمنتج أو خدمة", "tech": "HTML, CSS", "difficulty": "مبتدئ", "duration": "يوم واحد"}
]', 'ar'),
('project-ideas', 'web-intermediate', 'مشاريع ويب متوسط', '[
    {"name": "متجر إلكتروني", "description": "متجر بسيط مع سلة مشتريات ودفع", "tech": "PHP, MySQL, JavaScript", "difficulty": "متوسط", "duration": "2-3 أسابيع"},
    {"name": "نظام إدارة محتوى", "description": "CMS بسيط لإدارة المقالات والصفحات", "tech": "PHP, MySQL", "difficulty": "متوسط", "duration": "2 أسابيع"},
    {"name": "شبكة اجتماعية مصغرة", "description": "منصة تواصل بسيطة مع تسجيل ومنشورات", "tech": "PHP, MySQL, AJAX", "difficulty": "متوسط", "duration": "3 أسابيع"},
    {"name": "نظام حجوزات", "description": "نظام حجز مواعيد أو خدمات", "tech": "PHP, MySQL, JavaScript", "difficulty": "متوسط", "duration": "2 أسابيع"},
    {"name": "لوحة تحكم إحصائيات", "description": "Dashboard لعرض بيانات وإحصائيات", "tech": "PHP, MySQL, Chart.js", "difficulty": "متوسط", "duration": "أسبوع"}
]', 'ar'),
('project-ideas', 'mobile', 'مشاريع موبايل', '[
    {"name": "تطبيق ملاحظات", "description": "تطبيق لحفظ وتنظيم الملاحظات", "tech": "React Native / Flutter", "difficulty": "مبتدئ", "duration": "أسبوع"},
    {"name": "تطبيق طقس", "description": "تطبيق لعرض حالة الطقس", "tech": "React Native / Flutter", "difficulty": "مبتدئ", "duration": "3 أيام"},
    {"name": "تطبيق أذكار", "description": "تطبيق أذكار الصباح والمساء مع تذكيرات", "tech": "React Native / Flutter", "difficulty": "مبتدئ", "duration": "أسبوع"},
    {"name": "تطبيق وصفات طبخ", "description": "تطبيق لعرض وحفظ وصفات الطبخ السوري", "tech": "React Native / Flutter", "difficulty": "متوسط", "duration": "2 أسابيع"},
    {"name": "تطبيق تعلم لغة", "description": "تطبيق بطاقات تعليمية للغات", "tech": "React Native / Flutter", "difficulty": "متوسط", "duration": "3 أسابيع"}
]', 'ar'),
('project-ideas', 'api', 'مشاريع API و Backend', '[
    {"name": "REST API لمدونة", "description": "API كامل مع CRUD ومصادقة", "tech": "Node.js, Express, MongoDB", "difficulty": "متوسط", "duration": "أسبوع"},
    {"name": "نظام مصادقة", "description": "نظام تسجيل دخول مع JWT و OAuth", "tech": "Node.js / PHP", "difficulty": "متوسط", "duration": "أسبوع"},
    {"name": "خدمة اختصار روابط", "description": "خدمة لاختصار الروابط مع إحصائيات", "tech": "Node.js / PHP, MySQL", "difficulty": "مبتدئ", "duration": "3 أيام"},
    {"name": "API لمتجر إلكتروني", "description": "API كامل لمتجر مع سلة ودفع", "tech": "Node.js, Express, MongoDB", "difficulty": "متقدم", "duration": "3 أسابيع"},
    {"name": "نظام إشعارات", "description": "خدمة إرسال إشعارات فورية", "tech": "Node.js, WebSocket", "difficulty": "متوسط", "duration": "أسبوع"}
]', 'ar');

-- إعدادات الموقع الافتراضية
INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES
('site_name', 'Code Elta6ur Tools'),
('site_description', 'منصة أدوات عملية للمبرمج السوري'),
('site_language', 'ar'),
('maintenance_mode', '0'),
('registration_enabled', '1');
