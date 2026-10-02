<?php
/**
 * أداة: قائمة المهام اليومية (To-Do List)
 * تعمل للمستخدمين المجهولين (عبر localStorage) وللمسجلين (عبر قاعدة البيانات)
 * Code Elta6ur Tools
 */
require_once __DIR__ . '/../config/app.php';

$isUserAuth = isLoggedIn();
$todosFromDb = [];

if ($isUserAuth) {
    try {
        $db = getDB();
        $userId = $_SESSION['user_id'];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_task_db'])) {
            $title = sanitize($_POST['title'] ?? '');
            $priority = sanitize($_POST['priority'] ?? 'medium');
            $dueDate = sanitize($_POST['due_date'] ?? '') ?: null;
            if ($title) {
                $stmt = $db->prepare("INSERT INTO todos (user_id, title, priority, due_date) VALUES (?, ?, ?, ?)");
                $stmt->execute([$userId, $title, $priority, $dueDate]);
            }
        }
        
        $stmt = $db->prepare("SELECT * FROM todos WHERE user_id = ? ORDER BY completed ASC, priority DESC, created_at DESC");
        $stmt->execute([$userId]);
        $todosFromDb = $stmt->fetchAll();
    } catch(Throwable $e) {}
}

include __DIR__ . '/../includes/header.php';
renderToolHeader($tool);
?>

<!-- شريط الإضافة -->
<div class="card mb-2">
    <div class="form-row align-center">
        <div class="form-group" style="flex:4;margin-bottom:0">
            <input type="text" id="taskTitle" class="form-control" placeholder="ما هي المهمة التي تريد إنجازها اليوم؟" onkeydown="if(event.key==='Enter')addTask()">
        </div>
        <div class="form-group" style="flex:1;min-width:120px;margin-bottom:0">
            <select id="taskPriority" class="form-control">
                <option value="high">أولوية عالية 🔥</option>
                <option value="medium" selected>أولوية متوسطة ⚡</option>
                <option value="low">أولوية منخفضة ☕</option>
            </select>
        </div>
        <div class="form-group" style="flex:1;min-width:140px;margin-bottom:0">
            <input type="date" id="taskDueDate" class="form-control" value="<?= date('Y-m-d') ?>">
        </div>
        <button type="button" class="btn btn-primary" onclick="addTask()" style="margin-bottom:0">
            <i class="fas fa-plus"></i> إضافة المهمة
        </button>
    </div>
</div>

<!-- إحصائيات سريعة وأزرار الفلترة -->
<div class="stats-grid mb-2">
    <div class="stat-card">
        <div class="stat-icon purple"><i class="fas fa-tasks"></i></div>
        <div>
            <div class="stat-value" id="statTotalTasks">0</div>
            <div class="stat-label">إجمالي المهام</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
        <div>
            <div class="stat-value" id="statCompletedTasks" style="color:#10b981">0</div>
            <div class="stat-label">المهام المنجزة</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange"><i class="fas fa-hourglass-half"></i></div>
        <div>
            <div class="stat-value" id="statPendingTasks" style="color:#f59e0b">0</div>
            <div class="stat-label">قيد الانتظار</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="d-flex justify-between align-center flex-wrap gap-1 mb-2">
        <div class="tabs mb-0">
            <button type="button" class="tab active" onclick="setFilter('all', this)">الكل</button>
            <button type="button" class="tab" onclick="setFilter('pending', this)">المتبقية</button>
            <button type="button" class="tab" onclick="setFilter('completed', this)">المنجزة</button>
        </div>
        <div class="d-flex gap-1">
            <button type="button" class="btn btn-ghost btn-sm" onclick="exportTasks()"><i class="fas fa-download"></i> تصدير</button>
            <button type="button" class="btn btn-ghost btn-sm" onclick="clearCompleted()"><i class="fas fa-check-double"></i> مسح المنجز</button>
            <button type="button" class="btn btn-ghost btn-sm" onclick="clearAllTasks()"><i class="fas fa-trash"></i> مسح الكل</button>
        </div>
    </div>

    <!-- قائمة المهام -->
    <div id="todoListContainer" class="todo-list-items"></div>
    
    <div id="todoEmptyState" class="empty-state" style="padding:2.5rem 1rem;display:none">
        <i class="fas fa-clipboard-check"></i>
        <h3>لا توجد مهام حالياً</h3>
        <p>رائع! لا توجد مهام في هذه القائمة، أضف مهمة جديدة للبدء.</p>
    </div>
</div>

<style>
.todo-list-items {
    display: flex;
    flex-direction: column;
    gap: 0.65rem;
}
.todo-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: var(--bg-input);
    border: 1px solid var(--border-color);
    border-radius: var(--border-radius-sm);
    padding: 0.85rem 1rem;
    transition: all var(--transition-fast);
}
.todo-item:hover {
    border-color: var(--border-color-hover);
    background: var(--bg-card-hover);
}
.todo-item.is-completed {
    opacity: 0.6;
}
.todo-item.is-completed .todo-title {
    text-decoration: line-through;
    color: var(--text-muted);
}
.priority-badge {
    font-size: 0.725rem;
    padding: 0.2rem 0.6rem;
    border-radius: 12px;
    font-weight: 600;
}
.priority-high { background: rgba(239, 68, 68, 0.15); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.3); }
.priority-medium { background: rgba(245, 158, 11, 0.15); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.3); }
.priority-low { background: rgba(16, 185, 129, 0.15); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3); }
</style>

<?php 
renderToolExplanation('نصائح لإدارة المهام والإنتاجية العالية', [
    'حدد 3 مهام رئيسية فقط لكل يوم للتركيز عليها وتجنب التشتت.',
    'رتّب المهام حسب الأولوية واعتمد إنجاز المهام الأصعب أو الأهم أولاً في الصباح.',
    'يتم حفظ جميع مهامك محلياً بشكل فوري وآمن على جهازك حتى لو لم تقم بتسجيل الدخول.'
]);

