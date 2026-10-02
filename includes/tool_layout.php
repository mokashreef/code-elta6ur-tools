<?php
/**
 * المكونات البصرية المشتركة لصفحات الأدوات (Tool Architecture Components)
 * يوفر مظهراً موحداً، كوداً قابلاً لإعادة الاستخدام، وتوافقاً كاملاً مع Mobile First و RTL
 * Code Elta6ur Tools
 */

if (!defined('APP_NAME')) {
    require_once __DIR__ . '/../config/app.php';
}
require_once __DIR__ . '/tools_registry.php';

/**
 * عرض رأس الأداة الموحد مع مسار التصفح (Breadcrumb)
 */
function renderToolHeader($tool) {
    $categoryName = getCategoryName($tool['category']);
    ?>
    <div class="tool-breadcrumb">
        <a href="<?= BASE_URL ?>"><i class="fas fa-home"></i> الرئيسية</a>
        <i class="fas fa-chevron-left divider"></i>
        <span><?= sanitize($categoryName) ?></span>
        <i class="fas fa-chevron-left divider"></i>
        <span class="current"><?= sanitize($tool['name']) ?></span>
    </div>

    <div class="tool-page-header">
        <div class="tool-page-icon">
            <i class="fas <?= $tool['icon'] ?>"></i>
        </div>
        <div class="tool-page-info">
            <h1><?= sanitize($tool['name']) ?></h1>
            <p><?= sanitize($tool['description']) ?></p>
        </div>
    </div>
    <?php
}

/**
 * عرض محدد العملات العربية والعالمية الموحد
 */
function renderCurrencySelector($id = 'calcCurrency', $default = 'SAR', $label = 'العملة') {
    $currencies = getSupportedCurrencies();
    ?>
    <div class="form-group currency-select-group">
        <?php if ($label): ?>
        <label class="form-label" for="<?= $id ?>">
            <i class="fas fa-coins text-accent"></i> <?= sanitize($label) ?>
        </label>
        <?php endif; ?>
        <select id="<?= $id ?>" class="form-control currency-select" onchange="if(window.onCurrencyChange) window.onCurrencyChange(this.value)">
            <?php foreach ($currencies as $code => $c): ?>
            <option value="<?= $code ?>" <?= $code === $default ? 'selected' : '' ?>>
                <?= $c['name'] ?> (<?= $c['symbol'] ?> - <?= $code ?>)
            </option>
            <?php endforeach; ?>
        </select>
    </div>
    <?php
}

/**
 * عرض بطاقة النتائج الموحدة
 */
function renderResultArea($title = 'النتيجة', $actions = ['copy' => true, 'share' => true, 'download' => true, 'print' => false]) {
    ?>
    <div class="result-area" id="resultArea" style="display:none">
        <div class="result-header">
            <span class="result-title">
                <i class="fas fa-check-circle" style="color:var(--text-accent-light)"></i> 
                <span id="resultTitleText"><?= sanitize($title) ?></span>
            </span>
            <div class="result-actions">
                <?php if (!empty($actions['copy'])): ?>
                <button type="button" class="btn btn-ghost btn-sm" id="btnCopyResult" onclick="copyToolResult(this)" title="نسخ النتيجة">
                    <i class="fas fa-copy"></i> <span>نسخ</span>
                </button>
                <?php endif; ?>
                <?php if (!empty($actions['share'])): ?>
                <button type="button" class="btn btn-ghost btn-sm" id="btnShareResult" onclick="shareToolResult()" title="مشاركة">
                    <i class="fas fa-share-alt"></i> <span>مشاركة</span>
                </button>
                <?php endif; ?>
                <?php if (!empty($actions['download'])): ?>
                <button type="button" class="btn btn-ghost btn-sm" id="btnDownloadResult" onclick="downloadToolResult()" title="تنزيل النتيجة كملف">
                    <i class="fas fa-download"></i> <span>تحميل</span>
                </button>
                <?php endif; ?>
                <?php if (!empty($actions['print'])): ?>
                <button type="button" class="btn btn-ghost btn-sm" onclick="window.print()" title="طباعة">
                    <i class="fas fa-print"></i> <span>طباعة</span>
                </button>
                <?php endif; ?>
            </div>
        </div>

        <!-- ملخص النتيجة الأبرز -->
        <div class="result-primary-box" id="resultPrimaryBox">
            <div class="result-primary-value" id="resultPrimaryValue">0</div>
            <div class="result-primary-label" id="resultPrimaryLabel">النتيجة الإجمالية</div>
        </div>

        <!-- تفاصيل النتيجة التفصيلية أو النص -->
        <div class="result-content" id="resultContent"></div>

        <!-- بطاقات الإحصائيات الفرعية -->
        <div class="result-details-grid stats-grid" id="resultDetailsGrid"></div>
    </div>
    <?php
}

