<?php
/**
 * الصفحة الرئيسية - منصة الأدوات الشاملة
 * Code Elta6ur Tools
 */
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/includes/ecosystem_data.php';

$pageTitle = 'الرئيسية - دليل الأدوات الشاملة';
$currentPage = 'home';
$seoDescription = 'منصة عربية شاملة تضم أكثر من 200 أداة وحاسبة عملية مجانية: حاسبات مالية، بناء وتشطيب، طاقة شمسية وكهرباء، سيارات وسفر، تعليم، نصوص، وأدوات المطورين.';
$seoKeywords = 'أدوات مجانية, حاسبة رواتب, حاسبة كهرباء, طاقة شمسية, حاسبة دهان, حاسبة معدل, منسق json, عداد كلمات';

$categories = getAppCategories();

try {
    $allTools = getAllTools();
    $toolsByCategory = [];
    $featuredTools = [];
    $popularTools = [];

    foreach ($allTools as $tool) {
        $toolsByCategory[$tool['category']][] = $tool;
        if (!empty($tool['featured'])) {
            $featuredTools[] = $tool;
        }
        if (!empty($tool['popular'])) {
            $popularTools[] = $tool;
        }
    }
    
    // المفضلات للمستخدم المسجل
    $userFavorites = [];
    if (isLoggedIn()) {
        $favs = getFavorites($_SESSION['user_id']);
        foreach ($favs as $f) {
            $userFavorites[] = $f['id'];
        }
    }
} catch (Exception $e) {
    $allTools = [];
    $toolsByCategory = [];
    $featuredTools = [];
    $popularTools = [];
    $userFavorites = [];
}

include __DIR__ . '/includes/header.php';
?>

<!-- Hero Section -->
<section class="hero home-hero">
    <div class="hero-badge">
        <span class="dot"></span>
        <span><?= count($allTools) ?> أداة عملية مجانية ومباشرة</span>
    </div>
    <h1>منصة الأدوات <span>العربية الشاملة</span></h1>
    <p>أدوات وحاسبات دقيقة ومجانية تخدم الموظفين، أصحاب المشاريع، الطلاب، المهندسين، والمطورين بدون تعقيد.</p>

    <!-- بحث هيرو السريع والمباشر -->
    <div class="hero-search-wrapper">
        <div class="hero-search-box">
            <i class="fas fa-search hero-search-icon"></i>
            <input type="text" id="homeSearchInput" class="hero-search-input" placeholder="ابحث باسم الأداة أو الفكرة (مثال: راتب، كهرباء، طاقة شمسية، دهان، معدل، JSON)..." autocomplete="off">
            <button type="button" class="hero-search-clear" id="heroSearchClear" onclick="clearHomeSearch()" style="display:none">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="hero-search-tags">
            <span class="tag-title">عمليات بحث شائعة:</span>
            <button type="button" class="search-tag-btn" onclick="filterBySearch('راتب')">الرواتب</button>
            <button type="button" class="search-tag-btn" onclick="filterBySearch('كهرباء')">الكهرباء</button>
            <button type="button" class="search-tag-btn" onclick="filterBySearch('طاقة شمسية')">الطاقة الشمسية</button>
            <button type="button" class="search-tag-btn" onclick="filterBySearch('دهان')">الدهان والبناء</button>
            <button type="button" class="search-tag-btn" onclick="filterBySearch('معدل')">المعدل التراكمي</button>
            <button type="button" class="search-tag-btn" onclick="filterBySearch('json')">JSON</button>
        </div>
    </div>
</section>

<!-- شريط فلترة التصنيفات السريع (Mobile Touch Friendly) -->
<div class="category-filter-bar" id="categoryFilterBar">
    <button type="button" class="cat-filter-btn active" data-cat="all" onclick="filterCategory('all', this)">
        <i class="fas fa-th-large"></i>
        <span>جميع الأدوات (<?= count($allTools) ?>)</span>
    </button>
    <button type="button" class="cat-filter-btn" data-cat="featured" onclick="filterCategory('featured', this)">
        <i class="fas fa-star text-warning"></i>
        <span>المميزة (<?= count($featuredTools) ?>)</span>
    </button>
    <?php foreach ($categories as $catKey => $catInfo): ?>
    <?php if (isset($toolsByCategory[$catKey]) && count($toolsByCategory[$catKey]) > 0): ?>
    <button type="button" class="cat-filter-btn" data-cat="<?= $catKey ?>" onclick="filterCategory('<?= $catKey ?>', this)">
        <i class="fas <?= $catInfo['icon'] ?>"></i>
        <span><?= $catInfo['short_name'] ?? $catInfo['name'] ?> (<?= count($toolsByCategory[$catKey]) ?>)</span>
    </button>
    <?php endif; ?>
    <?php endforeach; ?>
</div>