renderRelatedTools($tool['related'] ?? ['notes', 'time-tracker', 'daily-study-hours-calculator']);
renderToolScriptHelpers();
?>

<script>
let tasks = [];
let currentFilter = 'all';

function initTodoList() {
    const saved = localStorage.getItem('elta6ur_todos');
    if (saved) {
        try { tasks = JSON.parse(saved); } catch(e) { tasks = []; }
    } else {
        // مهام ترحيبية نموذجية
        tasks = [
            { id: 1, title: 'استكشاف منصة كود التطور للأدوات الشاملة', priority: 'high', dueDate: '<?= date('Y-m-d') ?>', completed: true },
            { id: 2, title: 'تجربة حاسبة صافي الراتب والعملات', priority: 'medium', dueDate: '<?= date('Y-m-d') ?>', completed: false },
            { id: 3, title: 'تنظيم مهام ومشاريع العمل لهذا الأسبوع', priority: 'low', dueDate: '<?= date('Y-m-d', strtotime('+2 days')) ?>', completed: false }
        ];
        saveTasks();
    }
    renderTasks();
}

function saveTasks() {
    try {
        localStorage.setItem('elta6ur_todos', JSON.stringify(tasks));
    } catch(e) {}
}

function addTask() {
    const title = document.getElementById('taskTitle').value.trim();
    const priority = document.getElementById('taskPriority').value;
    const dueDate = document.getElementById('taskDueDate').value;

    if (!title) {
        showToast('يرجى كتابة عنوان المهمة', 'warning');
        return;
    }

    const newTask = {
        id: Date.now(),
        title: title,
        priority: priority,
        dueDate: dueDate,
        completed: false
    };

    tasks.unshift(newTask);
    saveTasks();
    document.getElementById('taskTitle').value = '';
    renderTasks();
    showToast('تمت إضافة المهمة بنجاح!');
}

function toggleTask(id) {
    const t = tasks.find(item => item.id === id);
    if (t) {
        t.completed = !t.completed;
        saveTasks();
        renderTasks();
    }
}

function deleteTask(id) {
    tasks = tasks.filter(item => item.id !== id);
    saveTasks();
    renderTasks();
    showToast('تم حذف المهمة');
}

function setFilter(filter, btn) {
    currentFilter = filter;
    document.querySelectorAll('.tabs .tab').forEach(t => t.classList.remove('active'));
    if (btn) btn.classList.add('active');
    renderTasks();
}

function clearCompleted() {
    tasks = tasks.filter(item => !item.completed);
    saveTasks();
    renderTasks();
    showToast('تم مسح المهام المكتملة');
}

function clearAllTasks() {
    if (confirm('هل أنت متأكد من مسح جميع المهام؟')) {
        tasks = [];
        saveTasks();
        renderTasks();
        showToast('تم مسح جميع المهام');
    }
}

function exportTasks() {
    let text = `قائمة المهام اليومية - كود التطور\nالتاريخ: ${new Date().toLocaleDateString('ar')}\n===============================\n\n`;
    tasks.forEach((t, i) => {
        text += `${i+1}. [${t.completed ? 'X' : ' '}] ${t.title} (الأولوية: ${t.priority}) - الاستحقاق: ${t.dueDate || '-'}\n`;
    });
    const blob = new Blob([text], { type: 'text/plain;charset=utf-8' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'todo-list.txt';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
    showToast('تم تصدير المهام!');
}

function renderTasks() {
    const container = document.getElementById('todoListContainer');
    const empty = document.getElementById('todoEmptyState');

    let filtered = tasks;
    if (currentFilter === 'pending') {
        filtered = tasks.filter(t => !t.completed);
    } else if (currentFilter === 'completed') {
        filtered = tasks.filter(t => t.completed);
    }

    const total = tasks.length;
    const completed = tasks.filter(t => t.completed).length;
    const pending = total - completed;

    document.getElementById('statTotalTasks').textContent = total;
    document.getElementById('statCompletedTasks').textContent = completed;
    document.getElementById('statPendingTasks').textContent = pending;

    if (filtered.length === 0) {
        container.innerHTML = '';
        empty.style.display = 'block';
        return;
    }

    empty.style.display = 'none';
    const priorityLabels = { 'high': 'عالية 🔥', 'medium': 'متوسطة ⚡', 'low': 'منخفضة ☕' };

    container.innerHTML = filtered.map(t => `
        <div class="todo-item ${t.completed ? 'is-completed' : ''}">
            <div class="d-flex align-center gap-1" style="flex:1;overflow:hidden">
                <input type="checkbox" ${t.completed ? 'checked' : ''} onchange="toggleTask(${t.id})" style="width:18px;height:18px;cursor:pointer">
                <span class="todo-title" style="font-size:0.95rem;word-break:break-word">${escapeHtml(t.title)}</span>
            </div>
            <div class="d-flex align-center gap-1">
                <span class="priority-badge priority-${t.priority}">${priorityLabels[t.priority] || t.priority}</span>
                ${t.dueDate ? `<span style="font-size:0.75rem;color:var(--text-muted)"><i class="far fa-calendar-alt"></i> ${t.dueDate}</span>` : ''}
                <button type="button" class="btn btn-ghost btn-sm" onclick="deleteTask(${t.id})" title="حذف" style="padding:0.3rem 0.6rem">
                    <i class="fas fa-trash text-danger"></i>
                </button>
            </div>
        </div>
    `).join('');
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

document.addEventListener('DOMContentLoaded', initTodoList);
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
