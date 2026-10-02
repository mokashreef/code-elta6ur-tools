<?php
/**
 * صفحة التسجيل
 */
require_once __DIR__ . '/../config/app.php';

if (isLoggedIn()) {
    redirect('user/dashboard.php');
}

$error = '';
$name = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    
    if (empty($name) || empty($email) || empty($password)) {
        $error = 'يرجى تعبئة جميع الحقول';
    } elseif ($password !== $confirm) {
        $error = 'كلمتا المرور غير متطابقتين';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'البريد الإلكتروني غير صالح';
    } else {
        $result = registerUser($name, $email, $password);
        if ($result['success']) {
            setFlash('تم إنشاء حسابك بنجاح! مرحباً بك 🎉', 'success');
            redirect('user/dashboard.php');
        } else {
            $error = $result['message'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إنشاء حساب - <?= APP_NAME ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-logo">
                <div class="logo-icon">
                    <i class="fas fa-terminal"></i>
                </div>
                <h1><?= APP_NAME ?></h1>
                <p>إنشاء حساب جديد</p>
            </div>

            <?php if ($error): ?>
            <div class="alert alert-error" style="margin:0 0 1rem">
                <i class="fas fa-exclamation-circle"></i>
                <span><?= $error ?></span>
            </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label class="form-label">الاسم الكامل <span class="required">*</span></label>
                    <input type="text" name="name" class="form-control" placeholder="أدخل اسمك" 
                           value="<?= $name ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">البريد الإلكتروني <span class="required">*</span></label>
                    <input type="email" name="email" class="form-control" placeholder="example@email.com" 
                           value="<?= $email ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">كلمة المرور <span class="required">*</span></label>
                    <input type="password" name="password" class="form-control" placeholder="6 أحرف على الأقل" required minlength="6">
                </div>

                <div class="form-group">
                    <label class="form-label">تأكيد كلمة المرور <span class="required">*</span></label>
                    <input type="password" name="confirm_password" class="form-control" placeholder="أعد كتابة كلمة المرور" required>
                </div>

                <button type="submit" class="btn btn-primary btn-block btn-lg">
                    <i class="fas fa-user-plus"></i>
                    إنشاء الحساب
                </button>
            </form>

            <div class="auth-footer">
                لديك حساب بالفعل؟ <a href="login.php">تسجيل الدخول</a>
            </div>
        </div>
    </div>
</body>
</html>
