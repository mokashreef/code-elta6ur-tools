<?php
/**
 * أداة: تتبع وقت العمل والمشاريع
 * ساعة إيقاف وتسجيل زمني فوري لجميع المستخدمين
 * Code Elta6ur Tools
 */
require_once __DIR__ . '/../config/app.php';
include __DIR__ . '/../includes/header.php';
renderToolHeader($tool);
?>

<!-- ساعة الإيقاف المباشرة -->
<div class="card mb-2 text-center">
    <div style="font-size:0.9rem;color:var(--text-muted);margin-bottom:0.5rem">ساعة إيقاف العمل الحالية</div>
    <div id="stopwatchDisplay" style="font-size:3.5rem;font-weight:800;font-family:monospace;color:var(--text-accent-light);line-height:1.2;margin:0.5rem 0">
        00:00:00
    </div>
    
    <div class="form-group mb-2" style="max-width:400px;margin-left:auto;margin-right:auto">
        <input type="text" id="swProjectName" class="form-control text-center" placeholder="اسم المشروع الجاري العمل عليه (مثال: متجر إلكتروني)">
    </div>

    <div class="d-flex justify-between align-center flex-wrap gap-1" style="justify-content:center">
        <button type="button" class="btn btn-primary btn-lg" id="btnSwStart" onclick="startTimer()">
            <i class="fas fa-play"></i> بدء التتبع
        </button>
        <button type="button" class="btn btn-warning btn-lg" id="btnSwPause" onclick="pauseTimer()" style="display:none">
            <i class="fas fa-pause"></i> إيقاف مؤقت
        </button>
        <button type="button" class="btn btn-danger btn-lg" id="btnSwStop" onclick="stopAndSaveTimer()" style="display:none">
            <i class="fas fa-check"></i> إنهاء وحفظ
        </button>
        <button type="button" class="btn btn-ghost btn-lg" id="btnSwReset" onclick="resetTimer()" style="display:none">
            <i class="fas fa-redo"></i> تصفير
        </button>
    </div>
</div>

<!-- إضافة يدوية سريعة -->
<div class="card mb-2">
    <h3 class="section-title"><i class="fas fa-plus-circle text-accent"></i> إضافة جلسة عمل يدوياً</h3>
    <div class="form-row align-center">
        <div class="form-group" style="flex:2;margin-bottom:0">
            <input type="text" id="manualProject" class="form-control" placeholder="اسم المشروع">
        </div>
        <div class="form-group" style="flex:3;margin-bottom:0">
            <input type="text" id="manualDesc" class="form-control" placeholder="تفاصيل العمل والمهام المنجزة">
        </div>
        <div class="form-group" style="flex:1;min-width:110px;margin-bottom:0">
            <input type="number" id="manualMinutes" class="form-control" placeholder="المدة (دقيقة)" min="1" step="5">
        </div>
        <button type="button" class="btn btn-primary" onclick="addManualEntry()" style="margin-bottom:0">
            <i class="fas fa-save"></i> حفظ
        </button>
    </div>
</div>

<!-- بطاقات إجمالي الوقت المنجز -->
<div class="stats-grid mb-2">
    <div class="stat-card">
        <div class="stat-icon purple"><i class="fas fa-business-time"></i></div>
        <div>
            <div class="stat-value" id="statTotalHours">0 ساعة</div>
            <div class="stat-label">إجمالي وقت العمل</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fas fa-project-diagram"></i></div>
        <div>
            <div class="stat-value" id="statProjectsCount">0</div>
            <div class="stat-label">عدد المشاريع المنجزة</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-calendar-check"></i></div>
        <div>
            <div class="stat-value" id="statTodayHours">0 ساعة</div>
            <div class="stat-label">وقت العمل اليوم</div>
        </div>
    </div>
</div>

<!-- سجل الجلسات -->
<div class="card">
    <div class="d-flex justify-between align-center flex-wrap gap-1 mb-2">
        <h3 class="section-title mb-0"><i class="fas fa-history text-accent"></i> سجل جلسات العمل</h3>
        <div class="d-flex gap-1">
            <button type="button" class="btn btn-ghost btn-sm" onclick="exportTimeReport()"><i class="fas fa-download"></i> تصدير التقرير</button>
            <button type="button" class="btn btn-ghost btn-sm" onclick="clearAllEntries()"><i class="fas fa-trash"></i> مسح السجل</button>
        </div>
    </div>

    <div class="mobile-table-wrapper">
        <table class="w-100" style="border-collapse:collapse" id="timeTable">
            <thead>
                <tr style="background:rgba(108,99,255,0.1);border-bottom:1px solid var(--border-color)">
                    <th style="padding:0.75rem;text-align:right">المشروع</th>
                    <th style="padding:0.75rem;text-align:right">الوصف</th>
                    <th style="padding:0.75rem;text-align:center">المدة</th>
                    <th style="padding:0.75rem;text-align:center">التاريخ</th>
                    <th style="padding:0.75rem;text-align:left">إجراء</th>
                </tr>
            </thead>
            <tbody id="timeEntriesBody"></tbody>
        </table>
    </div>

    <div id="timeEmptyState" class="empty-state" style="padding:2rem 1rem;display:none">
        <i class="fas fa-clock"></i>
        <h3>لا توجد جلسات مسجلة بعد</h3>
        <p>ابدأ ساعة الإيقاف عند بدء مشروعك لتسجيل ساعات العمل بدقة.</p>
    </div>
