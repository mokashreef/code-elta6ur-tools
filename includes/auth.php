<?php
/**
 * نظام المصادقة
 * Code Elta6ur Tools
 */

/**
 * تسجيل مستخدم جديد
 */
function registerUser($name, $email, $password) {
    $db = getDB();
    
    // التحقق من وجود البريد
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        return ['success' => false, 'message' => 'البريد الإلكتروني مستخدم بالفعل'];
    }
    
    // التحقق من قوة كلمة المرور
    if (strlen($password) < 6) {
        return ['success' => false, 'message' => 'كلمة المرور يجب أن تكون 6 أحرف على الأقل'];
    }
    
    // تشفير كلمة المرور
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // إدخال المستخدم
    $stmt = $db->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $stmt->execute([$name, $email, $hashedPassword]);
    
    $userId = $db->lastInsertId();
    
    // تسجيل الدخول تلقائياً
    loginSession($userId, $name, $email, 'user');
    
    logAction($userId, 'register', 'تسجيل حساب جديد');
    
    return ['success' => true, 'message' => 'تم إنشاء الحساب بنجاح'];
}

/**
 * تسجيل الدخول
 */
function loginUser($email, $password) {
    $db = getDB();
    
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if (!$user || !password_verify($password, $user['password'])) {
        return ['success' => false, 'message' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة'];
    }
    
    // إنشاء الجلسة
    loginSession($user['id'], $user['name'], $user['email'], $user['role']);
    
    logAction($user['id'], 'login', 'تسجيل دخول');
    
    return ['success' => true, 'message' => 'تم تسجيل الدخول بنجاح'];
}

/**
 * إنشاء جلسة تسجيل الدخول
 */
function loginSession($userId, $name, $email, $role) {
    session_regenerate_id(true);
    $_SESSION['user_id'] = $userId;
    $_SESSION['user_name'] = $name;
    $_SESSION['user_email'] = $email;
    $_SESSION['user_role'] = $role;
    $_SESSION['logged_in'] = true;
    $_SESSION['login_time'] = time();
}

/**
 * تسجيل الخروج
 */
function logoutUser() {
    if (isLoggedIn()) {
        logAction($_SESSION['user_id'], 'logout', 'تسجيل خروج');
    }
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
}

/**
 * التحقق من تسجيل الدخول
 */
function isLoggedIn() {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

/**
 * التحقق من صلاحية الأدمن
 */
function isAdmin() {
    return isLoggedIn() && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

/**
 * إجبار تسجيل الدخول
 */
function requireLogin() {
    if (!isLoggedIn()) {
        setFlash('يجب تسجيل الدخول أولاً', 'warning');
        redirect('auth/login.php');
    }
}

/**
 * إجبار صلاحية أدمن
 */
function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        setFlash('ليس لديك صلاحية للوصول', 'error');
        redirect('index.php');
    }
}

/**
 * جلب بيانات المستخدم الحالي
 */
function getCurrentUser() {
    if (!isLoggedIn()) return null;
    $db = getDB();
    $stmt = $db->prepare("SELECT id, name, email, role, avatar, bio, created_at FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

/**
 * تحديث الملف الشخصي
 */
function updateProfile($userId, $name, $bio = '') {
    $db = getDB();
    $stmt = $db->prepare("UPDATE users SET name = ?, bio = ? WHERE id = ?");
    $result = $stmt->execute([$name, $bio, $userId]);
    if ($result) {
        $_SESSION['user_name'] = $name;
    }
    return $result;
}

/**
 * تغيير كلمة المرور
 */
function changePassword($userId, $currentPassword, $newPassword) {
    $db = getDB();
    $stmt = $db->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    
    if (!password_verify($currentPassword, $user['password'])) {
        return ['success' => false, 'message' => 'كلمة المرور الحالية غير صحيحة'];
    }
    
    if (strlen($newPassword) < 6) {
        return ['success' => false, 'message' => 'كلمة المرور الجديدة يجب أن تكون 6 أحرف على الأقل'];
    }
    
    $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
    $stmt = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->execute([$hashed, $userId]);
    
    return ['success' => true, 'message' => 'تم تغيير كلمة المرور بنجاح'];
}