/**
 * عرض شرح طريقة الحساب والافتراضات
 */
function renderToolExplanation($title = 'طريقة الحساب والمعادلات المستخدمة', $points = [], $assumptions = '') {
    if (empty($points) && empty($assumptions)) return;
    ?>
    <div class="card tool-info-card">
        <h3 class="section-title"><i class="fas fa-lightbulb text-accent"></i> <?= sanitize($title) ?></h3>
        <?php if (!empty($points)): ?>
        <ul class="tool-explanation-list">
            <?php foreach ($points as $p): ?>
            <li><i class="fas fa-angle-left"></i> <?= $p ?></li>
            <?php endforeach; ?>
        </ul>
        <?php endif; ?>
        <?php if (!empty($assumptions)): ?>
        <div class="tool-assumptions-notice">
            <i class="fas fa-info-circle"></i>
            <span><strong>ملاحظة:</strong> <?= sanitize($assumptions) ?></span>
        </div>
        <?php endif; ?>
    </div>
    <?php
}

/**
 * عرض قسم الأسئلة الشائعة مع Structured Data
 */
function renderToolFAQ($faqs = []) {
    if (empty($faqs)) return;
    ?>
    <div class="card tool-faq-card">
        <h3 class="section-title"><i class="fas fa-question-circle text-accent"></i> الأسئلة الشائعة حول هذه الأداة</h3>
        <div class="faq-accordion">
            <?php foreach ($faqs as $idx => $faq): ?>
            <div class="faq-item <?= $idx === 0 ? 'active' : '' ?>">
                <button type="button" class="faq-question" onclick="this.parentElement.classList.toggle('active')">
                    <span><?= sanitize($faq['q']) ?></span>
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="faq-answer">
                    <p><?= $faq['a'] ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Structured Data JSON-LD for SEO -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "FAQPage",
      "mainEntity": [
        <?php foreach ($faqs as $i => $faq): ?>
        {
          "@type": "Question",
          "name": <?= json_encode($faq['q'], JSON_UNESCAPED_UNICODE) ?>,
          "acceptedAnswer": {
            "@type": "Answer",
            "text": <?= json_encode(strip_tags($faq['a']), JSON_UNESCAPED_UNICODE) ?>
          }
        }<?= $i < count($faqs) - 1 ? ',' : '' ?>
        <?php endforeach; ?>
      ]
    }
    </script>
    <?php
}

/**
 * عرض الأدوات ذات الصلة
 */
function renderRelatedTools($relatedSlugs = []) {
    if (empty($relatedSlugs)) return;
    $tools = [];
    foreach ($relatedSlugs as $slug) {
        $t = getToolBySlug($slug);
        if ($t) $tools[] = $t;
    }
    if (empty($tools)) return;
    ?>
    <div class="related-tools-section">
        <h3 class="section-title"><i class="fas fa-th-large text-accent"></i> أدوات ذات صلة قد تهمك</h3>
        <div class="tools-grid-compact">
            <?php foreach ($tools as $t): ?>
            <a href="<?= BASE_URL ?>tool.php?slug=<?= $t['slug'] ?>" class="tool-card-compact">
                <div class="tool-card-icon-compact"><i class="fas <?= $t['icon'] ?>"></i></div>
                <div class="tool-card-info-compact">
                    <h4><?= sanitize($t['name']) ?></h4>
                    <p><?= sanitize($t['description']) ?></p>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
}

