<?php
/**
 * صفحة دليل منظومة كود التطور (Code Elta6ur Ecosystem)
 * استعراض المنصات والمشاريع التابعة للمنظومة والمطور الرئيسي
 */
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/includes/ecosystem_data.php';

$pageTitle = 'منظومة كود التطور — دليل المنصات والمشاريع التقنية المتكاملة';
$currentPage = 'ecosystem';
$seoDescription = 'استكشف منظومة كود التطور: شبكة متكاملة من المنصات التعليمية، الأدوات الذكية، الحلول البرمجية للشركات، وقواعد المعرفة العتادية برؤية المهندس محمد أبو خشريف.';
$seoKeywords = 'كود التطور, منظومة كود التطور, Code Ora, AI Portfolio Builder, هاردوير بيس, منصة البكالوريا, محمد أبو خشريف, برمجة, أدوات عربية';
$canonicalUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . ($_SERVER['HTTP_HOST'] ?? 'localhost') . BASE_URL . "ecosystem.php";

$platforms = getEcosystemPlatforms();
$leadDev = getLeadDeveloperInfo();

include __DIR__ . '/includes/header.php';
?>

<div class="container ecosystem-page-container" style="max-width:1200px;margin:0 auto;padding:1.5rem 1rem">
    
    <!-- مسار التنقل (Breadcrumbs) -->
    <nav class="tool-breadcrumbs" aria-label="مسار التنقل" style="margin-bottom:1.5rem">
        <a href="<?= BASE_URL ?>" class="crumb-link"><i class="fas fa-home"></i> الرئيسية</a>
        <span class="crumb-separator"><i class="fas fa-chevron-left"></i></span>
        <span class="crumb-current">منظومة كود التطور</span>
    </nav>

    <!-- ترويسة المنظومة (Ecosystem Hero) -->
    <section class="ecosystem-hero card" style="margin-bottom:2.5rem;text-align:center;padding:3rem 1.5rem;background:linear-gradient(180deg, rgba(108,99,255,0.08) 0%, rgba(17,24,39,0.7) 100%);border-color:rgba(108,99,255,0.2)">
        <div class="hero-badge" style="margin:0 auto 1.25rem;display:inline-flex">
            <span class="dot" style="background:#00d4ff"></span>
            <span>منظومة رقمية عربية متكاملة</span>
        </div>
        <h1 style="font-size:clamp(1.75rem, 4vw, 2.75rem);font-weight:800;margin-bottom:1rem;color:var(--text-primary)">
            منظومة <span style="background:var(--gradient-accent);-webkit-background-clip:text;-webkit-text-fill-color:transparent">كود التطور</span>
        </h1>
        <p style="max-width:760px;margin:0 auto 1.75rem;font-size:1.05rem;line-height:1.8;color:var(--text-secondary)">
            ليست مجرد موقع أدوات أو مدونة عادية، بل مظلة تقنية متعددة الأذرع تجمع بين التعليم البرمجي الرصين، الحلول التقنية المتقدمة للمؤسسات، الأدوات الذكية، وقواعد المعرفة المتخصصة.
        </p>

        <div style="display:flex;justify-content:center;gap:1.5rem;flex-wrap:wrap;color:var(--text-muted);font-size:0.9rem">
            <span><i class="fas fa-cubes text-accent"></i> 6 منصات ومشاريع رئيسية</span>
            <span><i class="fas fa-tools text-secondary"></i> +200 أداة تفاعلية</span>
            <span><i class="fas fa-user-shield text-success"></i> قيادة برمجية موحدة</span>
        </div>
    </section>

    <!-- شبكة المنصات والمشاريع -->
    <section class="ecosystem-platforms-section" style="margin-bottom:3.5rem">
        <div class="category-header" style="margin-bottom:1.75rem">
            <div class="category-icon" style="background:rgba(0,212,255,0.15);color:#00d4ff">
                <i class="fas fa-network-wired"></i>
            </div>
            <div>
                <h2 class="category-title" style="margin:0">منصات ومشاريع المنظومة</h2>
                <p style="margin:0.25rem 0 0;color:var(--text-muted);font-size:0.85rem">تكامل تخصصي يغطي البرمجة، الأعمال، التعليم، والعتاد الصلب</p>
            </div>
        </div>

        <?php renderEcosystemCards(); ?>
    </section>

    <!-- تسليط الضوء على المطور الرئيسي -->
    <section class="lead-developer-section" style="margin-bottom:3.5rem">
        <div class="category-header" style="margin-bottom:1.5rem">
            <div class="category-icon" style="background:rgba(108,99,255,0.15);color:#6c63ff">
                <i class="fas fa-laptop-code"></i>
            </div>
            <div>
                <h2 class="category-title" style="margin:0">المطور والمؤسس للمنظومة</h2>
                <p style="margin:0.25rem 0 0;color:var(--text-muted);font-size:0.85rem">الرؤية والقيادة الهندسية خلف منصات كود التطور</p>
            </div>
        </div>

        <?php renderLeadDeveloperSpotlight(); ?>
    </section>

    <!-- علاقة المنصة بباقي المنظومة -->
    <section class="ecosystem-relations card" style="margin-bottom:3rem;padding:2rem">
        <h3 style="font-size:1.2rem;font-weight:700;margin-bottom:1rem;color:var(--text-primary)">
            <i class="fas fa-project-diagram text-accent"></i> كيف تتكامل منصة الأدوات مع المنظومة؟
        </h3>
        <p style="color:var(--text-secondary);line-height:1.8;margin-bottom:1.5rem;font-size:0.95rem">
            تمثل منصة الأدوات هذه الذراع الخدمي المباشر لمنظومة كود التطور، حيث توفر أكثر من 200 حاسبة وأداة يومية تعمل بدون اتصال بسيرفر خارجي، لتكمل المحتوى التدريبي في <strong>كود التطور</strong> و <strong>Code Ora</strong>، وتتكامل مع الحلول البرمجية التي تقدمها <strong>Code Elta6ur Agency</strong> وقواعد المعرفة في <strong>HardwareBase</strong>.
        </p>

        <div style="display:flex;gap:1rem;flex-wrap:wrap">
            <a href="<?= BASE_URL ?>" class="btn btn-primary">
                <i class="fas fa-th-large"></i> تصفح جميع الأدوات والحاسبات
            </a>
            <a href="https://code-elta6ur.com" target="_blank" rel="noopener noreferrer" class="btn btn-ghost">
                <i class="fas fa-external-link-alt"></i> زيارة الموقع الرئيسي لمنظومة كود التطور
            </a>
        </div>
    </section>