<!-- حاوية رسالة لا توجد نتائج بحث -->
<div id="searchNoResults" class="empty-state" style="display:none;margin:2rem 0">
    <i class="fas fa-search"></i>
    <h3>لم يتم العثور على أداة مطابقة</h3>
    <p>جرب البحث بكلمات أخرى أو تصفح التصنيفات في الأعلى</p>
    <button type="button" class="btn btn-primary" onclick="clearHomeSearch()" style="margin-top:1rem">
        عرض جميع الأدوات
    </button>
</div>

<!-- قسم الأدوات المميزة -->
<section class="tools-section featured-section" id="featuredSection" style="margin-bottom:2.5rem">
    <div class="category-header">
        <div class="category-icon" style="background:rgba(245,158,11,0.15);color:#f59e0b">
            <i class="fas fa-star"></i>
        </div>
        <h2 class="category-title">الأدوات الأكثر استخداماً والمميزة</h2>
        <span class="category-count"><?= count($featuredTools) ?> أداة</span>
    </div>
    <div class="tools-grid">
        <?php foreach ($featuredTools as $tool): ?>
        <a href="<?= BASE_URL ?>tool.php?slug=<?= $tool['slug'] ?>" class="tool-card tool-card-item" data-category="<?= $tool['category'] ?>" data-slug="<?= $tool['slug'] ?>" data-name="<?= htmlspecialchars($tool['name']) ?>" data-keywords="<?= htmlspecialchars(implode(' ', $tool['keywords'] ?? [])) ?>" id="tool-<?= $tool['slug'] ?>">
            <div class="tool-card-icon">
                <i class="fas <?= $tool['icon'] ?>"></i>
            </div>
            <h3 class="tool-card-title"><?= sanitize($tool['name']) ?></h3>
            <p class="tool-card-desc"><?= sanitize($tool['description']) ?></p>
            <div class="tool-card-footer">
                <span class="tool-card-category"><i class="fas <?= getCategoryIcon($tool['category']) ?>"></i> <?= getCategoryName($tool['category']) ?></span>
                <?php if (isLoggedIn()): ?>
                <button class="tool-card-fav <?= in_array($tool['id'], $userFavorites) ? 'active' : '' ?>" 
                        onclick="event.preventDefault(); event.stopPropagation(); toggleFavorite(<?= $tool['id'] ?>, this)">
                    <i class="<?= in_array($tool['id'], $userFavorites) ? 'fas' : 'far' ?> fa-heart"></i>
                </button>
                <?php endif; ?>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
</section>

<!-- جميع الأدوات مقسمة حسب الفئات -->
<div id="allCategoriesContainer">
<?php foreach ($categories as $catKey => $catInfo): ?>
<?php if (isset($toolsByCategory[$catKey]) && count($toolsByCategory[$catKey]) > 0): ?>
<section class="tools-category-block" id="category-block-<?= $catKey ?>" data-cat="<?= $catKey ?>" style="margin-bottom:3rem">
    <div class="category-header">
        <div class="category-icon">
            <i class="fas <?= $catInfo['icon'] ?>"></i>
        </div>
        <h2 class="category-title"><?= $catInfo['name'] ?></h2>
        <span class="category-count"><?= count($toolsByCategory[$catKey]) ?> أداة</span>
    </div>

    <div class="tools-grid">
        <?php foreach ($toolsByCategory[$catKey] as $tool): ?>
        <a href="<?= BASE_URL ?>tool.php?slug=<?= $tool['slug'] ?>" class="tool-card tool-card-item" data-category="<?= $tool['category'] ?>" data-slug="<?= $tool['slug'] ?>" data-name="<?= htmlspecialchars($tool['name']) ?>" data-keywords="<?= htmlspecialchars(implode(' ', $tool['keywords'] ?? [])) ?>" id="tool-<?= $tool['slug'] ?>">
            <div class="tool-card-icon">
                <i class="fas <?= $tool['icon'] ?>"></i>
            </div>
            <h3 class="tool-card-title"><?= sanitize($tool['name']) ?></h3>
            <p class="tool-card-desc"><?= sanitize($tool['description']) ?></p>
            <div class="tool-card-footer">
                <span class="tool-card-category"><?= $catInfo['short_name'] ?? $catInfo['name'] ?></span>
                <?php if (isLoggedIn()): ?>
                <button class="tool-card-fav <?= in_array($tool['id'], $userFavorites) ? 'active' : '' ?>" 
                        onclick="event.preventDefault(); event.stopPropagation(); toggleFavorite(<?= $tool['id'] ?>, this)">
                    <i class="<?= in_array($tool['id'], $userFavorites) ? 'fas' : 'far' ?> fa-heart"></i>
                </button>
                <?php endif; ?>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>
<?php endforeach; ?>
</div>

<!-- ============================================
     قسم منظومة كود التطور (Code Elta6ur Ecosystem)
     ============================================ -->
