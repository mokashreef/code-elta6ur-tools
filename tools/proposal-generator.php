<?php
/**
 * أداة: مولد رسالة تقديم للعمل الحر (Freelance Proposal)
 * Code Elta6ur Tools
 */
include __DIR__ . '/../includes/header.php';
renderToolHeader($tool);
$templates = getTemplates('proposal-generator');
?>

<div class="card mb-2">
    <div class="d-flex justify-between align-center flex-wrap gap-1 mb-2">
        <h3 class="section-title mb-0"><i class="fas fa-paper-plane text-accent"></i> تفاصيل عرض العمل الحر</h3>
        <button type="button" class="btn btn-ghost btn-sm" onclick="fillProposalSample()">
            <i class="fas fa-magic"></i> نموذج تجريبي جاهز
        </button>
    </div>

    <form id="proposalForm" onsubmit="event.preventDefault(); generateProposal();">
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">نوع ونبرة العرض</label>
                <select id="proposalTemplate" class="form-control" onchange="generateProposal()">
                    <?php foreach ($templates as $t): ?>
                    <option value="<?= $t['type'] ?>"><?= sanitize($t['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">اسمك الكامل <span class="required">*</span></label>
                <input type="text" id="propName" class="form-control" placeholder="مثال: يوسف الإبراهيم" required>
            </div>
            <div class="form-group">
                <label class="form-label">اسم العميل أو المؤسسة (إن وجد)</label>
                <input type="text" id="propClientName" class="form-control" placeholder="مثال: أستاذ عمر أو فريق العمل">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">عنوان أو هدف المشروع <span class="required">*</span></label>
            <input type="text" id="propProjectTitle" class="form-control" placeholder="مثال: تصميم وتطوير متجر ووردبريس وووكومرس متكامل" required>
        </div>

        <div class="form-group">
            <label class="form-label">لماذا أنت الخيار الأنسب؟ (نقاط القوة والقيمة المضافة) <span class="required">*</span></label>
            <textarea id="propWhyMe" class="form-control" rows="3" placeholder="مثال: امتلك خبرة 4 سنوات في تطوير المتاجر وقمت ببناء أكثر من 20 متجراً ناجحاً مع تحسين سرعة التصفح وتجربة الشراء." required></textarea>
        </div>

        <div class="form-group">
            <label class="form-label">خطة ومنهجية العمل المقترحة <span class="required">*</span></label>
            <textarea id="propWorkPlan" class="form-control" rows="3" placeholder="1. تحليل متطلبات المتجر وتحديد بوابات الدفع.&#10;2. تجهيز القالب وضبط تجربة الجوال.&#10;3. الاختبار الشامل وتدريبك على الإدارة." required></textarea>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">المدة الزمنية المتوقعة للتسليم</label>
                <input type="text" id="propDuration" class="form-control" placeholder="مثال: 7 إلى 10 أيام عمل">
            </div>
            <div class="form-group">
                <label class="form-label">الميزانية أو التكلفة المقترحة</label>
                <input type="text" id="propBudget" class="form-control" placeholder="مثال: 800 $ أو 3000 ر.س">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">روابط أعمال سابقة ذات صلة</label>
            <textarea id="propPortfolio" class="form-control" rows="2" placeholder="• متجر زهور: https://example.com/store1&#10;• متجر عطور: https://example.com/store2"></textarea>
        </div>

        <div class="d-flex gap-1 flex-wrap">
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="fas fa-check"></i> صياغة العرض
            </button>
            <button type="button" class="btn btn-ghost" onclick="clearProposalForm()">
                <i class="fas fa-redo"></i> مسح الحقول
            </button>
        </div>
    </form>
</div>

<?php renderResultArea('عرض التقديم الجاهز للإرسال', ['copy' => true, 'share' => true, 'download' => true]); ?>

<?php 
renderToolExplanation('أسرار قبول عروض العمل الحر (Freelance Proposals)', [
    'ابدأ العرض بالإشارة المباشرة لمشكلة العميل، وتجنب المقدمات الجاهزة المكررة.',
    'اذكر أمثلة وحلولاً مقترحة تثبت أنك قرأت وصف المشروع وفهمت المطلوب بدقة.',
    'أرفق فقط رابطين أو ثلاثة لأعمال سابقة مشابهة تماماً لما طلبه العميل.'
]);

renderRelatedTools($tool['related'] ?? ['cover-letter', 'cv-generator', 'freelancer-project-price-calculator']);
renderToolScriptHelpers();
?>

<script>
const PROP_TEMPLATES_DATA = <?= json_encode($templates, JSON_UNESCAPED_UNICODE) ?>;

function generateProposal() {
    const data = {
        name: document.getElementById('propName').value.trim() || 'المستقل',
        client_name: document.getElementById('propClientName').value.trim() || 'أخي الكريم',
        project_title: document.getElementById('propProjectTitle').value.trim() || 'المشروع',
        why_me: document.getElementById('propWhyMe').value.trim() || '',
        work_plan: document.getElementById('propWorkPlan').value.trim() || '',
        duration: document.getElementById('propDuration').value.trim() || 'حسب الاتفاق',
        budget: document.getElementById('propBudget').value.trim() || 'وفق الميزانية المحددة',
        portfolio_links: document.getElementById('propPortfolio').value.trim() || 'متوفرة في ملفي الشخصي'
    };

    const type = document.getElementById('proposalTemplate').value;
    const found = PROP_TEMPLATES_DATA.find(t => t.type === type);
    let template = found ? found.content : PROP_TEMPLATES_DATA[0]?.content || '';

    for (let key in data) {
        template = template.replaceAll('{{' + key + '}}', data[key]);
    }

    const words = template.trim().split(/\s+/).length;
    const statCards = [
        { icon: 'fa-envelope', label: 'الأسلوب', value: type === 'formal' ? 'رسمي' : 'عمل حر مباشر', color: 'purple' },
        { icon: 'fa-sort-numeric-up', label: 'عدد الكلمات', value: words + ' كلمة', color: 'blue' }
    ];

    showToolResult(
        `<span style="font-size:1.15rem;font-weight:700;color:var(--text-accent-light)">عرض جاهز لـ: ${escapeHtml(data.project_title)}</span>`,
        'رسالة التقديم',
        `<pre style="direction:rtl;text-align:right;white-space:pre-wrap;font-family:inherit;line-height:1.8;background:var(--bg-input);padding:1.25rem;border-radius:8px;border:1px solid var(--border-color)">${escapeHtml(template)}</pre>`,
        statCards
    );
    showToast('تم صياغة العرض بنجاح!');
}

function fillProposalSample() {
    document.getElementById('propName').value = 'عمر العلي';
    document.getElementById('propClientName').value = 'أ. عبد الله';
    document.getElementById('propProjectTitle').value = 'تطوير منصة تعليمية وربط نظام المدفوعات والشهادات';
    document.getElementById('propWhyMe').value = 'قمت بتنفيذ 8 منصات تعليمية مشابهة بنجاح، وأضمن لك منصة سريعة التصفح، محمية ضد التحميل غير المصرح للفيديوهات، وسهلة الإدارة.';
    document.getElementById('propWorkPlan').value = '1. إعداد هيكل الدورات والاختبارات وتجربة الطالب.\n2. دمج بوابات الدفع (مدى، فيزا، بايبال).\n3. ضبط الشهادات التلقائية واختبار النظام بالكامل.';
    document.getElementById('propDuration').value = '10 إلى 14 يوم عمل';
    document.getElementById('propBudget').value = '1,200 دولار';
    document.getElementById('propPortfolio').value = '• منصة أكاديمية المستقبل: https://example.com/demo1\n• منصة دورات إبداع: https://example.com/demo2';
    generateProposal();
}

function clearProposalForm() {
    document.getElementById('proposalForm').reset();
    document.getElementById('resultArea').style.display = 'none';
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text || '';
    return div.innerHTML;
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