</div>

<?php 
renderToolExplanation('فوائد تتبع ساعات العمل بدقة', [
    'تحديد التكلفة الحقيقية لساعاتك وفوترة عملائك بشفافية بناءً على الوقت الفعلي.',
    'تقييم سرعتك في إنجاز المهام لتسعير المشاريع المستقبلية بإنصاف ودون خسارة.',
    'مراقبة الوقت تمنع التسويف وتزيد التركيز بنسبة تصل إلى 40% (تقنية بومودورو).'
]);

renderRelatedTools($tool['related'] ?? ['hourly-wage-calculator', 'project-hours-calculator', 'freelancer-hourly-rate-calculator']);
renderToolScriptHelpers();
?>

<script>
let timeEntries = [];
let timerInterval = null;
let timerSeconds = 0;
let isRunning = false;

function initTimeTracker() {
    const saved = localStorage.getItem('elta6ur_time_entries');
    if (saved) {
        try { timeEntries = JSON.parse(saved); } catch(e) { timeEntries = []; }
    } else {
        timeEntries = [
            { id: 1, project: 'تطوير موقع شركة', desc: 'برمجة الواجهة الأمامية والتصميم', minutes: 120, date: '<?= date('Y-m-d') ?>' },
            { id: 2, project: 'تطبيق هاتف', desc: 'ربط واجهات الـ API والاختبار', minutes: 75, date: '<?= date('Y-m-d') ?>' }
        ];
        saveEntries();
    }
    renderEntries();
}

function saveEntries() {
    try {
        localStorage.setItem('elta6ur_time_entries', JSON.stringify(timeEntries));
    } catch(e) {}
}

function updateTimerDisplay() {
    const hrs = Math.floor(timerSeconds / 3600);
    const mins = Math.floor((timerSeconds % 3600) / 60);
    const secs = timerSeconds % 60;
    const str = `${String(hrs).padStart(2,'0')}:${String(mins).padStart(2,'0')}:${String(secs).padStart(2,'0')}`;
    document.getElementById('stopwatchDisplay').textContent = str;
}

function startTimer() {
    if (!isRunning) {
        isRunning = true;
        timerInterval = setInterval(() => {
            timerSeconds++;
            updateTimerDisplay();
        }, 1000);
        document.getElementById('btnSwStart').style.display = 'none';
        document.getElementById('btnSwPause').style.display = 'inline-flex';
        document.getElementById('btnSwStop').style.display = 'inline-flex';
        document.getElementById('btnSwReset').style.display = 'inline-flex';
    }
}

function pauseTimer() {
    if (isRunning) {
        clearInterval(timerInterval);
        isRunning = false;
        document.getElementById('btnSwStart').style.display = 'inline-flex';
        document.getElementById('btnSwStart').innerHTML = '<i class="fas fa-play"></i> استئناف';
        document.getElementById('btnSwPause').style.display = 'none';
    }
}

function stopAndSaveTimer() {
    clearInterval(timerInterval);
    isRunning = false;

    const project = document.getElementById('swProjectName').value.trim() || 'مشروع عام';
    const minutes = Math.max(1, Math.round(timerSeconds / 60));

    timeEntries.unshift({
        id: Date.now(),
        project: project,
        desc: 'جلسة عمل مسجلة عبر ساعة الإيقاف',
        minutes: minutes,
        date: new Date().toLocaleDateString('en-CA')
    });

    saveEntries();
    resetTimer();
    renderEntries();
    showToast(`تم تسجيل ${minutes} دقيقة بنجاح!`);
}

function resetTimer() {
    clearInterval(timerInterval);
    isRunning = false;
    timerSeconds = 0;
    updateTimerDisplay();
    document.getElementById('btnSwStart').style.display = 'inline-flex';
    document.getElementById('btnSwStart').innerHTML = '<i class="fas fa-play"></i> بدء التتبع';
    document.getElementById('btnSwPause').style.display = 'none';
    document.getElementById('btnSwStop').style.display = 'none';
    document.getElementById('btnSwReset').style.display = 'none';
}