/**
 * دوال JavaScript المشتركة للأدوات
 */
function renderToolScriptHelpers() {
    ?>
    <script>
    // العملات والرموز
    const CURRENCY_SYMBOLS = {
        'SAR': 'ر.س', 'AED': 'د.إ', 'QAR': 'ر.ق', 'KWD': 'د.ك', 'BHD': 'د.ب',
        'OMR': 'ر.ع', 'JOD': 'د.أ', 'EGP': 'ج.م', 'SYP': 'ل.س', 'IQD': 'د.ع',
        'LBP': 'ل.ل', 'YER': 'ر.ي', 'DZD': 'د.ج', 'MAD': 'د.م', 'TND': 'د.ت',
        'LYD': 'د.ل', 'SDG': 'ج.س', 'TRY': '₺', 'USD': '$', 'EUR': '€', 'GBP': '£'
    };

    function getSelectedCurrency(id = 'calcCurrency') {
        const el = document.getElementById(id);
        return el ? el.value : 'SAR';
    }

    function getCurrencySymbol(code) {
        return CURRENCY_SYMBOLS[code] || code;
    }

    function formatNumber(val, decimals = 2) {
        if (isNaN(val) || val === null || val === undefined) return '0';
        return Number(val).toLocaleString('ar-EG', {
            minimumFractionDigits: 0,
            maximumFractionDigits: decimals
        });
    }

    function formatNumberEn(val, decimals = 2) {
        if (isNaN(val) || val === null || val === undefined) return '0';
        return Number(val).toLocaleString('en-US', {
            minimumFractionDigits: 0,
            maximumFractionDigits: decimals
        });
    }

    function formatMoney(amount, currencyCode = null) {
        const code = currencyCode || getSelectedCurrency();
        const symbol = getCurrencySymbol(code);
        return `${formatNumber(amount)} ${symbol}`;
    }

    function setPrimaryResult(val, label = '') {
        const valEl = document.getElementById('resultPrimaryValue');
        const lblEl = document.getElementById('resultPrimaryLabel');
        if (valEl) valEl.innerHTML = val;
        if (lblEl && label) lblEl.textContent = label;
    }

    function showResultArea() {
        const area = document.getElementById('resultArea');
        if (area) {
            area.style.display = 'block';
            area.classList.add('show');
        }
    }

    function setDetailStats(stats = []) {
        const gridEl = document.getElementById('resultDetailsGrid');
        if (!gridEl) return;
        if (stats && stats.length > 0) {
            gridEl.innerHTML = stats.map(s => `
                <div class="stat-card">
                    <div class="stat-icon" style="background:rgba(255,255,255,0.05);color:${s.color || '#6c63ff'}">
                        <i class="fas ${s.icon || 'fa-info-circle'}"></i>
                    </div>
                    <div>
                        <div class="stat-value" style="color:${s.color || 'var(--text-primary)'}">${s.value}</div>
                        <div class="stat-label">${s.label}</div>
                    </div>
                </div>
            `).join('');
            gridEl.style.display = 'grid';
        } else {
            gridEl.innerHTML = '';
            gridEl.style.display = 'none';
        }
    }

    function setResultText(html = '') {
        const contentEl = document.getElementById('resultContent');
        if (contentEl) {
            contentEl.innerHTML = html;
            contentEl.style.display = html ? 'block' : 'none';
        }
    }

    function setResultContent(html = '') {
        setResultText(html);
    }

    function showToolResult(primaryVal, primaryLabel, detailsHtml = '', statCards = []) {
        setPrimaryResult(primaryVal, primaryLabel);
        setResultText(detailsHtml);
        setDetailStats(statCards);
        showResultArea();
    }

    function copyToolResult(btn) {
        const primaryVal = document.getElementById('resultPrimaryValue')?.textContent || '';
        const primaryLbl = document.getElementById('resultPrimaryLabel')?.textContent || '';
        const contentText = document.getElementById('resultContent')?.innerText || '';
        
        let fullText = `${primaryLbl}: ${primaryVal}\n`;
        if (contentText.trim()) {
            fullText += `\n${contentText}\n`;
        }

        // تفاصيل البطاقات إن وجدت
        document.querySelectorAll('#resultDetailsGrid .stat-card').forEach(card => {
            const lbl = card.querySelector('.stat-label')?.textContent || '';
            const val = card.querySelector('.stat-value')?.textContent || '';
            if (lbl && val) fullText += `${lbl}: ${val}\n`;
        });

        fullText += `\nتم الحساب بواسطة ${document.title} - كود التطور`;

        copyToClipboard(fullText, btn);
    }

    function shareToolResult() {
        const primaryVal = document.getElementById('resultPrimaryValue')?.textContent || '';
        const primaryLbl = document.getElementById('resultPrimaryLabel')?.textContent || '';
        const shareData = {
            title: document.title,
            text: `${primaryLbl}: ${primaryVal} - ${document.title}`,
            url: window.location.href
        };

        if (navigator.share) {
            navigator.share(shareData).catch(() => {});
        } else {
            copyToClipboard(window.location.href);
            showToast('تم نسخ رابط الأداة للمشاركة!');
        }
    }

    function downloadToolResult(filename) {
        const primaryVal = document.getElementById('resultPrimaryValue')?.textContent || '';
        const primaryLbl = document.getElementById('resultPrimaryLabel')?.textContent || '';
        const contentText = document.getElementById('resultContent')?.innerText || '';
        
        let fullText = `====================================\n`;
        fullText += `${document.title}\n`;
        fullText += `التاريخ: ${new Date().toLocaleDateString('ar')}\n`;
        fullText += `====================================\n\n`;
        fullText += `${primaryLbl}: ${primaryVal}\n\n`;
        
        if (contentText.trim()) {
            fullText += `التفاصيل:\n${contentText}\n\n`;
        }

        document.querySelectorAll('#resultDetailsGrid .stat-card').forEach(card => {
            const lbl = card.querySelector('.stat-label')?.textContent || '';
            const val = card.querySelector('.stat-value')?.textContent || '';
            if (lbl && val) fullText += `- ${lbl}: ${val}\n`;
        });

        fullText += `\nرابط الأداة: ${window.location.href}\n`;

        const blob = new Blob([fullText], { type: 'text/plain;charset=utf-8' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = (filename || 'tool-result') + '.txt';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
        showToast('تم تحميل النتيجة بنجاح!');
    }

    // حفظ واسترجاع مدخلات النموذج
    function saveLastInputs(toolKey) {
        try {
            const inputs = {};
            document.querySelectorAll('.tool-card input, .tool-card select, .tool-card textarea').forEach(el => {
                if (el.id) inputs[el.id] = el.value;
            });
            localStorage.setItem('tool_input_' + toolKey, JSON.stringify(inputs));
        } catch(e) {}
    }

    function restoreLastInputs(toolKey) {
        try {
            const saved = localStorage.getItem('tool_input_' + toolKey);
            if (!saved) return;
            const inputs = JSON.parse(saved);
            for (const [id, val] of Object.entries(inputs)) {
                const el = document.getElementById(id);
                if (el && val !== undefined) {
                    el.value = val;
                }
            }
        } catch(e) {}
    }

    function saveToolStorage(toolKey, data) {
        try {
            localStorage.setItem('tool_' + toolKey, JSON.stringify(data));
        } catch(e) {}
    }

    function loadToolStorage(toolKey) {
        try {
            const saved = localStorage.getItem('tool_' + toolKey);
            return saved ? JSON.parse(saved) : null;
        } catch(e) {
            return null;
        }
    }
    </script>
    <?php
}
