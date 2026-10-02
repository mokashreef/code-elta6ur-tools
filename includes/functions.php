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

/**
 * جلب جميع الأدوات
 */
function getAllTools($activeOnly = true) {
    $db = getDB();
    $sql = "SELECT * FROM tools";
    if ($activeOnly) {
        $sql .= " WHERE status = 1";
    }
    $sql .= " ORDER BY sort_order ASC";
    return $db->query($sql)->fetchAll();
}

/**
 * جلب الأدوات حسب الفئة
 */
function getToolsByCategory($category, $activeOnly = true) {
    $db = getDB();
    $sql = "SELECT * FROM tools WHERE category = ?";
    if ($activeOnly) {
        $sql .= " AND status = 1";
    }
    $sql .= " ORDER BY sort_order ASC";
    $stmt = $db->prepare($sql);
    $stmt->execute([$category]);
    return $stmt->fetchAll();
}

/**
 * جلب أداة بالـ slug
 */
function getToolBySlug($slug) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM tools WHERE slug = ? AND status = 1");
    $stmt->execute([$slug]);
    return $stmt->fetch();
}

/**
 * جلب أداة بالـ ID
 */
function getToolById($id) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM tools WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
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
 * جلب القوالب
 */
function getTemplates($toolSlug, $type = null) {
    $db = getDB();
    $sql = "SELECT * FROM templates WHERE tool_slug = ?";
    $params = [$toolSlug];
    if ($type) {
        $sql .= " AND type = ?";
        $params[] = $type;
    }
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * جلب قالب واحد
 */
function getTemplate($toolSlug, $type = 'default') {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM templates WHERE tool_slug = ? AND type = ? LIMIT 1");
    $stmt->execute([$toolSlug, $type]);
    return $stmt->fetch();
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
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO logs (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)");
    $stmt->execute([$userId, $action, $details, $_SERVER['REMOTE_ADDR'] ?? '']);
}

/**
 * التحقق من المفضلة
 */
function isFavorite($userId, $toolId) {
    $db = getDB();
    $stmt = $db->prepare("SELECT id FROM favorites WHERE user_id = ? AND tool_id = ?");
    $stmt->execute([$userId, $toolId]);
    return $stmt->fetch() !== false;
}

/**
 * تبديل المفضلة
 */
function toggleFavorite($userId, $toolId) {
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
}

/**
 * جلب المفضلات
 */
function getFavorites($userId) {
    $db = getDB();
    $stmt = $db->prepare("
        SELECT t.* FROM favorites f 
        JOIN tools t ON f.tool_id = t.id 
        WHERE f.user_id = ? AND t.status = 1 
        ORDER BY f.created_at DESC
    ");
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

/**
 * البحث في الأدوات
 */
function searchTools($query) {
    $db = getDB();
    $searchTerm = "%{$query}%";
    $stmt = $db->prepare("SELECT * FROM tools WHERE status = 1 AND (name LIKE ? OR description LIKE ?) ORDER BY sort_order ASC");
    $stmt->execute([$searchTerm, $searchTerm]);
    return $stmt->fetchAll();
}

/**
 * أسماء الفئات بالعربي
 */
function getCategoryName($category) {
    $categories = [
        'career' => 'العمل الحر والمهني',
        'generators' => 'المولدات',
        'text' => 'النصوص والأكواد',
        'productivity' => 'الإنتاجية'
    ];
    return $categories[$category] ?? $category;
}

/**
 * أيقونة الفئة
 */
function getCategoryIcon($category) {
    $icons = [
        'career' => 'fa-rocket',
        'generators' => 'fa-magic',
        'text' => 'fa-code',
        'productivity' => 'fa-chart-line'
    ];
    return $icons[$category] ?? 'fa-tools';
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