function addManualEntry() {
    const project = document.getElementById('manualProject').value.trim() || 'مشروع عام';
    const desc = document.getElementById('manualDesc').value.trim() || 'جلسة عمل';
    const mins = parseInt(document.getElementById('manualMinutes').value) || 0;

    if (mins <= 0) {
        showToast('يرجى إدخال مدة صحيحة بالدقائق', 'warning');
        return;
    }

    timeEntries.unshift({
        id: Date.now(),
        project: project,
        desc: desc,
        minutes: mins,
        date: new Date().toLocaleDateString('en-CA')
    });

    saveEntries();
    document.getElementById('manualProject').value = '';
    document.getElementById('manualDesc').value = '';
    document.getElementById('manualMinutes').value = '';
    renderEntries();
    showToast('تمت إضافة جلسة العمل!');
}

function deleteEntry(id) {
    timeEntries = timeEntries.filter(e => e.id !== id);
    saveEntries();
    renderEntries();
    showToast('تم حذف الجلسة');
}

function clearAllEntries() {
    if (confirm('هل أنت متأكد من مسح جميع الجلسات المسجلة؟')) {
        timeEntries = [];
        saveEntries();
        renderEntries();
        showToast('تم مسح السجل بالكامل');
    }
}

function exportTimeReport() {
    let text = `تقرير تتبع ساعات العمل والمشاريع\nالتاريخ: ${new Date().toLocaleDateString('ar')}\n===============================\n\n`;
    let totalMins = 0;
    timeEntries.forEach((e, i) => {
        totalMins += e.minutes;
        text += `${i+1}. [${e.date}] ${e.project}: ${e.desc} (${e.minutes} دقيقة)\n`;
    });
    text += `\n===============================\n`;
    text += `إجمالي الوقت: ${(totalMins/60).toFixed(1)} ساعة (${totalMins} دقيقة)\n`;

    const blob = new Blob([text], { type: 'text/plain;charset=utf-8' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'time-tracker-report.txt';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
    showToast('تم تصدير التقرير بنجاح!');
}

function renderEntries() {
    const tbody = document.getElementById('timeEntriesBody');
    const empty = document.getElementById('timeEmptyState');
    const table = document.getElementById('timeTable');

    const totalMinutes = timeEntries.reduce((sum, e) => sum + e.minutes, 0);
    const todayDate = new Date().toLocaleDateString('en-CA');
    const todayMinutes = timeEntries.filter(e => e.date === todayDate).reduce((sum, e) => sum + e.minutes, 0);
    const uniqueProjects = new Set(timeEntries.map(e => e.project)).size;

    document.getElementById('statTotalHours').textContent = (totalMinutes / 60).toFixed(1) + ' ساعة';
    document.getElementById('statTodayHours').textContent = (todayMinutes / 60).toFixed(1) + ' ساعة';
    document.getElementById('statProjectsCount').textContent = uniqueProjects;

    if (timeEntries.length === 0) {
        tbody.innerHTML = '';
        table.style.display = 'none';
        empty.style.display = 'block';
        return;
    }

    table.style.display = 'table';
    empty.style.display = 'none';

    tbody.innerHTML = timeEntries.map(e => {
        const hrs = Math.floor(e.minutes / 60);
        const mins = e.minutes % 60;
        const durationStr = (hrs > 0 ? `${hrs} س ` : '') + (mins > 0 ? `${mins} د` : (hrs === 0 ? '0 د' : ''));

        return `
            <tr style="border-bottom:1px solid var(--border-color)">
                <td style="padding:0.75rem"><strong>${escapeHtml(e.project)}</strong></td>
                <td style="padding:0.75rem;color:var(--text-secondary)">${escapeHtml(e.desc)}</td>
                <td style="padding:0.75rem;text-align:center"><span style="color:var(--text-accent-light);font-weight:700">${durationStr}</span></td>
                <td style="padding:0.75rem;text-align:center;font-size:0.8rem;color:var(--text-muted)">${e.date}</td>
                <td style="padding:0.75rem;text-align:left">
                    <button type="button" class="btn btn-ghost btn-sm" onclick="deleteEntry(${e.id})" title="حذف" style="padding:0.25rem 0.5rem">
                        <i class="fas fa-trash text-danger"></i>
                    </button>
                </td>
            </tr>
        `;
    }).join('');
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text || '';
    return div.innerHTML;
}

document.addEventListener('DOMContentLoaded', initTimeTracker);
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
