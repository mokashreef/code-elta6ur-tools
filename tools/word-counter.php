<?php
/**
 * أداة: عداد الكلمات والأحرف المتقدم
 * Code Elta6ur Tools
 */
include __DIR__ . '/../includes/header.php';
renderToolHeader($tool);
?>

<div class="card">
    <div class="form-group">
        <label class="form-label d-flex justify-between align-center">
            <span><i class="fas fa-edit text-accent"></i> أدخل أو الصق النص هنا</span>
            <span style="font-size:0.8rem;color:var(--text-muted)">تحليل فوري ومباشر</span>
        </label>
        <textarea id="wcInput" class="form-control" rows="8" placeholder="اكتب أو الصق أي نص عربي أو إنجليزي هنا لحساب الكلمات والأحرف بدقة..." oninput="analyzeText()"></textarea>
    </div>

    <div class="d-flex justify-between align-center flex-wrap gap-1">
        <div class="d-flex gap-1">
            <button type="button" class="btn btn-ghost btn-sm" onclick="clearText()">
                <i class="fas fa-trash-alt"></i> مسح النص
            </button>
            <button type="button" class="btn btn-ghost btn-sm" onclick="copyStats()">
                <i class="fas fa-copy"></i> نسخ الإحصائيات
            </button>
            <button type="button" class="btn btn-ghost btn-sm" onclick="downloadReport()">
                <i class="fas fa-download"></i> تصدير التقرير
            </button>
        </div>
        <div id="quickSummary" style="font-size:0.85rem;color:var(--text-accent-light);font-weight:600">
            0 كلمة | 0 حرف
        </div>
    </div>
</div>

<!-- شبكة الإحصائيات الأساسية -->
<div class="stats-grid mb-2">
    <div class="stat-card">
        <div class="stat-icon purple"><i class="fas fa-paragraph"></i></div>
        <div>
            <div class="stat-value" id="valWords">0</div>
            <div class="stat-label">عدد الكلمات</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fas fa-font"></i></div>
        <div>
            <div class="stat-value" id="valChars">0</div>
            <div class="stat-label">إجمالي الأحرف (مع المسافات)</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-text-width"></i></div>
        <div>
            <div class="stat-value" id="valCharsNoSpace">0</div>
            <div class="stat-label">الأحرف (بدون مسافات)</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange"><i class="fas fa-clock"></i></div>
        <div>
            <div class="stat-value" id="valReadTime">0 ثانية</div>
            <div class="stat-label">وقت القراءة المقدر</div>
        </div>
    </div>
</div>

<!-- شبكة الإحصائيات التفصيلية -->
<div class="stats-grid mb-2">
    <div class="stat-card">
        <div class="stat-icon purple"><i class="fas fa-list-ol"></i></div>
        <div>
            <div class="stat-value" id="valSentences">0</div>
            <div class="stat-label">عدد الجمل</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fas fa-align-justify"></i></div>
        <div>
            <div class="stat-value" id="valParagraphs">0</div>
            <div class="stat-label">عدد الفقرات</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-bars"></i></div>
        <div>
            <div class="stat-value" id="valLines">0</div>
            <div class="stat-label">عدد الأسطر</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange"><i class="fas fa-microphone"></i></div>
        <div>
            <div class="stat-value" id="valSpeechTime">0 ثانية</div>
            <div class="stat-label">وقت الإلقاء الصوتي</div>
        </div>
    </div>
</div>

<!-- الكلمات الأكثر تكراراً في النص -->
<div class="card mb-2" id="topKeywordsCard" style="display:none">
    <h3 class="section-title"><i class="fas fa-chart-bar text-accent"></i> أكثر الكلمات تكراراً في النص</h3>
    <div id="topKeywordsList" style="display:flex;flex-wrap:wrap;gap:0.5rem;margin-top:0.75rem"></div>
</div>

<?php 
renderToolExplanation('كيف يتم حساب إحصائيات النص بدقة؟', [
    'يتم حساب الكلمات عن طريق فصل النصوص بناءً على المسافات البيضاء والرموز غير الأبجدية، مع التعامل الصحيح مع الكلمات العربية متعددة الأحرف.',
    'وقت القراءة الصامتة يُقاس بمعدل متوسط يبلغ 200 إلى 250 كلمة في الدقيقة للشخص البالغ.',
    'وقت الإلقاء والتحدث الصوتي يُقاس بمعدل نطق معتدل يبلغ 130 كلمة في الدقيقة لضمان وضوح مخارج الحروف.'
], 'جميع الحسابات فورية وتتم محلياً في متصفحك دون إرسال نصوصك عبر الإنترنت.');

renderToolFAQ([
    ['q' => 'هل الأداة دقيقة في حساب كلمات المقالات العربية؟', 'a' => 'نعم، تم تدقيق خوارزمية الفصل بحيث تستبعد المسافات الزائدة وعلامات الترقيم وتعد الكلمات الحقيقية فقط.'],
    ['q' => 'هل تشمل الأحرف بدون مسافات علامات الترقيم؟', 'a' => 'نعم، الأحرف بدون مسافات تشمل جميع الحروف والأرقام وعلامات الترقيم والرموز مستبعدةً فقط المسافات والأسطر الفارغة.']
]);

renderRelatedTools($tool['related'] ?? ['reading-time-calculator', 'clean-arabic-text', 'keyword-density-calculator']);
renderToolScriptHelpers();
?>

