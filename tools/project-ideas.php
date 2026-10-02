<?php
/**
 * أداة: مولد أفكار مشاريع
 */
$result = '';

// أفكار مشاريع ثابتة مصنفة
$ideasDB = [
    'web' => [
        'مبتدئ' => [
            ['name' => 'موقع بورتفوليو شخصي', 'desc' => 'موقع لعرض مشاريعك ومهاراتك الشخصية', 'tech' => 'HTML, CSS, JavaScript', 'duration' => '3-5 أيام'],
            ['name' => 'مدونة شخصية', 'desc' => 'مدونة بسيطة مع نظام مقالات وتعليقات', 'tech' => 'PHP, MySQL', 'duration' => 'أسبوع'],
            ['name' => 'قائمة مهام تفاعلية', 'desc' => 'تطبيق لإدارة المهام مع السحب والإفلات', 'tech' => 'HTML, CSS, JS', 'duration' => '2-3 أيام'],
            ['name' => 'آلة حاسبة متقدمة', 'desc' => 'آلة حاسبة علمية بواجهة حديثة', 'tech' => 'HTML, CSS, JS', 'duration' => 'يومين'],
            ['name' => 'صفحة هبوط تسويقية', 'desc' => 'صفحة هبوط احترافية لمنتج رقمي', 'tech' => 'HTML, CSS', 'duration' => 'يوم واحد'],
            ['name' => 'كتاب طبخ رقمي', 'desc' => 'موقع لعرض وصفات الطبخ السوري', 'tech' => 'HTML, CSS, JS', 'duration' => '3 أيام'],
        ],
        'متوسط' => [
            ['name' => 'متجر إلكتروني', 'desc' => 'متجر كامل مع سلة مشتريات ونظام طلبات', 'tech' => 'PHP, MySQL, JS', 'duration' => '2-3 أسابيع'],
            ['name' => 'نظام إدارة محتوى (CMS)', 'desc' => 'نظام CMS مخصص لإدارة المقالات والصفحات', 'tech' => 'PHP, MySQL', 'duration' => '2 أسابيع'],
            ['name' => 'منصة تواصل اجتماعي مصغرة', 'desc' => 'منصة مع حسابات ومنشورات وتعليقات', 'tech' => 'PHP, MySQL, AJAX', 'duration' => '3 أسابيع'],
            ['name' => 'نظام حجز مواعيد', 'desc' => 'نظام حجز مواعيد لعيادة أو صالون', 'tech' => 'PHP, MySQL, JS', 'duration' => '2 أسابيع'],
            ['name' => 'لوحة إحصائيات تفاعلية', 'desc' => 'Dashboard لعرض بيانات وإحصائيات بيانية', 'tech' => 'PHP, MySQL, Chart.js', 'duration' => 'أسبوع'],
        ],
        'متقدم' => [
            ['name' => 'منصة تعليمية كاملة', 'desc' => 'منصة مع دورات ومقاطع فيديو وشهادات', 'tech' => 'Laravel, MySQL, Vue.js', 'duration' => '4-6 أسابيع'],
            ['name' => 'نظام إدارة مشاريع', 'desc' => 'مثل Trello مع لوحات ومهام وفرق', 'tech' => 'React, Node.js, MongoDB', 'duration' => '4 أسابيع'],
            ['name' => 'منصة عمل حر', 'desc' => 'منصة ربط بين أصحاب المشاريع والمستقلين', 'tech' => 'Laravel, MySQL, Vue.js', 'duration' => '6-8 أسابيع'],
        ]
    ],
    'mobile' => [
        'مبتدئ' => [
            ['name' => 'تطبيق ملاحظات', 'desc' => 'تطبيق لحفظ وتنظيم الملاحظات', 'tech' => 'React Native / Flutter', 'duration' => 'أسبوع'],
            ['name' => 'تطبيق أذكار', 'desc' => 'أذكار الصباح والمساء مع تذكيرات', 'tech' => 'React Native / Flutter', 'duration' => 'أسبوع'],
            ['name' => 'تطبيق حاسبة', 'desc' => 'حاسبة بتصميم حديث', 'tech' => 'React Native', 'duration' => '3 أيام'],
        ],
        'متوسط' => [
            ['name' => 'تطبيق وصفات طبخ', 'desc' => 'تطبيق وصفات مع بحث وتصنيفات', 'tech' => 'Flutter', 'duration' => '2 أسابيع'],
            ['name' => 'تطبيق تتبع عادات', 'desc' => 'تتبع عاداتك اليومية مع إحصائيات', 'tech' => 'React Native', 'duration' => '2 أسابيع'],
            ['name' => 'تطبيق محادثة', 'desc' => 'تطبيق مراسلة فوري مع Firebase', 'tech' => 'Flutter, Firebase', 'duration' => '3 أسابيع'],
        ]
    ],
    'api' => [
        'مبتدئ' => [
            ['name' => 'REST API لمدونة', 'desc' => 'API كامل مع CRUD للمقالات', 'tech' => 'Node.js, Express', 'duration' => '3 أيام'],
            ['name' => 'اختصار روابط', 'desc' => 'خدمة اختصار روابط مع إحصائيات', 'tech' => 'PHP / Node.js', 'duration' => '2 أيام'],
        ],
        'متوسط' => [
            ['name' => 'API متجر إلكتروني', 'desc' => 'API كامل مع مصادقة وCRUD', 'tech' => 'Node.js, MongoDB', 'duration' => '2 أسابيع'],
            ['name' => 'نظام مصادقة JWT', 'desc' => 'نظام تسجيل متقدم مع JWT', 'tech' => 'Node.js / PHP', 'duration' => 'أسبوع'],
        ]
    ]
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $field = sanitize($_POST['field'] ?? 'web');
    $level = sanitize($_POST['level'] ?? 'مبتدئ');
    
    if (isset($ideasDB[$field][$level])) {
        $ideas = $ideasDB[$field][$level];
        // اختيار عشوائي لـ 3 أفكار
        shuffle($ideas);
        $selected = array_slice($ideas, 0, min(3, count($ideas)));
        
        $result = "🎯 أفكار مشاريع - المجال: " . ($field === 'web' ? 'ويب' : ($field === 'mobile' ? 'موبايل' : 'API')) . " | المستوى: {$level}\n";
        $result .= str_repeat('═', 50) . "\n\n";
        
        foreach ($selected as $i => $idea) {
            $result .= "📌 المشروع " . ($i + 1) . ": " . $idea['name'] . "\n";
            $result .= "   الوصف: " . $idea['desc'] . "\n";
            $result .= "   التقنيات: " . $idea['tech'] . "\n";
            $result .= "   المدة المتوقعة: " . $idea['duration'] . "\n\n";
        }
    }
    incrementToolUsage($toolId);
}

