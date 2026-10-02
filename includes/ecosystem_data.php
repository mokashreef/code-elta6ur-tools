<?php
/**
 * بيانات ودليل منظومة كود التطور (Code Elta6ur Ecosystem)
 * تعريف المنصات والمشاريع التابعة والمطور الرئيسي
 */

if (!defined('APP_NAME')) {
    require_once __DIR__ . '/../config/app.php';
}

/**
 * جلب قائمة المنصات والمشاريع المعتمدة ضمن المنظومة
 */
function getEcosystemPlatforms() {
    return [
        'main' => [
            'id' => 'main',
            'name' => 'كود التطور — المنصة الرئيسية',
            'tagline' => 'المنصة التعليمية الشاملة لتعليم البرمجة وتطوير الويب',
            'description' => 'المركز الرئيسي لمنظومة كود التطور، يقدم مسارات تعليمية ومقالات متخصصة وشروحات تطبيقية في البرمجة وتطوير الويب ومواكبة الذكاء الاصطناعي للمطورين والمبتدئين.',
            'url' => 'https://code-elta6ur.com',
            'badge' => 'المنصة الرئيسية',
            'badge_color' => 'purple',
            'category' => 'تعليم البرمجة والذكاء الاصطناعي',
            'icon' => 'fa-graduation-cap',
            'accent' => '#6c63ff',
            'cta_text' => 'زيارة الموقع الرئيسي'
        ],
        'portfolio_builder' => [
            'id' => 'portfolio_builder',
            'name' => 'AI Portfolio Builder',
            'tagline' => 'منشئ معارض الأعمال والمواقع الشخصية الذكي',
            'description' => 'أداة ذكية متخصصة لبناء وإطلاق المواقع الشخصية ومعارض الأعمال المهنية للمطورين والمصممين في دقائق معدودة وبدون كتابة كود، مع قوالب متجاوبة ومعاينة فورية.',
            'url' => 'https://portfolio.code-elta6ur.net',
            'badge' => 'أداة ذكية',
            'badge_color' => 'cyan',
            'category' => 'أدوات الويب والمعارض الشخصية',
            'icon' => 'fa-id-card',
            'accent' => '#00d4ff',
            'cta_text' => 'إنشاء معرض أعمالك'
        ],
        'code_ora' => [
            'id' => 'code_ora',
            'name' => 'Code Ora (كود اورا)',
            'tagline' => 'أكاديمية برمجية تفاعلية ودورات متقدمة',
            'description' => 'منصة رائدة لتعليم البرمجة وتطوير مهارات المطورين العرب، تضم مسارات تدريبية، دورات تخصصية، اختبارات تقنية تفاعلية، ومساعداً ذكياً موجهاً للتأهيل لسوق العمل.',
            'url' => 'https://code-ora.com',
            'badge' => 'دورات وتدريب تفاعلي',
            'badge_color' => 'blue',
            'category' => 'دورات تدريبية واختبارات برمجية',
            'icon' => 'fa-laptop-code',
            'accent' => '#3b82f6',
            'cta_text' => 'استكشاف كود اورا'
        ],
        'baccalaureate' => [
            'id' => 'baccalaureate',
            'name' => 'منصة البكالوريا السورية الذكية',
            'tagline' => 'الذراع الأكاديمي الذكي لطلاب الثانوية',
            'description' => 'منصة تعليمية ذكية ومتخصصة لطلاب الشهادة الثانوية (البكالوريا)، توفر أدوات دراسية مبتكرة، ملخصات مركزة، واختبارات لتمكين الطلاب من التفوق الدراسي.',
            'url' => 'https://baccalaureate.code-elta6ur.sy',
            'badge' => 'منصة تعليمية مدرسية',
            'badge_color' => 'green',
            'category' => 'التعليم الأكاديمي والمدرسي',
            'icon' => 'fa-book-reader',
            'accent' => '#10b981',
            'cta_text' => 'دخول منصة البكالوريا'
        ],
        'software_agency' => [
            'id' => 'software_agency',
            'name' => 'كود التطور للحلول البرمجية (Code Elta6ur Agency)',
            'tagline' => 'وكالة البرمجيات وحلول الأعمال السحابية',
            'description' => 'الذراع التقني المخصص لتقديم الخدمات البرمجية وهندسة الحلول الرقمية، وتطوير تطبيقات الويب السحابية (SaaS)، والمتاجر الإلكترونية، وتكامل الأنظمة للشركات ورواد الأعمال.',
            'url' => 'https://code-elta6ur.net',
            'badge' => 'حلول برمجية ومؤسسية',
            'badge_color' => 'purple',
            'category' => 'تطوير البرمجيات وحلول الأعمال',
            'icon' => 'fa-cubes',
            'accent' => '#8b5cf6',
            'cta_text' => 'استعراض الخدمات البرمجية'
        ],
        'hardwarebase' => [
            'id' => 'hardwarebase',
            'name' => 'هاردوير بيس (HardwareBase)',
            'tagline' => 'قاعدة المعرفة الشاملة للهاردوير وصيانة الأجهزة',
            'description' => 'قاعدة معرفة عتادية متخصصة وشاملة للهواتف والإلكترونيات والقطع، توفر تشخيص الأعطال الشائعة، ومخططات الدوائر المتكاملة (ICs)، ودليل الصيانة العتادية للمهندسين والفنيين.',
            'url' => 'https://hardwarebase.code-elta6ur.com',
            'badge' => 'قاعدة معرفة عتادية',
            'badge_color' => 'orange',
            'category' => 'العتاد الصلب وصيانة الإلكترونيات',
            'icon' => 'fa-microchip',
            'accent' => '#f59e0b',
            'cta_text' => 'تصفح هاردوير بيس'
        ]
    ];
}

