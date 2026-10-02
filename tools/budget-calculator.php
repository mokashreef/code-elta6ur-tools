<?php
/**
 * أداة: حاسبة ميزانية بسيطة
 */
include __DIR__ . '/../includes/header.php';
?>
<div class="tool-page-header">
    <div class="tool-page-icon"><i class="fas <?= $tool['icon'] ?>"></i></div>
    <div class="tool-page-info"><h1><?= sanitize($tool['name']) ?></h1><p><?= sanitize($tool['description']) ?></p></div>
</div>

<div class="stats-grid" style="margin-bottom:1.5rem">
    <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-arrow-down"></i></div>
        <div><div class="stat-value" id="totalIncome" style="color:#10b981">$0</div><div class="stat-label">إجمالي الدخل</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange"><i class="fas fa-arrow-up"></i></div>
        <div><div class="stat-value" id="totalExpenses" style="color:#f59e0b">$0</div><div class="stat-label">إجمالي المصاريف</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon purple"><i class="fas fa-wallet"></i></div>
        <div><div class="stat-value" id="balance">$0</div><div class="stat-label">الرصيد</div></div>
    </div>
</div>

<div class="card" style="margin-bottom:1.5rem">
    <div class="d-flex gap-1" style="flex-wrap:wrap;align-items:flex-end">
        <div class="form-group" style="flex:2;min-width:150px;margin-bottom:0">
            <input type="text" id="budgetDesc" class="form-control" placeholder="الوصف (مثال: راتب مشروع)">
        </div>
        <div class="form-group" style="flex:1;min-width:100px;margin-bottom:0">
            <input type="number" id="budgetAmount" class="form-control" placeholder="المبلغ" min="0" step="0.01">
        </div>
        <div class="form-group" style="flex:1;min-width:120px;margin-bottom:0">
            <select id="budgetType" class="form-control">
                <option value="income">دخل</option>
                <option value="expense">مصروف</option>
            </select>
        </div>
        <div class="form-group" style="flex:1;min-width:120px;margin-bottom:0">
            <select id="budgetCategory" class="form-control">
                <option value="عمل حر">عمل حر</option>
                <option value="راتب">راتب</option>
                <option value="استضافة">استضافة</option>
                <option value="أدوات">أدوات</option>
                <option value="تعليم">تعليم</option>
                <option value="إنترنت">إنترنت</option>
                <option value="أخرى">أخرى</option>
            </select>
        </div>
        <button class="btn btn-primary" onclick="addBudgetItem()" style="margin-bottom:0"><i class="fas fa-plus"></i></button>
    </div>
</div>

<div class="card">
    <div class="d-flex align-center justify-between mb-1">
        <h3 style="font-size:0.9rem;color:var(--text-secondary)">السجل</h3>
        <div class="d-flex gap-1">
            <button class="btn btn-ghost btn-sm" onclick="exportBudget()"><i class="fas fa-download"></i> تصدير</button>
            <button class="btn btn-ghost btn-sm" onclick="if(confirm('مسح الكل؟')){budgetItems=[];renderBudget()}"><i class="fas fa-trash"></i> مسح</button>
        </div>
    </div>
    <div id="budgetList"></div>
    <div id="budgetEmpty" class="empty-state" style="padding:2rem">
        <i class="fas fa-calculator"></i>
        <h3>لا توجد عناصر</h3>
        <p>أضف دخلك ومصاريفك لبدء تتبع الميزانية</p>
    </div>
</div>

<script>
let budgetItems = JSON.parse(localStorage.getItem('elta6ur_budget') || '[]');

function addBudgetItem() {
    const desc = document.getElementById('budgetDesc').value.trim();
    const amount = parseFloat(document.getElementById('budgetAmount').value);
    const type = document.getElementById('budgetType').value;
    const category = document.getElementById('budgetCategory').value;
    
    if (!desc || !amount) { showToast('الوصف والمبلغ مطلوبان', 'error'); return; }
    
    budgetItems.unshift({ id: Date.now(), desc, amount, type, category, date: new Date().toLocaleDateString('ar-SY') });
    localStorage.setItem('elta6ur_budget', JSON.stringify(budgetItems));
    
    document.getElementById('budgetDesc').value = '';
    document.getElementById('budgetAmount').value = '';
    renderBudget();
}

function removeBudgetItem(id) {
    budgetItems = budgetItems.filter(i => i.id !== id);
    localStorage.setItem('elta6ur_budget', JSON.stringify(budgetItems));
    renderBudget();
}

function renderBudget() {
    const list = document.getElementById('budgetList');
    const empty = document.getElementById('budgetEmpty');
    
    let totalIncome = 0, totalExpenses = 0;
    budgetItems.forEach(i => { if (i.type === 'income') totalIncome += i.amount; else totalExpenses += i.amount; });
    const balance = totalIncome - totalExpenses;
    
    document.getElementById('totalIncome').textContent = '$' + totalIncome.toFixed(2);
    document.getElementById('totalExpenses').textContent = '$' + totalExpenses.toFixed(2);
    document.getElementById('balance').textContent = '$' + balance.toFixed(2);
    document.getElementById('balance').style.color = balance >= 0 ? '#10b981' : '#ef4444';
    
    if (budgetItems.length === 0) { list.innerHTML = ''; empty.style.display = ''; return; }
    empty.style.display = 'none';
    
    list.innerHTML = budgetItems.map(i => `
        <div class="d-flex align-center gap-1" style="padding:0.65rem 0;border-bottom:1px solid var(--border-color)">
            <i class="fas fa-${i.type === 'income' ? 'arrow-down' : 'arrow-up'}" style="color:${i.type === 'income' ? '#10b981' : '#f59e0b'};width:20px"></i>
            <span style="flex:1">${i.desc}</span>
            <span class="badge badge-${i.type === 'income' ? 'success' : 'warning'}">${i.category}</span>
            <span style="font-weight:700;color:${i.type === 'income' ? '#10b981' : '#ef4444'}">
                ${i.type === 'income' ? '+' : '-'}$${i.amount.toFixed(2)}
            </span>
            <span style="font-size:0.7rem;color:var(--text-muted)">${i.date}</span>
            <button class="btn btn-ghost btn-sm" style="color:#ef4444" onclick="removeBudgetItem(${i.id})"><i class="fas fa-times"></i></button>
        </div>
    `).join('');
}

function exportBudget() {
    let text = 'ميزانية المشروع\n' + '═'.repeat(40) + '\n\n';
    budgetItems.forEach(i => {
        text += `${i.type === 'income' ? '↓' : '↑'} ${i.desc} | ${i.category} | ${i.type === 'income' ? '+' : '-'}$${i.amount.toFixed(2)} | ${i.date}\n`;
    });
    let totalIncome = budgetItems.filter(i => i.type === 'income').reduce((s, i) => s + i.amount, 0);
    let totalExpenses = budgetItems.filter(i => i.type === 'expense').reduce((s, i) => s + i.amount, 0);
    text += '\n' + '─'.repeat(40) + '\n';
    text += `الدخل: $${totalIncome.toFixed(2)}\nالمصاريف: $${totalExpenses.toFixed(2)}\nالرصيد: $${(totalIncome - totalExpenses).toFixed(2)}`;
    
    const blob = new Blob([text], { type: 'text/plain;charset=utf-8' });
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = 'budget.txt';
    a.click();
}

renderBudget();
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
