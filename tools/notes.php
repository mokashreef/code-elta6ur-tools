<?php
/**
 * أداة: الملاحظات السريعة والمفكرة
 * تعمل محلياً وفورياً لجميع المستخدمين مع التزامن
 * Code Elta6ur Tools
 */
require_once __DIR__ . '/../config/app.php';
include __DIR__ . '/../includes/header.php';
renderToolHeader($tool);
?>

<div class="card mb-2">
    <div class="form-group">
        <label class="form-label"><i class="fas fa-pen text-accent"></i> عنوان الملاحظة</label>
        <input type="text" id="noteTitle" class="form-control" placeholder="عنوان الملاحظة...">
    </div>
    <div class="form-group">
        <label class="form-label"><i class="fas fa-align-right text-accent"></i> محتوى الملاحظة</label>
        <textarea id="noteContent" class="form-control" rows="4" placeholder="اكتب أفكارك وملاحظاتك هنا..."></textarea>
    </div>
    <div class="d-flex justify-between align-center flex-wrap gap-1">
        <div class="d-flex align-center gap-1">
            <span style="font-size:0.85rem;color:var(--text-secondary)">لون الملاحظة:</span>
            <input type="color" id="noteColor" value="#6c63ff" style="width:38px;height:34px;border:none;cursor:pointer;border-radius:6px;background:none">
        </div>
        <div class="d-flex gap-1">
            <button type="button" class="btn btn-primary" onclick="saveNote()">
                <i class="fas fa-save"></i> <span id="btnSaveText">حفظ الملاحظة</span>
            </button>
            <button type="button" class="btn btn-ghost" onclick="resetNoteForm()">
                <i class="fas fa-times"></i> إلغاء
            </button>
        </div>
    </div>
</div>

<!-- شريط البحث السريع في الملاحظات -->
<div class="d-flex justify-between align-center flex-wrap gap-1 mb-2">
    <div class="search-box" style="flex:1;max-width:360px">
        <i class="fas fa-search"></i>
        <input type="text" id="searchNotesInput" placeholder="ابحث في ملاحظاتك..." oninput="searchNotes()" class="form-control">
    </div>
    <div class="d-flex gap-1">
        <button type="button" class="btn btn-ghost btn-sm" onclick="exportAllNotes()">
            <i class="fas fa-download"></i> تصدير الكل
        </button>
        <button type="button" class="btn btn-ghost btn-sm" onclick="clearAllNotes()">
            <i class="fas fa-trash"></i> مسح الكل
        </button>
    </div>
</div>

<!-- شبكة بطاقات الملاحظات -->
<div id="notesGrid" class="notes-grid"></div>

<div id="notesEmptyState" class="empty-state" style="padding:2.5rem 1rem;display:none">
    <i class="fas fa-sticky-note"></i>
    <h3>لا توجد ملاحظات بعد</h3>
    <p>ابدأ بتدوين أول فكرة أو ملاحظة باستخدام النموذج في الأعلى.</p>
</div>

<style>
.notes-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1rem;
}
.note-card {
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: var(--border-radius);
    padding: 1.25rem;
    position: relative;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    transition: all var(--transition);
}
.note-card:hover {
    border-color: var(--border-color-hover);
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
}
.note-card.is-pinned {
    border-color: var(--text-accent);
    background: linear-gradient(135deg, rgba(108, 99, 255, 0.05) 0%, rgba(17, 24, 39, 0.8) 100%);
}
.note-pin-btn {
    position: absolute;
    top: 1rem;
    left: 1rem;
    background: none;
    border: none;
    color: var(--text-muted);
    cursor: pointer;
    font-size: 0.95rem;
    transition: all var(--transition-fast);
}
.note-pin-btn.active {
    color: var(--text-accent);
}
.note-card-title {
    font-size: 1rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
    padding-left: 1.5rem;
}
.note-card-body {
    font-size: 0.875rem;
    color: var(--text-secondary);
    line-height: 1.6;
    white-space: pre-wrap;
    word-break: break-word;
    margin-bottom: 1rem;
    flex: 1;
}
.note-card-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-top: 1px solid var(--border-color);
    padding-top: 0.75rem;
    font-size: 0.75rem;
    color: var(--text-muted);
}
</style>

<?php 
renderToolExplanation('تنظيم الأفكار وتدوين الملاحظات', [
    'استخدم الألوان لتصنيف الملاحظات (أفكار عمل، مهام عائلية، مراجع).',
    'خاصية التثبيت (Pin) تجعل الملاحظات الهامة تظهر دائماً في بداية القائمة.',
    'تدوين الأفكار فور ورودها يفرغ الذاكرة المؤقتة للعقل ويزيد من قدرتك على التركيز.'
]);

renderRelatedTools($tool['related'] ?? ['todo-list', 'time-tracker', 'word-counter']);
renderToolScriptHelpers();
?>

<script>
let notes = [];
let editingNoteId = null;

function initNotes() {
    const saved = localStorage.getItem('elta6ur_notes');
    if (saved) {
        try { notes = JSON.parse(saved); } catch(e) { notes = []; }
    } else {
        notes = [
            {
                id: 1,
                title: 'مرحباً بك في مفكرة كود التطور 📝',
                content: 'يمكنك حفظ الأفكار السريعة، تلوينها، وتثبيتها للأعلى والرجوع إليها في أي وقت.',
                color: '#6c63ff',
                pinned: true,
                date: '<?= date('Y/m/d') ?>'
            }
        ];
        saveNotesToLocal();
    }
    renderNotesList();
}