<section class="ecosystem-home-section" id="ecosystemSection" style="margin-top:4rem;margin-bottom:3rem">
    <div class="category-header" style="justify-content:space-between;align-items:flex-end;flex-wrap:wrap;gap:1rem;margin-bottom:2rem">
        <div style="display:flex;align-items:center;gap:0.75rem">
            <div class="category-icon" style="background:rgba(108,99,255,0.15);color:#6c63ff">
                <i class="fas fa-network-wired"></i>
            </div>
            <div>
                <h2 class="category-title" style="margin:0">منظومة كود التطور — منصات ومشاريع متكاملة</h2>
                <p style="margin:0.35rem 0 0;color:var(--text-secondary);font-size:0.875rem">
                    منظومة تقنية عربية شاملة تجمع بين التعليم البرمجي، الحلول المؤسسية، الأدوات الذكية، وقواعد المعرفة العتادية
                </p>
            </div>
        </div>
        <a href="<?= BASE_URL ?>ecosystem.php" class="btn btn-ghost btn-sm" style="font-size:0.85rem">
            <span>استعراض دليل المنظومة بالكامل</span>
            <i class="fas fa-arrow-left"></i>
        </a>
    </div>

    <!-- شبكة بطاقات المنصات -->
    <?php renderEcosystemCards(); ?>

    <!-- بطاقة المطور والمؤسس -->
    <div style="margin-top:2.5rem">
        <?php renderLeadDeveloperSpotlight(); ?>
    </div>
</section>

<script>
// تصفية الأدوات حسب التصنيف أو البحث الفوري
let currentActiveCat = 'all';

function filterCategory(catKey, btn) {
    currentActiveCat = catKey;
    document.querySelectorAll('.cat-filter-btn').forEach(b => b.classList.remove('active'));
    if (btn) btn.classList.add('active');

    const featuredSec = document.getElementById('featuredSection');
    const catBlocks = document.querySelectorAll('.tools-category-block');

    if (catKey === 'all') {
        if (featuredSec) featuredSec.style.display = 'block';
        catBlocks.forEach(b => b.style.display = 'block');
    } else if (catKey === 'featured') {
        if (featuredSec) featuredSec.style.display = 'block';
        catBlocks.forEach(b => b.style.display = 'none');
    } else {
        if (featuredSec) featuredSec.style.display = 'none';
        catBlocks.forEach(b => {
            b.style.display = (b.dataset.cat === catKey) ? 'block' : 'none';
        });
    }

    // إعادة ضبط حقل البحث
    const searchInput = document.getElementById('homeSearchInput');
    if (searchInput && searchInput.value.trim()) {
        performLiveFilter();
    }
}

function filterBySearch(query) {
    const input = document.getElementById('homeSearchInput');
    if (input) {
        input.value = query;
        performLiveFilter();
        input.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
}

function clearHomeSearch() {
    const input = document.getElementById('homeSearchInput');
    if (input) {
        input.value = '';
        performLiveFilter();
    }
}

function performLiveFilter() {
    const input = document.getElementById('homeSearchInput');
    const query = (input ? input.value : '').trim().toLowerCase();
    const clearBtn = document.getElementById('heroSearchClear');
    if (clearBtn) clearBtn.style.display = query ? 'block' : 'none';

    const cards = document.querySelectorAll('.tool-card-item');
    const noResults = document.getElementById('searchNoResults');
    const featuredSec = document.getElementById('featuredSection');
    const catBlocks = document.querySelectorAll('.tools-category-block');

    if (!query) {
        // العودة للوضع الطبيعي حسب التصنيف النشط
        filterCategory(currentActiveCat);
        cards.forEach(c => c.style.display = '');
        if (noResults) noResults.style.display = 'none';
        return;
    }

    // بحث فوري
    let visibleCount = 0;
    cards.forEach(card => {
        const name = (card.dataset.name || '').toLowerCase();
        const desc = (card.querySelector('.tool-card-desc')?.textContent || '').toLowerCase();
        const keywords = (card.dataset.keywords || '').toLowerCase();
        const slug = (card.dataset.slug || '').toLowerCase();

        const match = name.includes(query) || desc.includes(query) || keywords.includes(query) || slug.includes(query);
        if (match) {
            card.style.display = '';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });

    // إخفاء الأقسام الفارغة
    catBlocks.forEach(block => {
        const hasVisible = Array.from(block.querySelectorAll('.tool-card-item')).some(c => c.style.display !== 'none');
        block.style.display = hasVisible ? 'block' : 'none';
    });

    if (featuredSec) {
        const hasVisible = Array.from(featuredSec.querySelectorAll('.tool-card-item')).some(c => c.style.display !== 'none');
        featuredSec.style.display = hasVisible ? 'block' : 'none';
    }

    if (noResults) {
        noResults.style.display = (visibleCount === 0) ? 'block' : 'none';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('homeSearchInput');
    if (input) {
        input.addEventListener('input', performLiveFilter);
    }
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
