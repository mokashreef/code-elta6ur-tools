<?php
/**
 * وظائف عامة
 * Code Elta6ur Tools
 */

/**
 * تنظيف المدخلات
 */
function sanitize($input) {
    if (is_array($input)) {
        return array_map('sanitize', $input);
    }
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

/**
 * تنظيف المدخلات مع السماح بـ HTML
 */
function sanitizeHtml($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * إعادة التوجيه
 */
function redirect($url) {
    header("Location: " . BASE_URL . ltrim($url, '/'));
    exit;
}

/**
 * رسائل Flash
 */
function setFlash($message, $type = 'success') {
    $_SESSION['flash'] = [
        'message' => $message,
        'type' => $type
    ];
}

function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * إنشاء CSRF Token
 */
function generateCSRFToken() {
    if (empty($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

/**
 * التحقق من CSRF Token
 */
function verifyCSRFToken($token) {
    if (empty($_SESSION[CSRF_TOKEN_NAME]) || empty($token)) {
        return false;
    }
    $valid = hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
    // تجديد التوكن بعد الاستخدام
    unset($_SESSION[CSRF_TOKEN_NAME]);
    return $valid;
}

/**
 * حقل CSRF مخفي
 */
function csrfField() {
    $token = generateCSRFToken();
    return '<input type="hidden" name="' . CSRF_TOKEN_NAME . '" value="' . $token . '">';
}

require_once __DIR__ . '/tools_registry.php';
require_once __DIR__ . '/tool_layout.php';

/**
 * جلب جميع الأدوات
 */
function getAllTools($activeOnly = true) {
    // الاعتماد على السجل الموحد الشامل
    $tools = getAllRegisteredTools();
    if ($activeOnly) {
        $tools = array_filter($tools, function($t) { return ($t['status'] ?? 1) == 1; });
    }
    return array_values($tools);
}

/**
 * جلب الأدوات حسب الفئة
 */
function getToolsByCategory($category, $activeOnly = true) {
    $tools = getRegisteredToolsByCategory($category);
    if ($activeOnly) {
        $tools = array_filter($tools, function($t) { return ($t['status'] ?? 1) == 1; });
    }
    return array_values($tools);
}

/**
 * جلب أداة بالـ slug
 */
function getToolBySlug($slug) {
    $tool = getRegisteredToolBySlug($slug);
    if ($tool) return $tool;
    
    // محاولة من قاعدة البيانات إن وجدت
    try {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM tools WHERE slug = ? AND status = 1");
        $stmt->execute([$slug]);
        return $stmt->fetch();
    } catch (Throwable $e) {
        return null;
    }
}

/**
 * جلب أداة بالـ ID
 */
function getToolById($id) {
    $all = getAllRegisteredTools();
    foreach ($all as $t) {
        if ($t['id'] == $id) return $t;
    }
    try {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM tools WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    } catch (Throwable $e) {
        return null;
    }
}

/**
 * زيادة عداد استخدام الأداة
 */
function incrementToolUsage($toolId) {
    $db = getDB();
    $stmt = $db->prepare("UPDATE tools SET usage_count = usage_count + 1 WHERE id = ?");
    $stmt->execute([$toolId]);
}

/**
 * حفظ نتيجة المستخدم
 */
function saveOutput($userId, $toolId, $title, $content) {
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO user_outputs (user_id, tool_id, title, content) VALUES (?, ?, ?, ?)");
    $stmt->execute([$userId, $toolId, $title, $content]);
    return $db->lastInsertId();
}

/**
 * جلب نتائج المستخدم
 */
function getUserOutputs($userId, $limit = 20, $offset = 0) {
    $db = getDB();
    $stmt = $db->prepare("
        SELECT uo.*, t.name as tool_name, t.slug as tool_slug, t.icon as tool_icon 
        FROM user_outputs uo 
        JOIN tools t ON uo.tool_id = t.id 
        WHERE uo.user_id = ? 
        ORDER BY uo.created_at DESC 
        LIMIT ? OFFSET ?
    ");
    $stmt->execute([$userId, $limit, $offset]);
    return $stmt->fetchAll();
}

/**
 * جلب نتيجة واحدة
 */
function getOutputById($outputId, $userId) {
    $db = getDB();
    $stmt = $db->prepare("SELECT uo.*, t.name as tool_name FROM user_outputs uo JOIN tools t ON uo.tool_id = t.id WHERE uo.id = ? AND uo.user_id = ?");
    $stmt->execute([$outputId, $userId]);
    return $stmt->fetch();
}

/**
 * تحديث نتيجة
 */
function updateOutput($outputId, $userId, $title, $content) {
    $db = getDB();
    $stmt = $db->prepare("UPDATE user_outputs SET title = ?, content = ? WHERE id = ? AND user_id = ?");
    return $stmt->execute([$title, $content, $outputId, $userId]);
}

/**
 * حذف نتيجة
 */
function deleteOutput($outputId, $userId) {
    $db = getDB();
    $stmt = $db->prepare("DELETE FROM user_outputs WHERE id = ? AND user_id = ?");
    return $stmt->execute([$outputId, $userId]);
}

/**
 * جلب القوالب مع دعم القوالب الافتراضية المدمجة
 */
function getTemplates($toolSlug, $type = null) {
    try {
        $db = getDB();
        $sql = "SELECT * FROM templates WHERE tool_slug = ?";
        $params = [$toolSlug];
        if ($type) {
            $sql .= " AND type = ?";
            $params[] = $type;
        }
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $results = $stmt->fetchAll();
        if (!empty($results)) {
            return $results;
        }
    } catch (Throwable $e) {}

    // القوالب المدمجة الاحتياطية في حال كانت قاعدة البيانات فارغة
    $builtIn = getDefaultBuiltInTemplates($toolSlug);
    if ($type) {
        $filtered = [];
        foreach ($builtIn as $t) {
            if ($t['type'] === $type) $filtered[] = $t;
        }
        return $filtered;
    }
    return $builtIn;
}

/**
 * جلب قالب واحد
 */
function getTemplate($toolSlug, $type = 'default') {
    try {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM templates WHERE tool_slug = ? AND type = ? LIMIT 1");
        $stmt->execute([$toolSlug, $type]);
        $res = $stmt->fetch();
        if ($res) return $res;
    } catch (Throwable $e) {}

    $templates = getTemplates($toolSlug, $type);
    if (!empty($templates)) {
        return $templates[0];
    }
    // قالب افتراضي عام
    return [
        'tool_slug' => $toolSlug,
        'type' => $type,
        'name' => 'قالب افتراضي',
        'content' => "{{content}}"
    ];
}

/**
 * القوالب المدمجة في المنصة
 */
function getDefaultBuiltInTemplates($toolSlug) {
    $templates = [
        'cv-generator' => [
            ['tool_slug' => 'cv-generator', 'type' => 'professional', 'name' => 'قالب احترافي', 'content' => "# {{name}}\n## {{title}}\n\n### معلومات التواصل\n- 📧 البريد الإلكتروني: {{email}}\n- 📱 الهاتف: {{phone}}\n- 📍 الموقع: {{location}}\n- 🔗 LinkedIn: {{linkedin}}\n- 💻 GitHub: {{github}}\n\n---\n\n### الملخص المهني\n{{summary}}\n\n---\n\n### الخبرات العملية\n{{experience}}\n\n---\n\n### التعليم والشهادات\n{{education}}\n\n---\n\n### المهارات التقنية\n{{skills}}\n\n---\n\n### اللغات\n{{languages}}\n\n---\n\n### المشاريع البارزة\n{{projects}}"],
            ['tool_slug' => 'cv-generator', 'type' => 'modern', 'name' => 'قالب عصري', 'content' => "╔══════════════════════════════════════╗\n║  {{name}}\n║  {{title}}\n╚══════════════════════════════════════╝\n\n◆ التواصل: {{email}} | {{phone}} | {{location}}\n◆ الروابط: {{linkedin}} | {{github}}\n\n◆ نبذة:\n{{summary}}\n\n◆ الخبرات:\n{{experience}}\n\n◆ المهارات:\n{{skills}}\n\n◆ المشاريع:\n{{projects}}\n\n◆ التعليم: {{education}} | اللغات: {{languages}}"],
            ['tool_slug' => 'cv-generator', 'type' => 'minimal', 'name' => 'قالب بسيط ومختصر', 'content' => "{{name}} — {{title}}\n{{email}} | {{phone}} | {{location}}\n\nالهدف والنبذة:\n{{summary}}\n\nالخبرات:\n{{experience}}\n\nالمهارات التقنية: {{skills}}\nالتعليم: {{education}}\nالمشاريع: {{projects}}"]
        ],
        'proposal-generator' => [
            ['tool_slug' => 'proposal-generator', 'type' => 'freelance', 'name' => 'رسالة عمل حر مقنعة', 'content' => "مرحباً {{client_name}}،\n\nقرأت تفاصيل مشروعك \"{{project_title}}\" بعناية، ويسعدني تنفيذ هذا المشروع باحترافية وجودة عالية.\n\n**لماذا أنا الخيار الأنسب؟**\n{{why_me}}\n\n**خطة ومنهجية العمل:**\n{{work_plan}}\n\n**المدة المتوقعة للتسليم:** {{duration}}\n**الميزانية المقترحة:** {{budget}}\n\n**نماذج أعمال سابقة ذات صلة:**\n{{portfolio_links}}\n\nجاهز للبدء فوراً ومناقشة أي تفاصيل إضافية.\n\nمع فائق الاحترام والتقدير،\n{{name}}"],
            ['tool_slug' => 'proposal-generator', 'type' => 'formal', 'name' => 'عرض عمل رسمي للشركات', 'content' => "السيد/السيدة {{client_name}} المحترم/ة،\n\nتحية طيبة وبعد،\n\nبالإشارة إلى مشروعكم الموقر \"{{project_title}}\"، يسرني تقديم هذا العرض الفني والمالي لتنفيذ المشروع بأعلى معايير الجودة.\n\n**الخبرة والمؤهلات:**\n{{why_me}}\n\n**خطة التنفيذ ومراحل المشروع:**\n{{work_plan}}\n\n**الجدول الزمني:** {{duration}}\n**التكلفة الإجمالية:** {{budget}}\n\n**نماذج ومراجع الأعمال:**\n{{portfolio_links}}\n\nفي انتظار تشريفكم بالرد، ودمتم برعاية الله.\n\nمقدم العرض:\n{{name}}"]
        ],
        'readme-generator' => [
            ['tool_slug' => 'readme-generator', 'type' => 'standard', 'name' => 'قالب README قياسي', 'content' => "# {{project_name}}\n\n> {{description}}\n\n## ✨ المميزات الرئيسية\n{{features}}\n\n## 📋 المتطلبات\n{{requirements}}\n\n## 🚀 التثبيت والتشغيل\n```bash\n{{installation}}\n```\n\n## 💻 طريقة الاستخدام\n{{usage}}\n\n## 🤝 المساهمة\n{{contributing}}\n\n## 📝 الترخيص\n{{license}}\n\n## 📬 للتواصل والدعم\n{{contact}}"]
        ],
        'cover-letter' => [
            ['tool_slug' => 'cover-letter', 'type' => 'professional', 'name' => 'خطاب تغطية احترافي', 'content' => "الاسم: {{name}}\nالبريد: {{email}} | الهاتف: {{phone}}\nالتاريخ: {{date}}\n\nإلى: إدارة التوظيف والموارد البشرية في {{company}}\n\nالموضوع: التقدم لشغل وظيفة {{position}}\n\nتحية طيبة وبعد،\n\nيسرني أن أتقدم بطلبي لشغل وظيفة {{position}} المعلن عنها لدى مؤسستكم المرموقة {{company}}.\n\n{{intro_paragraph}}\n\n**أهم المهارات والمؤهلات:**\n{{qualifications}}\n\n**لماذا أود الانضمام إلى {{company}}؟**\n{{why_company}}\n\nكلي أمل أن أحظى بفرصة لإجراء مقابلة شخصية لمناقشة مؤهلاتي بتفصيل أكبر.\n\nوتفضلوا بقبول فائق الاحترام والتقدير،\n{{name}}"]
        ],
        'bio-generator' => [
            ['tool_slug' => 'bio-generator', 'type' => 'short', 'name' => 'نبذة سريعة', 'content' => "{{name}} | {{title}} متخصص في {{specialization}} بخبرة {{years}} سنوات. {{achievement}} 🚀"],
            ['tool_slug' => 'bio-generator', 'type' => 'professional', 'name' => 'نبذة مهنية متكاملة', 'content' => "{{name}} هو {{title}} متخصص في {{specialization}} ويمتلك خبرة تمتد لـ {{years}} سنوات. نجح في تنفيذ وإنجاز {{projects_count}} مشروع بمستوى احترافي.\n\n{{achievement}}\n\nالمهارات الأساسية: {{skills}}.\nللتواصل: {{email}} | {{linkedin}}"]
        ],
        'code-templates' => [
            ['tool_slug' => 'code-templates', 'type' => 'html-boilerplate', 'name' => 'قالب HTML5 متجاوب', 'content' => "<!DOCTYPE html>\n<html lang=\"ar\" dir=\"rtl\">\n<head>\n    <meta charset=\"UTF-8\">\n    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\n    <title>{{title}}</title>\n    <style>\n        body { font-family: 'Cairo', sans-serif; direction: rtl; margin: 0; padding: 2rem; background: #06080f; color: #f1f5f9; }\n    </style>\n</head>\n<body>\n    <h1>{{title}}</h1>\n    <p>مرحباً بك في موقعك الجديد!</p>\n</body>\n</html>"],
            ['tool_slug' => 'code-templates', 'type' => 'js-fetch', 'name' => 'دالة Fetch API في JavaScript', 'content' => "// جلب وإرسال البيانات عبر API\nasync function apiRequest(url, method = 'GET', data = null) {\n    const options = {\n        method,\n        headers: { 'Content-Type': 'application/json' }\n    };\n    if (data) options.body = JSON.stringify(data);\n    \n    try {\n        const response = await fetch(url, options);\n        if (!response.ok) throw new Error('HTTP error! status: ' + response.status);\n        return await response.json();\n    } catch (err) {\n        console.error('API Error:', err);\n        throw err;\n    }\n}"]
        ],
        'portfolio-generator' => [
            ['tool_slug' => 'portfolio-generator', 'type' => 'default', 'name' => 'صفحة بورتفوليو نصية', 'content' => "# 👋 مرحباً، أنا {{name}}\n## {{title}}\n\n{{bio}}\n\n---\n## 🚀 المشاريع\n{{projects}}\n\n---\n## 💼 الخبرات\n{{experience}}\n\n---\n## 🛠️ المهارات التقنية\n{{skills}}\n\n---\n## 📬 تواصل معي\n- البريد: {{email}}\n- GitHub: {{github}}\n- LinkedIn: {{linkedin}}"]
        ]
    ];

    return $templates[$toolSlug] ?? [
        ['tool_slug' => $toolSlug, 'type' => 'default', 'name' => 'قالب افتراضي', 'content' => "محتوى القالب الافتراضي"]
    ];
}

/**
 * تطبيق القالب
 */
function applyTemplate($template, $data) {
    $result = $template;
    foreach ($data as $key => $value) {
        $result = str_replace('{{' . $key . '}}', $value, $result);
    }
    return $result;
}

/**
 * تسجيل نشاط
 */
function logAction($userId, $action, $details = null) {
    try {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO logs (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)");
        $stmt->execute([$userId, $action, $details, $_SERVER['REMOTE_ADDR'] ?? '']);
    } catch (Throwable $e) {}
}

/**
 * التحقق من المفضلة
 */
function isFavorite($userId, $toolId) {
    try {
        $db = getDB();
        $stmt = $db->prepare("SELECT id FROM favorites WHERE user_id = ? AND tool_id = ?");
        $stmt->execute([$userId, $toolId]);
        return $stmt->fetch() !== false;
    } catch (Throwable $e) {
        return false;
    }
}

/**
 * تبديل المفضلة
 */
function toggleFavorite($userId, $toolId) {
    try {
        $db = getDB();
        if (isFavorite($userId, $toolId)) {
            $stmt = $db->prepare("DELETE FROM favorites WHERE user_id = ? AND tool_id = ?");
            $stmt->execute([$userId, $toolId]);
            return false;
        } else {
            $stmt = $db->prepare("INSERT INTO favorites (user_id, tool_id) VALUES (?, ?)");
            $stmt->execute([$userId, $toolId]);
            return true;
        }
    } catch (Throwable $e) {
        return false;
    }
}

/**
 * جلب المفضلات
 */
function getFavorites($userId) {
    try {
        $db = getDB();
        $stmt = $db->prepare("
            SELECT t.* FROM favorites f 
            JOIN tools t ON f.tool_id = t.id 
            WHERE f.user_id = ? AND t.status = 1 
            ORDER BY f.created_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    } catch (Throwable $e) {
        return [];
    }
}

/**
 * البحث الذكي في الأدوات عبر السجل الشامل
 */
function searchTools($query) {
    return searchRegisteredTools($query);
}

/**
 * أسماء الفئات بالعربي
 */
function getCategoryName($category) {
    $categories = getAppCategories();
    return $categories[$category]['name'] ?? ($categories[$category]['short_name'] ?? $category);
}

/**
 * أيقونة الفئة
 */
function getCategoryIcon($category) {
    $categories = getAppCategories();
    return $categories[$category]['icon'] ?? 'fa-tools';
}

/**
 * تنسيق التاريخ بالعربي
 */
function formatDate($date) {
    $timestamp = strtotime($date);
    $diff = time() - $timestamp;
    
    if ($diff < 60) return 'الآن';
    if ($diff < 3600) return floor($diff / 60) . ' دقيقة';
    if ($diff < 86400) return floor($diff / 3600) . ' ساعة';
    if ($diff < 604800) return floor($diff / 86400) . ' يوم';
    
    return date('Y/m/d', $timestamp);
}

/**
 * عدد نتائج المستخدم
 */
function getUserOutputsCount($userId) {
    $db = getDB();
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM user_outputs WHERE user_id = ?");
    $stmt->execute([$userId]);
    return $stmt->fetch()['count'];
}

/**
 * إحصائيات عامة (للأدمن)
 */
function getStats() {
    $db = getDB();
    return [
        'users' => $db->query("SELECT COUNT(*) as c FROM users")->fetch()['c'],
        'tools' => $db->query("SELECT COUNT(*) as c FROM tools WHERE status = 1")->fetch()['c'],
        'outputs' => $db->query("SELECT COUNT(*) as c FROM user_outputs")->fetch()['c'],
        'logs' => $db->query("SELECT COUNT(*) as c FROM logs")->fetch()['c'],
        'top_tools' => $db->query("SELECT name, usage_count FROM tools ORDER BY usage_count DESC LIMIT 5")->fetchAll()
    ];
}
