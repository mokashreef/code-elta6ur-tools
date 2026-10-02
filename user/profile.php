<?php
/**
 * الملف الشخصي
 */
require_once __DIR__ . '/../config/app.php';
requireLogin();

$pageTitle = 'الملف الشخصي';
$currentPage = 'profile';

$user = getCurrentUser();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'update_profile') {
        $name = sanitize($_POST['name'] ?? '');
        $bio = sanitize($_POST['bio'] ?? '');
        
        if (empty($name)) {
            setFlash('الاسم مطلوب', 'error');
        } else {
            updateProfile($_SESSION['user_id'], $name, $bio);
            setFlash('تم تحديث الملف الشخصي بنجاح', 'success');
            $user = getCurrentUser();
        }
    } elseif ($action === 'change_password') {
        $current = $_POST['current_password'] ?? '';
        $new = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';
        
        if ($new !== $confirm) {
            setFlash('كلمتا المرور غير متطابقتين', 'error');
        } else {
            $result = changePassword($_SESSION['user_id'], $current, $new);
            setFlash($result['message'], $result['success'] ? 'success' : 'error');
        }
    }
    
    redirect('user/profile.php');
}

include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-user"></i>
        الملف الشخصي
    </h1>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem">
    <!-- تعديل المعلومات -->
    <div class="card">
        <h3 class="section-title" style="margin-bottom:1.5rem">
            <i class="fas fa-edit"></i> المعلومات الشخصية
        </h3>
        <form method="POST">
            <input type="hidden" name="action" value="update_profile">
            
            <div class="form-group">
                <label class="form-label">الاسم</label>
                <input type="text" name="name" class="form-control" value="<?= sanitize($user['name']) ?>" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">البريد الإلكتروني</label>
                <input type="email" class="form-control" value="<?= sanitize($user['email']) ?>" disabled>
                <span class="form-hint">لا يمكن تغيير البريد الإلكتروني</span>
            </div>
            
            <div class="form-group">
                <label class="form-label">نبذة عنك</label>
                <textarea name="bio" class="form-control" rows="4" placeholder="اكتب نبذة قصيرة عن نفسك..."><?= sanitize($user['bio'] ?? '') ?></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> حفظ التغييرات
            </button>
        </form>
    </div>

    <!-- تغيير كلمة المرور -->
    <div class="card">
        <h3 class="section-title" style="margin-bottom:1.5rem">
            <i class="fas fa-lock"></i> تغيير كلمة المرور
        </h3>
        <form method="POST">
            <input type="hidden" name="action" value="change_password">
            
            <div class="form-group">
                <label class="form-label">كلمة المرور الحالية</label>
                <input type="password" name="current_password" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">كلمة المرور الجديدة</label>
                <input type="password" name="new_password" class="form-control" required minlength="6">
            </div>
            
            <div class="form-group">
                <label class="form-label">تأكيد كلمة المرور الجديدة</label>
                <input type="password" name="confirm_password" class="form-control" required>
            </div>
            
            <button type="submit" class="btn btn-warning">
                <i class="fas fa-key"></i> تغيير كلمة المرور
            </button>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