</div>

<!-- بيانات Schema.org JSON-LD لدعم محركات البحث -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Organization",
  "name": "منظومة كود التطور",
  "alternateName": "Code Elta6ur Ecosystem",
  "url": "https://code-elta6ur.com",
  "founder": {
    "@type": "Person",
    "name": "محمد أبو خشريف",
    "alternateName": "Mohammad Abu Khashrif",
    "jobTitle": "Software Engineer & Founder",
    "url": "https://mohammad.code-elta6ur.com",
    "sameAs": [
      "https://github.com/mokashreef"
    ]
  },
  "department": [
    {
      "@type": "WebSite",
      "name": "كود التطور التعليمية",
      "url": "https://code-elta6ur.com"
    },
    {
      "@type": "WebApplication",
      "name": "AI Portfolio Builder",
      "url": "https://portfolio.code-elta6ur.net"
    },
    {
      "@type": "WebSite",
      "name": "Code Ora",
      "url": "https://code-ora.com"
    },
    {
      "@type": "WebSite",
      "name": "منصة البكالوريا السورية الذكية",
      "url": "https://baccalaureate.code-elta6ur.sy"
    },
    {
      "@type": "WebSite",
      "name": "Code Elta6ur Software Agency",
      "url": "https://code-elta6ur.net"
    },
    {
      "@type": "WebSite",
      "name": "هاردوير بيس HardwareBase",
      "url": "https://hardwarebase.code-elta6ur.com"
    }
  ]
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
