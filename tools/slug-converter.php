<?php
/**
 * أداة: تحويل النص إلى Slug (عربي وإنجليزي)
 * Code Elta6ur Tools
 */
include __DIR__ . '/../includes/header.php';
renderToolHeader($tool);
?>

<div class="card">
    <div class="form-group">
        <label class="form-label" for="slugInput">
            <i class="fas fa-heading text-accent"></i> أدخل النص أو عنوان المقال
        </label>
        <input type="text" id="slugInput" class="form-control" placeholder="مثال: دليلك الشامل لتعلم البرمجة باللغة العربية 2026" oninput="generateSlug()">
    </div>

    <div class="form-row">
        <div class="form-group">
            <label class="form-label">نوع الرابط (Slug Type)</label>
            <select id="slugType" class="form-control" onchange="generateSlug()">
                <option value="arabic">سلاج عربي بالأحرف العربية (موصى به للمحتوى العربي)</option>
                <option value="latin">سلاج إنجليزي صوتي (Transliterated Latin)</option>
            </select>
        </div>
        <div class="form-group">
            <label class="form-label">رمز الفاصل (Separator)</label>
            <select id="slugSeparator" class="form-control" onchange="generateSlug()">
                <option value="-">شرطة عادية (-)</option>
                <option value="_">شرطة سفلية (_)</option>
                <option value=".">نقطة (.)</option>
            </select>
        </div>
    </div>

    <div class="d-flex gap-2 flex-wrap mb-2">
        <label style="display:flex;align-items:center;gap:0.5rem;font-size:0.85rem;cursor:pointer">
            <input type="checkbox" id="optLower" checked onchange="generateSlug()">
            <span>تحويل الأحرف الإنجليزية لحالة صغيرة (lowercase)</span>
        </label>
        <label style="display:flex;align-items:center;gap:0.5rem;font-size:0.85rem;cursor:pointer">
            <input type="checkbox" id="optRemoveStopWords" onchange="generateSlug()">
            <span>إزالة حروف الجر وأدوات الربط الشائعة (في، من، على...)</span>
        </label>
    </div>

    <div class="d-flex gap-1">
        <button type="button" class="btn btn-primary" onclick="generateSlug()">
            <i class="fas fa-link"></i> توليد الرابط
        </button>
        <button type="button" class="btn btn-ghost" onclick="clearSlug()">
            <i class="fas fa-eraser"></i> مسح
        </button>
    </div>
</div>

<?php renderResultArea('الرابط المولد (SEO Friendly Slug)', ['copy' => true, 'share' => true, 'download' => true]); ?>

<?php 
renderToolExplanation('أهمية الرابط اللطيف (Slug) لمحركات البحث (SEO)', [
    'الـ Slug هو الجزء الأخير في عنوان URL الذي يحدد الصفحة أو المقال بلغة واضحة يفهمها المستخدم ومحرك البحث.',
    'الروابط العربية بالكلمات المفتاحية تعزز ثقة القارئ وتزيد من نسبة النقر (CTR) في نتائج بحث Google.',
    'تقوم هذه الأداة بحذف علامات الترقيم، الرموز الغريبة، والمسافات الزائدة واستبدالها بفواصل قياسية نظيفة.'
], 'لا يؤثر تحويل النص إلى رابط على الكلمات الأصلية، وتتم المعالجة بالكامل محلياً وبسرعة فائقة.');

renderToolFAQ([
    ['q' => 'أيهما أفضل للسيو: الرابط العربي أم الإنجليزي؟', 'a' => 'للمحتوى العربي الخالص، يُفضل الرابط العربي المعبر الذي يحتوي على الكلمة المفتاحية، بينما الرابط الإنجليزي يكون أسهل في النسخ والمشاركة عبر التطبيقات القديمة.'],
    ['q' => 'هل تدعم الأداة حذف علامات التشكيل؟', 'a' => 'نعم، تزيل الأداة الحركات والتشكيل والتطويل (ـ) تلقائياً لضمان سلامة الرابط وعدم حدوث أي خطأ في المتصفحات.']
]);

