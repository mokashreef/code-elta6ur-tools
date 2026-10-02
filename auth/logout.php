<?php
/**
 * تسجيل الخروج
 */
require_once __DIR__ . '/../config/app.php';
logoutUser();
setFlash('تم تسجيل الخروج بنجاح', 'success');
redirect('index.php');
