<?php
/**
 * أداة: مولد سيرة ذاتية احترافية (CV Generator)
 * Code Elta6ur Tools
 */
include __DIR__ . '/../includes/header.php';
renderToolHeader($tool);
$templates = getTemplates('cv-generator');
?>

<div class="card mb-2">
    <div class="d-flex justify-between align-center flex-wrap gap-1 mb-2">
        <h3 class="section-title mb-0"><i class="fas fa-user-edit text-accent"></i> بيانات السيرة الذاتية</h3>
        <button type="button" class="btn btn-ghost btn-sm" onclick="fillCvSample()">
            <i class="fas fa-magic"></i> ملء بنموذج تجريبي
        </button>
    </div>

    <form id="cvForm" onsubmit="event.preventDefault(); generateCv();">
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">نمط وقالب السيرة الذاتية</label>
                <select id="cvTemplate" class="form-control" onchange="generateCv()">
                    <?php foreach ($templates as $t): ?>
                    <option value="<?= $t['type'] ?>"><?= sanitize($t['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">الاسم الكامل <span class="required">*</span></label>
                <input type="text" id="cvName" class="form-control" placeholder="مثال: أحمد محمد خالد" required>
            </div>
            <div class="form-group">
                <label class="form-label">المسمى المهني <span class="required">*</span></label>
                <input type="text" id="cvTitle" class="form-control" placeholder="مثال: مهندس برمجيات ومطور Full-Stack" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">البريد الإلكتروني</label>
                <input type="email" id="cvEmail" class="form-control" placeholder="ahmed@example.com">
            </div>
            <div class="form-group">
                <label class="form-label">رقم الهاتف</label>
                <input type="text" id="cvPhone" class="form-control" placeholder="+966 50 123 4567">
            </div>
            <div class="form-group">
                <label class="form-label">المدينة / الدولة</label>
                <input type="text" id="cvLocation" class="form-control" placeholder="الرياض، السعودية">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">حساب LinkedIn (اختياري)</label>
                <input type="text" id="cvLinkedin" class="form-control" placeholder="linkedin.com/in/username">
            </div>
            <div class="form-group">
                <label class="form-label">حساب GitHub أو البورتفوليو (اختياري)</label>
                <input type="text" id="cvGithub" class="form-control" placeholder="github.com/username">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">الملخص المهني (Professional Summary)</label>
            <textarea id="cvSummary" class="form-control" rows="3" placeholder="نبذة موجزة تبرز سنوات خبرتك وأهم إنجازاتك وقيمتك المضافة..."></textarea>
        </div>

        <div class="form-group">
            <label class="form-label">الخبرات العملية (Work Experience)</label>
            <textarea id="cvExperience" class="form-control" rows="4" placeholder="• مطور أول في شركة التقنية (2022 - الآن): قيادة فريق البرمجة وبناء المنصة.&#10;• مطور ويب في مؤسسة الحلول (2020 - 2022): تطوير واجهات المستخدم والتطبيقات."></textarea>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">التعليم والمؤهلات (Education)</label>
                <textarea id="cvEducation" class="form-control" rows="3" placeholder="بكالوريوس علوم الحاسب - جامعة دمشق (2016 - 2020)"></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">المهارات التقنية (Skills)</label>
                <textarea id="cvSkills" class="form-control" rows="3" placeholder="PHP, Laravel, JavaScript, Vue.js, MySQL, Git, Docker, RESTful APIs"></textarea>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">أبرز المشاريع (Projects)</label>
                <textarea id="cvProjects" class="form-control" rows="3" placeholder="• منصة تجارة إلكترونية متكاملة تخدم 50 ألف مستخدم.&#10;• تطبيق إدارة المهام السحابي."></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">اللغات (Languages)</label>
                <textarea id="cvLanguages" class="form-control" rows="3" placeholder="العربية (اللغة الأم)، الإنجليزية (مستوى متقدم احترافي)"></textarea>
            </div>
        </div>

        <div class="d-flex gap-1 flex-wrap">
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="fas fa-file-contract"></i> توليد السيرة الذاتية
            </button>
            <button type="button" class="btn btn-ghost" onclick="clearCvForm()">
                <i class="fas fa-redo"></i> مسح البيانات
            </button>
        </div>
    </form>
</div>

<?php renderResultArea('السيرة الذاتية الجاهزة', ['copy' => true, 'share' => true, 'download' => true, 'print' => true]); ?>

<?php 
renderToolExplanation('نصائح لإعداد سيرة ذاتية مقنعة لمدراء التوظيف', [
    'اجعل الملخص المهني مختصراً ومركزاً على الحلول التي تقدمها للشركة.',
    'في خانة الخبرات اذكر الإنجازات بالأرقام والنسب المئوية وليس مجرد قائمة بالمهام الروتينية.',
    'تأكد من دقة وسائل الاتصال والروابط المهنية (LinkedIn / GitHub).'
], 'يتم توليد السيرة الذاتية بتنسيق Markdown متوافق يمكن نسخه أو تنزيله أو طباعته مباشرة.');

renderToolFAQ([
    ['q' => 'كيف أقدم بهذه السيرة الذاتية لأصحاب العمل؟', 'a' => 'يمكنك نسخ النص مباشرة في بريد التقديم أو تنزيل الملف، أو الضغط على زر "طباعة" لحفظه كملف PDF نظيف.'],
    ['q' => 'هل تناسب هذه السيرة الذاتية أنظمة الفرز الآلي (ATS)؟', 'a' => 'نعم، التنسيق النصي الواضح بدون أعمدة معقدة أو جداول غير قياسية هو الأمثل لجميع برامج الـ ATS.']
]);

renderRelatedTools($tool['related'] ?? ['cover-letter', 'proposal-generator', 'bio-generator']);
renderToolScriptHelpers();
?>

<script>
const CV_TEMPLATES_DATA = <?= json_encode($templates, JSON_UNESCAPED_UNICODE) ?>;

function getSelectedTemplateContent(type) {
    const found = CV_TEMPLATES_DATA.find(t => t.type === type);
    return found ? found.content : CV_TEMPLATES_DATA[0]?.content || '';
}

function generateCv() {
    const data = {
        name: document.getElementById('cvName').value.trim() || 'الاسم الكامل',
        title: document.getElementById('cvTitle').value.trim() || 'المسمى المهني',
        email: document.getElementById('cvEmail').value.trim() || 'email@example.com',
        phone: document.getElementById('cvPhone').value.trim() || '',
        location: document.getElementById('cvLocation').value.trim() || '',
        linkedin: document.getElementById('cvLinkedin').value.trim() || '',
        github: document.getElementById('cvGithub').value.trim() || '',
        summary: document.getElementById('cvSummary').value.trim() || '',
        experience: document.getElementById('cvExperience').value.trim() || '',
        education: document.getElementById('cvEducation').value.trim() || '',
        skills: document.getElementById('cvSkills').value.trim() || '',
        languages: document.getElementById('cvLanguages').value.trim() || '',
        projects: document.getElementById('cvProjects').value.trim() || ''
    };

    const type = document.getElementById('cvTemplate').value;
    let template = getSelectedTemplateContent(type);

    for (let key in data) {
        template = template.replaceAll('{{' + key + '}}', data[key]);
    }

    const words = template.trim().split(/\s+/).length;
    const statCards = [
        { icon: 'fa-file-alt', label: 'النمط', value: type, color: 'purple' },
        { icon: 'fa-sort-numeric-up', label: 'عدد الكلمات', value: words + ' كلمة', color: 'blue' }
    ];

    showToolResult(
        `<div style="font-size:1.3rem;font-weight:700;color:var(--text-accent-light);margin-bottom:0.5rem">${escapeHtml(data.name)} - ${escapeHtml(data.title)}</div>`,
        'السيرة الذاتية',
        `<pre style="direction:rtl;text-align:right;white-space:pre-wrap;font-family:inherit;line-height:1.8;background:var(--bg-input);padding:1.25rem;border-radius:8px;border:1px solid var(--border-color);max-height:550px;overflow-y:auto">${escapeHtml(template)}</pre>`,
        statCards
    );
    showToast('تم توليد السيرة الذاتية بنجاح!');
}

function fillCvSample() {
    document.getElementById('cvName').value = 'طارق زياد العمري';
    document.getElementById('cvTitle').value = 'مهندس برمجيات أول ومطور Full-Stack';
    document.getElementById('cvEmail').value = 'tariq.alomari@example.com';
    document.getElementById('cvPhone').value = '+966 55 987 6543';
    document.getElementById('cvLocation').value = 'الرياض، المملكة العربية السعودية';
    document.getElementById('cvLinkedin').value = 'linkedin.com/in/tariq-alomari';
    document.getElementById('cvGithub').value = 'github.com/tariq-dev';
    document.getElementById('cvSummary').value = 'مهندس برمجيات بخبرة تزيد عن 6 سنوات في تصميم وبناء تطبيقات الويب والمتاجر السحابية عالية الأداء. شغوف بحلول البنية التحتية، الأمان، وتجربة المستخدم الحديثة.';
    document.getElementById('cvExperience').value = '• قائد فريق تطوير البرمجيات - شركة الحلول الرقمية (2022 - الآن):\n  إدارة فريق من 5 مطورين، تقليل زمن تحميل المنصة بنسبة 45%، وبناء بنية microservices.\n\n• مطور ويب أول - منصة التجارة الذكية (2019 - 2022):\n  تطوير بوابات الدفع الإلكتروني وواجهات API متكاملة تخدم أكثر من 100 ألف طلب شهرياً.';
    document.getElementById('cvEducation').value = 'بكالوريوس هندسة البرمجيات ونظم المعلومات - بتقدير ممتاز مع مرتبة الشرف (2015 - 2019)';
    document.getElementById('cvSkills').value = 'PHP, Laravel, JavaScript, TypeScript, Node.js, Vue.js, Tailwind CSS, PostgreSQL, Redis, Docker, AWS, CI/CD, Git';
    document.getElementById('cvProjects').value = '• منصة إدارية متعددة المستأجرين (SaaS) للمنشآت المتوسطة.\n• تطبيق حاسبات مالية متقدم يخدم أكثر من 50 ألف زائر شهرياً.';
    document.getElementById('cvLanguages').value = 'العربية (اللغة الأم) | الإنجليزية (طليق باحتراف كامل C1)';
    generateCv();
}

function clearCvForm() {
    document.getElementById('cvForm').reset();
    document.getElementById('resultArea').style.display = 'none';
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text || '';
    return div.innerHTML;
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