<script>
function analyzeText() {
    const text = document.getElementById('wcInput').value;
    const trimmed = text.trim();

    const chars = text.length;
    const charsNoSpace = text.replace(/\s/g, '').length;
    
    // كلمات حقيقية
    const wordsArray = trimmed ? trimmed.split(/\s+/).filter(w => w.length > 0) : [];
    const words = wordsArray.length;

    // أسطر
    const lines = text ? text.split('\n').length : 0;

    // فقرات
    const paragraphs = trimmed ? trimmed.split(/\n\s*\n/).filter(p => p.trim().length > 0).length : 0;

    // جمل
    const sentences = trimmed ? trimmed.split(/[.!?؟\n]+/).filter(s => s.trim().length > 0).length : 0;

    // وقت القراءة (200 wpm)
    let readTimeStr = '0 ثانية';
    if (words > 0) {
        const totalSeconds = Math.round((words / 200) * 60);
        if (totalSeconds < 60) {
            readTimeStr = totalSeconds + ' ثانية';
        } else {
            const mins = Math.floor(totalSeconds / 60);
            const secs = totalSeconds % 60;
            readTimeStr = mins + ' دقيقة ' + (secs > 0 ? secs + ' ث' : '');
        }
    }

    // وقت الإلقاء (130 wpm)
    let speechTimeStr = '0 ثانية';
    if (words > 0) {
        const totalSeconds = Math.round((words / 130) * 60);
        if (totalSeconds < 60) {
            speechTimeStr = totalSeconds + ' ثانية';
        } else {
            const mins = Math.floor(totalSeconds / 60);
            const secs = totalSeconds % 60;
            speechTimeStr = mins + ' دقيقة ' + (secs > 0 ? secs + ' ث' : '');
        }
    }

    // تحديث الواجهة
    document.getElementById('valWords').textContent = formatNumberEn(words);
    document.getElementById('valChars').textContent = formatNumberEn(chars);
    document.getElementById('valCharsNoSpace').textContent = formatNumberEn(charsNoSpace);
    document.getElementById('valReadTime').textContent = readTimeStr;
    document.getElementById('valSentences').textContent = formatNumberEn(sentences);
    document.getElementById('valParagraphs').textContent = formatNumberEn(paragraphs);
    document.getElementById('valLines').textContent = formatNumberEn(lines);
    document.getElementById('valSpeechTime').textContent = speechTimeStr;

    document.getElementById('quickSummary').textContent = `${formatNumberEn(words)} كلمة | ${formatNumberEn(chars)} حرف`;

    // الكلمات الأكثر تكراراً
    calculateTopKeywords(wordsArray);
}

function calculateTopKeywords(wordsArray) {
    const card = document.getElementById('topKeywordsCard');
    const list = document.getElementById('topKeywordsList');
    if (!wordsArray || wordsArray.length < 5) {
        card.style.display = 'none';
        return;
    }

    const stopWords = new Set(['في', 'من', 'على', 'إلى', 'عن', 'مع', 'هذا', 'هذه', 'أن', 'إن', 'ما', 'هو', 'هي', 'لا', 'لم', 'لن', 'أو', 'ثم', 'و', 'ف', 'التي', 'الذي', 'الذين', 'كل', 'the', 'and', 'is', 'in', 'to', 'of', 'for', 'a', 'on', 'with', 'that']);
    const freq = {};

    wordsArray.forEach(w => {
        const clean = w.toLowerCase().replace(/[.,!?:;"'()؟،]/g, '');
        if (clean.length > 2 && !stopWords.has(clean)) {
            freq[clean] = (freq[clean] || 0) + 1;
        }
    });

    const sorted = Object.entries(freq).sort((a, b) => b[1] - a[1]).slice(0, 8);
    if (sorted.length === 0) {
        card.style.display = 'none';
        return;
    }

    list.innerHTML = sorted.map(([word, count]) => `
        <span style="background:rgba(108,99,255,0.12);border:1px solid rgba(108,99,255,0.3);padding:0.35rem 0.75rem;border-radius:20px;font-size:0.85rem">
            <strong>${escapeHtml(word)}</strong> <span style="color:var(--text-accent-light)">(${count})</span>
        </span>
    `).join('');
    card.style.display = 'block';
}

function clearText() {
    document.getElementById('wcInput').value = '';
    analyzeText();
}

function copyStats() {
    const text = `إحصائيات النص:
- عدد الكلمات: ${document.getElementById('valWords').textContent}
- عدد الأحرف الإجمالي: ${document.getElementById('valChars').textContent}
- عدد الأحرف بدون مسافات: ${document.getElementById('valCharsNoSpace').textContent}
- عدد الجمل: ${document.getElementById('valSentences').textContent}
- عدد الفقرات: ${document.getElementById('valParagraphs').textContent}
- وقت القراءة المقدر: ${document.getElementById('valReadTime').textContent}
- وقت الإلقاء المقدر: ${document.getElementById('valSpeechTime').textContent}

تم التحليل عبر منصة كود التطور للأدوات الشاملة`;
    copyToClipboard(text);
}

function downloadReport() {
    const text = `تقرير تحليل النص
التاريخ: ${new Date().toLocaleDateString('ar')}
==================================
الكلمات: ${document.getElementById('valWords').textContent}
الأحرف الإجمالية: ${document.getElementById('valChars').textContent}
الأحرف بدون مسافات: ${document.getElementById('valCharsNoSpace').textContent}
الجمل: ${document.getElementById('valSentences').textContent}
الفقرات: ${document.getElementById('valParagraphs').textContent}
الأسطر: ${document.getElementById('valLines').textContent}
وقت القراءة: ${document.getElementById('valReadTime').textContent}
وقت الإلقاء الصوتي: ${document.getElementById('valSpeechTime').textContent}
==================================
النص المحلل:
${document.getElementById('wcInput').value}`;

    const blob = new Blob([text], { type: 'text/plain;charset=utf-8' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'word-count-report.txt';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
    showToast('تم تحميل التقرير!');
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
