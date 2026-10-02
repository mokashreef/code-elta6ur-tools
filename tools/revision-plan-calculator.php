<?php
/**
 * أداة: حاسبة خطة المراجعة الذكية (التكرار المتباعد Spaced Repetition)
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'revision-plan-calculator';
$tool = getToolBySlug($slug);
if (!$tool) {
    redirect('index.php');
}

include __DIR__ . '/../includes/header.php';
?>

<div class="container tool-container">
    <?php renderToolHeader($tool); ?>

    <div class="tool-content-grid">
        <!-- قسم إدخال البيانات -->
        <div class="tool-card card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-sliders-h text-accent"></i> أدخل البيانات المطلوبة</h3>
            </div>
            <div class="card-body">
    <div class="form-group">
        <label class="form-label" for="studyDateInput">تاريخ اليوم الذي درست فيه الدرس لأول مرة</label>
        <input type="text" id="studyDateInput" class="form-control" value="2026-10-02"    placeholder="YYYY-MM-DD" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="topicName">اسم الدرس أو الفصل</label>
        <input type="text" id="topicName" class="form-control" value="الفصل الأول - مقدمة المادة"    placeholder="اسم الموضوع" oninput="calculateTool()">
    </div>

                <div class="tool-actions-bar" style="display:flex;gap:0.75rem;margin-top:1.5rem;flex-wrap:wrap">
                    <button type="button" class="btn btn-primary btn-lg" style="flex:1" onclick="calculateTool()">
                        <i class="fas fa-calculator"></i> احسب الآن
                    </button>
                    <button type="button" class="btn btn-ghost" onclick="resetToolInputs()">
                        <i class="fas fa-undo"></i> إعادة تعيين
                    </button>
                </div>
            </div>
        </div>

        <!-- قسم عرض النتيجة -->
        <div class="tool-result-wrapper">
            <?php renderResultArea('ملخص الحساب والنتائج', ['copy' => true, 'share' => true, 'download' => true, 'print' => true]); ?>
        </div>
    </div>

    <!-- قسم الشرح والمعادلات -->
    <?php 
    renderToolExplanation(
        'طريقة الحساب والمعادلات المستخدمة',
        array (
  0 => 'أثبت العالم إبنجهاوس أن الإنسان ينسى حوالي 70% مما تعلمه بعد 24 ساعة فقط إذا لم يراجعه.',
  1 => 'المراجعة على فترات متصاعدة (يوم 1، يوم 3، يوم 7، يوم 14، يوم 30) تعيد قوة الذاكرة إلى 100% بأقل مجهود زمني ممكن.',
),
        'يفترض استخدام الاسترجاع النشط (Active Recall) بدلاً من مجرد إعادة القراءة السلبية.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'ما هو الاسترجاع النشط (Active Recall)؟',
    'a' => 'هو محاولة تذكر المعلومات واختبار نفسك من الذاكرة وإغلاق الكتاب، بدلاً من إعادة قراءة النص المظلل بالألوان، وهو الأسلوب الأقوى عالمياً لتثبيت المعلومات.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'memorization-time-calculator',
  1 => 'daily-study-hours-calculator',
  2 => 'exam-countdown-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const dateStr = document.getElementById('studyDateInput').value;
            const topic = document.getElementById('topicName').value || 'الموضوع';

            const baseDate = new Date(dateStr);
            if (isNaN(baseDate.getTime())) {
                alert('يرجى إدخال تاريخ صحيح بالصيغة YYYY-MM-DD');
                return;
            }

            const intervals = [
                { name: 'المراجعة 1 (تثبيت فوري)', days: 1, desc: 'مراجعة سريعة لمدة 10 دقائق لتثبيت الذاكرة الأولية' },
                { name: 'المراجعة 2 (كسر منحنى النسيان)', days: 3, desc: 'استرجاع نشط وحل 3 أسئلة على الدرس' },
                { name: 'المراجعة 3 (نقل للذاكرة طويلة المدى)', days: 7, desc: 'مراجعة خريطة المفاهيم والنقاط الرئيسية' },
                { name: 'المراجعة 4 (ترسيخ عميق)', days: 14, desc: 'حل أسئلة امتحانات سابقة بدون النظر للكتاب' },
                { name: 'المراجعة 5 (تثبيت دائم)', days: 30, desc: 'استرجاع شامل قبل الامتحان النهائي' }
            ];

            let scheduleHtml = '<div style="display:flex;flex-direction:column;gap:0.75rem;margin-top:1rem">';
            intervals.forEach((item, idx) => {
                const revDate = new Date(baseDate);
                revDate.setDate(revDate.getDate() + item.days);
                const dateFormatted = revDate.toLocaleDateString('ar-EG', { weekday: 'short', month: 'short', day: 'numeric' });
                scheduleHtml += `
                    <div style="background:var(--bg-glass);padding:0.75rem 1rem;border-radius:var(--radius-md);border-right:4px solid var(--primary)">
                        <div style="display:flex;justify-content:space-between;align-items:center;font-weight:600">
                            <span>${item.name} (+ ${item.days} أيام)</span>
                            <span style="color:var(--text-accent-light)">${dateFormatted}</span>
                        </div>
                        <div style="font-size:0.85rem;color:var(--text-secondary);margin-top:0.25rem">${item.desc}</div>
                    </div>
                `;
            });
            scheduleHtml += '</div>';

            setPrimaryResult('جدول 5 مراجعات ذكية مبني على منحنى النسيان (Ebbinghaus)', 'جدول المراجعة المتباعدة');
            showResultArea();

            setDetailStats([
                { label: 'نسبة الحفظ المتوقعة بعد 30 يوماً', value: '92% استرجاع ممتاز ⭐', color: '#10b981' },
                { label: 'وقت المراجعة الواحدة المقترح', value: '10 إلى 15 دقيقة فقط', color: '#3b82f6' },
                { label: 'الموضوع المراد مراجعته', value: topic, color: '#f59e0b' },
                { label: 'الأساس العلمي المعتمد', value: 'منحنى النسيان لإبنجهاوس', color: '#8b5cf6' }
            ]);

            setResultContent(scheduleHtml);
        
        saveLastInputs('revision-plan-calculator');
    } catch (e) {
        console.error('Calculation error:', e);
    }
}

function resetToolInputs() {
    document.querySelectorAll('.tool-card input, .tool-card textarea').forEach(el => {
        if (el.defaultValue !== undefined) el.value = el.defaultValue;
    });
    calculateTool();
}

function onCurrencyChange() {
    calculateTool();
}

// تنفيذ الحساب تلقائياً عند تحميل الصفحة واسترجاع المدخلات المحفوظة
document.addEventListener('DOMContentLoaded', () => {
    restoreLastInputs('revision-plan-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>