include __DIR__ . '/../includes/header.php';
?>

<div class="tool-page-header">
    <div class="tool-page-icon"><i class="fas <?= $tool['icon'] ?>"></i></div>
    <div class="tool-page-info"><h1><?= sanitize($tool['name']) ?></h1><p><?= sanitize($tool['description']) ?></p></div>
</div>

<div class="card">
    <form method="POST">
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">المجال</label>
                <select name="field" class="form-control">
                    <option value="web">تطوير الويب</option>
                    <option value="mobile">تطبيقات الموبايل</option>
                    <option value="api">API و Backend</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">المستوى</label>
                <select name="level" class="form-control">
                    <option value="مبتدئ">مبتدئ</option>
                    <option value="متوسط">متوسط</option>
                    <option value="متقدم">متقدم</option>
                </select>
            </div>
        </div>
        <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-lightbulb"></i> اقتراح أفكار</button>
    </form>
</div>

<?php if ($result): ?>
<div class="result-area show" id="resultArea">
    <div class="result-header">
        <span class="result-title"><i class="fas fa-lightbulb"></i> الأفكار جاهزة!</span>
        <div class="result-actions">
            <button class="btn btn-ghost btn-sm" onclick="copyResult()"><i class="fas fa-copy"></i> نسخ</button>
            <button type="submit" form="toolForm" class="btn btn-secondary btn-sm"><i class="fas fa-sync"></i> أفكار جديدة</button>
        </div>
    </div>
    <div class="result-content" id="resultContent"><?= $result ?></div>
</div>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