renderRelatedTools($tool['related'] ?? ['arabic-english-slug-generator', 'url-encode', 'clean-arabic-text']);
renderToolScriptHelpers();
?>

<script>
function generateSlug() {
    const input = document.getElementById('slugInput').value.trim();
    const type = document.getElementById('slugType').value;
    const sep = document.getElementById('slugSeparator').value;
    const lower = document.getElementById('optLower').checked;
    const removeStop = document.getElementById('optRemoveStopWords').checked;

    if (!input) {
        document.getElementById('resultArea').style.display = 'none';
        return;
    }

    let text = input;

    // إزالة التشكيل
    text = text.replace(/[\u064B-\u065F\u0670]/g, '');

    // إزالة التطويل (الكشيدة)
    text = text.replace(/ـ+/g, '');

    // إزالة كلمات الوقف إن تم التحديد
    if (removeStop) {
        const stopWords = ['في', 'من', 'على', 'إلى', 'عن', 'مع', 'هذا', 'هذه', 'أن', 'إن', 'ما', 'هو', 'هي', 'و', 'أو', 'ثم'];
        const words = text.split(/\s+/);
        text = words.filter(w => !stopWords.includes(w)).join(' ');
    }

    let slug = '';
    if (type === 'arabic') {
        // الحفاظ على الحروف العربية والأرقام والإنجليزية
        slug = text
            .replace(/[^\u0621-\u064A\u0660-\u0669a-zA-Z0-9\s_-]/g, '')
            .trim();
        if (lower) slug = slug.toLowerCase();
        slug = slug.replace(/[\s_-]+/g, sep);
    } else {
        // تحويل صوتي للاتينية
        const arMap = {
            'ا':'a','أ':'a','إ':'i','آ':'a','ء':'','ؤ':'w','ئ':'y','ى':'a','ة':'h',
            'ب':'b','ت':'t','ث':'th','ج':'j','ح':'h','خ':'kh','د':'d','ذ':'dh',
            'ر':'r','ز':'z','س':'s','ش':'sh','ص':'s','ض':'d','ط':'t','ظ':'dh',
            'ع':'a','غ':'gh','ف':'f','ق':'q','ك':'k','ل':'l','م':'m','ن':'n',
            'ه':'h','و':'w','ي':'y','٠':'0','١':'1','٢':'2','٣':'3','٤':'4','٥':'5','٦':'6','٧':'7','٨':'8','٩':'9'
        };
        let translit = '';
        for (let char of text) {
            translit += (arMap[char] !== undefined) ? arMap[char] : char;
        }
        slug = translit.replace(/[^a-zA-Z0-9\s_-]/g, '').trim();
        if (lower) slug = slug.toLowerCase();
        slug = slug.replace(/[\s_-]+/g, sep);
    }

    // تنظيف الفواصل في البداية والنهاية
    const sepEscaped = sep.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    slug = slug.replace(new RegExp(`^${sepEscaped}+|${sepEscaped}+$`, 'g'), '');

    if (!slug) slug = 'slug';

    const statCards = [
        { icon: 'fa-text-width', label: 'طول الرابط', value: slug.length + ' حرف', color: 'purple' },
        { icon: 'fa-globe', label: 'النوع', value: type === 'arabic' ? 'عربي نقي' : 'صوتي لاتيني', color: 'blue' }
    ];

    showToolResult(
        `<span style="direction:ltr;text-align:left;display:inline-block;font-family:monospace;font-size:1.15rem;word-break:break-all">${escapeHtml(slug)}</span>`,
        'الرابط المناسب للسيو (Slug)',
        `<p style="direction:rtl;margin-top:0.5rem;font-size:0.85rem;color:var(--text-secondary)">مثال في الموقع: <code>https://example.com/blog/${escapeHtml(slug)}</code></p>`,
        statCards
    );
}

function clearSlug() {
    document.getElementById('slugInput').value = '';
    document.getElementById('resultArea').style.display = 'none';
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