function saveNotesToLocal() {
    try {
        localStorage.setItem('elta6ur_notes', JSON.stringify(notes));
    } catch(e) {}
}

function saveNote() {
    const title = document.getElementById('noteTitle').value.trim() || 'ملاحظة بدون عنوان';
    const content = document.getElementById('noteContent').value.trim();
    const color = document.getElementById('noteColor').value;

    if (!content && title === 'ملاحظة بدون عنوان') {
        showToast('يرجى كتابة محتوى للملاحظة', 'warning');
        return;
    }

    if (editingNoteId) {
        const n = notes.find(item => item.id === editingNoteId);
        if (n) {
            n.title = title;
            n.content = content;
            n.color = color;
            n.date = new Date().toLocaleDateString('ar');
        }
        editingNoteId = null;
        document.getElementById('btnSaveText').textContent = 'حفظ الملاحظة';
        showToast('تم تحديث الملاحظة بنجاح!');
    } else {
        const newNote = {
            id: Date.now(),
            title: title,
            content: content,
            color: color,
            pinned: false,
            date: new Date().toLocaleDateString('ar')
        };
        notes.unshift(newNote);
        showToast('تمت إضافة الملاحظة بنجاح!');
    }

    saveNotesToLocal();
    resetNoteForm();
    renderNotesList();
}

function editNote(id) {
    const n = notes.find(item => item.id === id);
    if (!n) return;

    editingNoteId = id;
    document.getElementById('noteTitle').value = n.title;
    document.getElementById('noteContent').value = n.content;
    document.getElementById('noteColor').value = n.color || '#6c63ff';
    document.getElementById('btnSaveText').textContent = 'تحديث الملاحظة';
    document.getElementById('noteTitle').focus();
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function resetNoteForm() {
    editingNoteId = null;
    document.getElementById('noteTitle').value = '';
    document.getElementById('noteContent').value = '';
    document.getElementById('noteColor').value = '#6c63ff';
    document.getElementById('btnSaveText').textContent = 'حفظ الملاحظة';
}

function togglePin(id) {
    const n = notes.find(item => item.id === id);
    if (n) {
        n.pinned = !n.pinned;
        // وضع المثبتة في الأول
        notes.sort((a, b) => (b.pinned ? 1 : 0) - (a.pinned ? 1 : 0));
        saveNotesToLocal();
        renderNotesList();
    }
}

function deleteNote(id) {
    notes = notes.filter(item => item.id !== id);
    saveNotesToLocal();
    renderNotesList();
    showToast('تم حذف الملاحظة');
}

function searchNotes() {
    const q = document.getElementById('searchNotesInput').value.trim().toLowerCase();
    renderNotesList(q);
}

function clearAllNotes() {
    if (confirm('هل أنت متأكد من مسح جميع الملاحظات؟')) {
        notes = [];
        saveNotesToLocal();
        renderNotesList();
        showToast('تم مسح جميع الملاحظات');
    }
}

function exportAllNotes() {
    let text = `ملاحظاتي ومذكراتي - منصة كود التطور\nالتاريخ: ${new Date().toLocaleDateString('ar')}\n===============================\n\n`;
    notes.forEach((n, i) => {
        text += `[${i+1}] ${n.title} (${n.date})\n${n.content}\n-------------------------------\n\n`;
    });
    const blob = new Blob([text], { type: 'text/plain;charset=utf-8' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'my-notes.txt';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
    showToast('تم تصدير جميع الملاحظات بنجاح!');
}

function renderNotesList(query = '') {
    const grid = document.getElementById('notesGrid');
    const empty = document.getElementById('notesEmptyState');

    let filtered = notes;
    if (query) {
        filtered = notes.filter(n => n.title.toLowerCase().includes(query) || n.content.toLowerCase().includes(query));
    }

    if (filtered.length === 0) {
        grid.innerHTML = '';
        empty.style.display = 'block';
        return;
    }

    empty.style.display = 'none';
    grid.innerHTML = filtered.map(n => `
        <div class="note-card ${n.pinned ? 'is-pinned' : ''}" style="border-right: 4px solid ${n.color || '#6c63ff'}">
            <button type="button" class="note-pin-btn ${n.pinned ? 'active' : ''}" onclick="togglePin(${n.id})" title="${n.pinned ? 'إلغاء التثبيت' : 'تثبيت للأعلى'}">
                <i class="fas fa-thumbtack"></i>
            </button>
            <div>
                <h4 class="note-card-title">${escapeHtml(n.title)}</h4>
                <div class="note-card-body">${escapeHtml(n.content)}</div>
            </div>
            <div class="note-card-footer">
                <span>${n.date || ''}</span>
                <div class="d-flex gap-1">
                    <button type="button" class="btn btn-ghost btn-sm" onclick="copyToClipboard('${escapeJs(n.content)}', this)" title="نسخ" style="padding:0.25rem 0.5rem">
                        <i class="fas fa-copy"></i>
                    </button>
                    <button type="button" class="btn btn-ghost btn-sm" onclick="editNote(${n.id})" title="تعديل" style="padding:0.25rem 0.5rem">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button type="button" class="btn btn-ghost btn-sm" onclick="deleteNote(${n.id})" title="حذف" style="padding:0.25rem 0.5rem">
                        <i class="fas fa-trash text-danger"></i>
                    </button>
                </div>
            </div>
        </div>
    `).join('');
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text || '';
    return div.innerHTML;
}

function escapeJs(str) {
    return (str || '').replace(/'/g, "\\'").replace(/\n/g, '\\n').replace(/\r/g, '');
}

document.addEventListener('DOMContentLoaded', initNotes);
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