/**
 * بيانات المطور الرئيسي وصاحب المنظومة
 */
function getLeadDeveloperInfo() {
    return [
        'name' => 'محمد أبو خشريف',
        'english_name' => 'Mohammad Abu Khashrif',
        'title' => 'مهندس برمجيات | مؤسس منظومة كود التطور',
        'role' => 'المطور الرئيسي وصاحب معرض الأعمال',
        'bio' => 'مهندس برمجيات ومطور Full-Stack ومؤسس منظومة كود التطور. يركز على ابتكار منتجات تقنية حقيقية، كتابة كود نظيف ومستدام، وتقديم حلول برمجية عملية تخدم المجتمع العربي والشركات.',
        'portfolio_url' => 'https://mohammad.code-elta6ur.com',
        'github_url' => 'https://github.com/mokashreef',
        'github_username' => 'mokashreef',
        'avatar_initials' => 'م.خ',
        'services' => [
            'هندسة البرمجيات والأنظمة السحابية',
            'تطوير تطبيقات الويب الكاملة (Full-Stack)',
            'بناء المنصات الرقمية المتكاملة',
            'استشارات وحلول الأعمال البرمجية'
        ]
    ];
}

/**
 * عرض بطاقات منصات المنظومة
 */
function renderEcosystemCards($limit = null) {
    $platforms = getEcosystemPlatforms();
    if ($limit !== null) {
        $platforms = array_slice($platforms, 0, $limit);
    }
    ?>
    <div class="ecosystem-grid">
        <?php foreach ($platforms as $platform): ?>
        <article class="ecosystem-card" style="--card-accent: <?= $platform['accent'] ?>">
            <div class="ecosystem-card-header">
                <div class="ecosystem-icon" style="color: <?= $platform['accent'] ?>; border-color: rgba(<?= hexToRgb($platform['accent']) ?>, 0.2); background: rgba(<?= hexToRgb($platform['accent']) ?>, 0.1);">
                    <i class="fas <?= $platform['icon'] ?>"></i>
                </div>
                <div class="ecosystem-badge-wrapper">
                    <span class="ecosystem-badge" style="color: <?= $platform['accent'] ?>; background: rgba(<?= hexToRgb($platform['accent']) ?>, 0.12); border-color: rgba(<?= hexToRgb($platform['accent']) ?>, 0.25);">
                        <?= $platform['badge'] ?>
                    </span>
                </div>
            </div>

            <div class="ecosystem-card-body">
                <h3 class="ecosystem-title"><?= sanitize($platform['name']) ?></h3>
                <div class="ecosystem-tagline"><?= sanitize($platform['tagline']) ?></div>
                <p class="ecosystem-desc"><?= sanitize($platform['description']) ?></p>
            </div>

            <div class="ecosystem-card-footer">
                <span class="ecosystem-category">
                    <i class="fas fa-folder-open"></i> <?= $platform['category'] ?>
                </span>
                <a href="<?= $platform['url'] ?>" target="_blank" rel="noopener noreferrer" class="ecosystem-link-btn" style="--btn-accent: <?= $platform['accent'] ?>">
                    <span><?= $platform['cta_text'] ?></span>
                    <i class="fas fa-arrow-left"></i>
                </a>
            </div>
        </article>
        <?php endforeach; ?>
    </div>
    <?php
}

/**
 * بطاقة تعريف المطور الرئيسي للمنظومة
 */
function renderLeadDeveloperSpotlight() {
    $dev = getLeadDeveloperInfo();
    ?>
    <div class="developer-spotlight-card">
        <div class="developer-spotlight-header">
            <div class="developer-avatar-wrapper">
                <div class="developer-avatar">
                    <i class="fas fa-user-tie"></i>
                </div>
                <span class="status-indicator" title="نشط ومتاح للعمل"></span>
            </div>
            <div class="developer-main-info">
                <div class="developer-role-tag"><i class="fas fa-crown text-warning"></i> <?= $dev['role'] ?></div>
                <h3 class="developer-name"><?= $dev['name'] ?> <span class="developer-en-name">(<?= $dev['english_name'] ?>)</span></h3>
                <p class="developer-title"><?= $dev['title'] ?></p>
            </div>
        </div>

        <p class="developer-bio"><?= $dev['bio'] ?></p>

        <div class="developer-services-tags">
            <?php foreach ($dev['services'] as $service): ?>
            <span class="dev-service-badge"><i class="fas fa-check-circle"></i> <?= $service ?></span>
            <?php endforeach; ?>
        </div>

        <div class="developer-actions">
            <a href="<?= $dev['portfolio_url'] ?>" target="_blank" rel="noopener noreferrer" class="btn btn-primary">
                <i class="fas fa-briefcase"></i>
                <span>استعراض معرض الأعمال (Portfolio)</span>
                <i class="fas fa-external-link-alt" style="font-size:0.75rem;margin-right:0.25rem"></i>
            </a>
            <a href="<?= $dev['github_url'] ?>" target="_blank" rel="noopener noreferrer" class="btn btn-ghost">
                <i class="fab fa-github"></i>
                <span>مشاريع GitHub البرمجية</span>
                <i class="fas fa-external-link-alt" style="font-size:0.75rem;margin-right:0.25rem"></i>
            </a>
        </div>
    </div>
    <?php
}

/**
 * دالة مساعدة لتحويل Hex إلى RGB
 */
if (!function_exists('hexToRgb')) {
    function hexToRgb($hex) {
        $hex = str_replace('#', '', $hex);
        if (strlen($hex) == 3) {
            $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
            $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
            $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
        } else {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }
        return "$r, $g, $b";
    }
}
