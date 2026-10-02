<?php
/**
 * أداة: مولد الفواتير الاحترافية
 * Code Elta6ur Tools
 */
include __DIR__ . '/../includes/header.php';
renderToolHeader($tool);
?>

<div class="card mb-2">
    <form id="invoiceForm" onsubmit="event.preventDefault(); generateInvoice();">
        <h3 class="section-title"><i class="fas fa-info-circle text-accent"></i> بيانات الفاتورة الأساسية</h3>
        
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">رقم الفاتورة</label>
                <input type="text" id="invNumber" class="form-control" value="INV-<?= date('Ym') ?>-001">
            </div>
            <div class="form-group">
                <label class="form-label">تاريخ الإصدار</label>
                <input type="date" id="invDate" class="form-control" value="<?= date('Y-m-d') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">تاريخ الاستحقاق (اختياري)</label>
                <input type="date" id="invDueDate" class="form-control" value="<?= date('Y-m-d', strtotime('+14 days')) ?>">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">من (بياناتك / اسم شركتك) <span class="required">*</span></label>
                <input type="text" id="invFromName" class="form-control" placeholder="اسمك أو اسم المؤسسة" value="مؤسسة التقنية الرقمية" required>
            </div>
            <div class="form-group">
                <label class="form-label">بريدك أو هاتفك</label>
                <input type="text" id="invFromContact" class="form-control" placeholder="info@example.com / 0501234567" value="contact@example.com">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">إلى (اسم العميل أو الجهة) <span class="required">*</span></label>
                <input type="text" id="invToName" class="form-control" placeholder="اسم العميل أو الشركة المستفيدة" value="شركة الأفق للتجارة" required>
            </div>
            <div class="form-group">
                <label class="form-label">عنوان العميل أو وسيلة التواصل</label>
                <input type="text" id="invToContact" class="form-control" placeholder="الرياض، المملكة العربية السعودية" value="client@example.com">
            </div>
        </div>

        <div class="form-row">
            <?php renderCurrencySelector('invCurrency', 'SAR', 'عملة الفاتورة'); ?>
            <div class="form-group">
                <label class="form-label">نسبة ضريبة القيمة المضافة (%)</label>
                <input type="number" id="invTax" class="form-control" value="15" min="0" max="100" step="0.1">
            </div>
            <div class="form-group">
                <label class="form-label">قيمة الخصم الإجمالي (إن وجد)</label>
                <input type="number" id="invDiscount" class="form-control" value="0" min="0" step="0.5">
            </div>
        </div>

        <!-- بنود الفاتورة -->
        <h3 class="section-title mt-2"><i class="fas fa-list-ol text-accent"></i> بنود وخدمات الفاتورة</h3>
        <div id="invoiceItemsList">
            <div class="invoice-item-row form-row align-center mb-1">
                <div class="form-group" style="flex:4;margin-bottom:0">
                    <input type="text" class="form-control item-desc" placeholder="وصف الخدمة أو المنتج" value="تصميم وبرمجة واجهات المستخدم المتجاوبة" required>
                </div>
                <div class="form-group" style="flex:1;min-width:80px;margin-bottom:0">
                    <input type="number" class="form-control item-qty" placeholder="الكمية" value="1" min="1" step="1" oninput="updateLiveSubtotal()">
                </div>
                <div class="form-group" style="flex:2;min-width:110px;margin-bottom:0">
                    <input type="number" class="form-control item-price" placeholder="سعر الوحدة" value="1500" min="0" step="0.5" oninput="updateLiveSubtotal()">
                </div>
                <div class="form-group" style="flex:0;margin-bottom:0">
                    <button type="button" class="btn btn-danger btn-sm" onclick="removeItemRow(this)" title="حذف البند">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>

        <button type="button" class="btn btn-ghost btn-sm mt-1 mb-2" onclick="addInvoiceItemRow()">
            <i class="fas fa-plus"></i> إضافة بند جديد
        </button>

        <div class="form-group">
            <label class="form-label">شروط الدفع والملاحظات</label>
            <textarea id="invNotes" class="form-control" rows="2" placeholder="مثال: يرجى تحويل المبلغ خلال 14 يوماً من تاريخ الفاتورة على الحساب البنكي...">شكراً لتعاملكم معنا. الدفع عبر التحويل البنكي خلال المدة المحددة.</textarea>
        </div>

        <div class="d-flex gap-1 flex-wrap">
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="fas fa-file-invoice"></i> توليد ومعاينة الفاتورة
            </button>
            <button type="button" class="btn btn-ghost" onclick="resetInvoice()">
                <i class="fas fa-redo"></i> إعادة تعيين
            </button>
        </div>
    </form>
</div>

<!-- منطقة النتيجة ومعاينة الفاتورة للطباعة -->
<div class="result-area" id="resultArea" style="display:none">
    <div class="result-header">
        <span class="result-title"><i class="fas fa-check-circle text-success"></i> الفاتورة جاهزة</span>
        <div class="result-actions">
            <button type="button" class="btn btn-primary btn-sm" onclick="printInvoice()"><i class="fas fa-print"></i> طباعة الفاتورة / PDF</button>
            <button type="button" class="btn btn-ghost btn-sm" onclick="copyInvoiceText(this)"><i class="fas fa-copy"></i> نسخ ملخص</button>
            <button type="button" class="btn btn-ghost btn-sm" onclick="downloadInvoiceHTML()"><i class="fas fa-download"></i> تحميل HTML</button>
        </div>
    </div>

    <!-- البطاقة البصرية للفاتورة الجاهزة للطباعة -->
    <div class="invoice-preview-card" id="invoicePrintArea">
        <div class="inv-head d-flex justify-between align-center flex-wrap gap-2 pb-2 border-bottom">
            <div>
                <h2 id="viewFromName" style="color:var(--text-accent-light);margin-bottom:0.25rem">مؤسسة التقنية</h2>
                <div id="viewFromContact" style="font-size:0.85rem;color:var(--text-muted)">contact@example.com</div>
            </div>
            <div style="text-align:left">
                <div style="font-size:1.5rem;font-weight:800;color:var(--text-primary)">فاتورة مبيعات</div>
                <div style="font-size:0.85rem;color:var(--text-muted)">رقم: <strong id="viewInvNumber">INV-001</strong></div>
                <div style="font-size:0.85rem;color:var(--text-muted)">التاريخ: <span id="viewInvDate"></span></div>
            </div>
        </div>

        <div class="inv-bill-to py-2">
            <div style="font-size:0.8rem;color:var(--text-muted);margin-bottom:0.2rem">فاتورة موجهة إلى:</div>
            <h3 id="viewToName" style="color:var(--text-primary);margin-bottom:0.2rem">شركة الأفق</h3>
            <div id="viewToContact" style="font-size:0.85rem;color:var(--text-secondary)">client@example.com</div>
        </div>

        <div class="mobile-table-wrapper">
            <table class="inv-table w-100" style="border-collapse:collapse">
                <thead>
                    <tr style="background:rgba(108,99,255,0.1);border-bottom:1px solid var(--border-color)">
                        <th style="padding:0.6rem 0.75rem;text-align:right">#</th>
                        <th style="padding:0.6rem 0.75rem;text-align:right">الوصف</th>
                        <th style="padding:0.6rem 0.75rem;text-align:center">الكمية</th>
                        <th style="padding:0.6rem 0.75rem;text-align:left">سعر الوحدة</th>
                        <th style="padding:0.6rem 0.75rem;text-align:left">الإجمالي</th>
                    </tr>
                </thead>
                <tbody id="viewItemsBody"></tbody>
            </table>
        </div>

        <div class="inv-summary d-flex justify-between flex-wrap gap-2 pt-2 border-top">
            <div style="max-width:350px">
                <div style="font-size:0.8rem;color:var(--text-muted)">ملاحظات وشروط الدفع:</div>
                <p id="viewNotes" style="font-size:0.85rem;color:var(--text-secondary);margin-top:0.25rem"></p>
            </div>
            <div style="min-width:240px">
                <div class="d-flex justify-between py-1" style="font-size:0.9rem">
                    <span style="color:var(--text-secondary)">المجموع الفرعي:</span>
                    <strong id="viewSubtotal">0</strong>
                </div>
                <div class="d-flex justify-between py-1" style="font-size:0.9rem" id="viewDiscountRow">
                    <span style="color:var(--text-secondary)">الخصم:</span>
                    <strong id="viewDiscount" style="color:#ef4444">-0</strong>
                </div>
                <div class="d-flex justify-between py-1" style="font-size:0.9rem">
                    <span style="color:var(--text-secondary)">الضريبة (<span id="viewTaxRate">15</span>%):</span>
                    <strong id="viewTaxAmount">0</strong>
                </div>
                <div class="d-flex justify-between py-1 mt-1 border-top" style="font-size:1.15rem;font-weight:800;color:var(--text-accent-light)">
                    <span>الإجمالي المستحق:</span>
                    <span id="viewTotal">0</span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.invoice-preview-card {
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: var(--border-radius);
    padding: 1.5rem;
    color: var(--text-primary);
}
.inv-table th, .inv-table td {
    padding: 0.65rem 0.75rem;
    border-bottom: 1px solid var(--border-color);
    font-size: 0.9rem;
}
.pb-2 { padding-bottom: 0.75rem; }
.py-2 { padding: 0.75rem 0; }
.pt-2 { padding-top: 0.75rem; }
.border-bottom { border-bottom: 1px solid var(--border-color); }
.border-top { border-top: 1px solid var(--border-color); }
@media print {
    body * { visibility: hidden; }
    #invoicePrintArea, #invoicePrintArea * { visibility: visible; }
    #invoicePrintArea {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        background: #fff !important;
        color: #000 !important;
        border: none;
    }
}
</style>

<?php 
renderToolExplanation('كيف تصمم فاتورة رسمية متوافقة؟', [
    'تأكد من وضوح اسمك أو اسم شركتك ومعلومات الاتصال لتسهيل التواصل والتحويل.',
    'حدد وصفاً دقيقاً لكل بند أو خدمة مقدمة مع الكمية وسعر الوحدة لتجنب أي سوء تفاهم.',
    'ادعم ضريبة القيمة المضافة المطبقة في دولتك (مثل 15% في السعودية، 5% في الإمارات).',
    'استخدم زر الطباعة لحفظ الفاتورة مباشرة بصيغة PDF عالية الدقة.'
], 'جميع بيانات الفواتير يتم معالجتها داخل متصفحك مباشرة لحماية خصوصيتك وخصوصية عملائك.');

renderToolFAQ([
    ['q' => 'كيف أقوم بحفظ الفاتورة بصيغة PDF؟', 'a' => 'اضغط على زر "طباعة الفاتورة / PDF"، ثم من نافذة الطباعة اختر حفظ بتنسيق PDF (Save as PDF).'],
    ['q' => 'هل تدعم الأداة جميع العملات العربية؟', 'a' => 'نعم، تدعم الأداة أكثر من 20 عملة عربية ودولية (ريال سعودي، درهم، دولار، دينار كويتي، جنيه مصري، وغيرها).']
]);

renderRelatedTools($tool['related'] ?? ['budget-calculator', 'vat-calculator', 'discount-tax-calculator']);
renderToolScriptHelpers();
?>

<script>
function addInvoiceItemRow(desc = '', qty = 1, price = 0) {
    const container = document.getElementById('invoiceItemsList');
    const row = document.createElement('div');
    row.className = 'invoice-item-row form-row align-center mb-1';
    row.innerHTML = `
        <div class="form-group" style="flex:4;margin-bottom:0">
            <input type="text" class="form-control item-desc" placeholder="وصف الخدمة أو المنتج" value="${escapeHtml(desc)}" required>
        </div>
        <div class="form-group" style="flex:1;min-width:80px;margin-bottom:0">
            <input type="number" class="form-control item-qty" placeholder="الكمية" value="${qty}" min="1" step="1" oninput="updateLiveSubtotal()">
        </div>
        <div class="form-group" style="flex:2;min-width:110px;margin-bottom:0">
            <input type="number" class="form-control item-price" placeholder="سعر الوحدة" value="${price}" min="0" step="0.5" oninput="updateLiveSubtotal()">
        </div>
        <div class="form-group" style="flex:0;margin-bottom:0">
            <button type="button" class="btn btn-danger btn-sm" onclick="removeItemRow(this)" title="حذف البند">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;
    container.appendChild(row);
    updateLiveSubtotal();
}

function removeItemRow(btn) {
    const rows = document.querySelectorAll('.invoice-item-row');
    if (rows.length <= 1) {
        showToast('يجب أن تحتوي الفاتورة على بند واحد على الأقل', 'warning');
        return;
    }
    btn.closest('.invoice-item-row').remove();
    updateLiveSubtotal();
}

function updateLiveSubtotal() {
    // تحديث سريع إن دعت الحاجة
}

function generateInvoice() {
    const fromName = document.getElementById('invFromName').value.trim();
    const fromContact = document.getElementById('invFromContact').value.trim();
    const toName = document.getElementById('invToName').value.trim();
    const toContact = document.getElementById('invToContact').value.trim();
    const invNumber = document.getElementById('invNumber').value.trim();
    const invDate = document.getElementById('invDate').value;
    const cur = document.getElementById('invCurrency').value;
    const curSymbol = getCurrencySymbol(cur);
    const taxRate = parseFloat(document.getElementById('invTax').value) || 0;
    const discount = parseFloat(document.getElementById('invDiscount').value) || 0;
    const notes = document.getElementById('invNotes').value.trim();

    const rows = document.querySelectorAll('.invoice-item-row');
    let items = [];
    let subtotal = 0;

    rows.forEach((r, idx) => {
        const desc = r.querySelector('.item-desc').value.trim() || `بند رقم ${idx+1}`;
        const qty = parseFloat(r.querySelector('.item-qty').value) || 1;
        const price = parseFloat(r.querySelector('.item-price').value) || 0;
        const total = qty * price;
        subtotal += total;
        items.push({ idx: idx + 1, desc, qty, price, total });
    });

    const taxableAmount = Math.max(0, subtotal - discount);
    const taxAmount = (taxableAmount * taxRate) / 100;
    const grandTotal = taxableAmount + taxAmount;

    // تحديث المعاينة
    document.getElementById('viewFromName').textContent = fromName;
    document.getElementById('viewFromContact').textContent = fromContact;
    document.getElementById('viewToName').textContent = toName;
    document.getElementById('viewToContact').textContent = toContact;
    document.getElementById('viewInvNumber').textContent = invNumber;
    document.getElementById('viewInvDate').textContent = invDate;
    document.getElementById('viewNotes').textContent = notes || 'لا توجد ملاحظات إضافية.';

    document.getElementById('viewSubtotal').textContent = `${formatNumber(subtotal)} ${curSymbol}`;
    document.getElementById('viewDiscount').textContent = `-${formatNumber(discount)} ${curSymbol}`;
    document.getElementById('viewDiscountRow').style.display = discount > 0 ? 'flex' : 'none';
    document.getElementById('viewTaxRate').textContent = taxRate;
    document.getElementById('viewTaxAmount').textContent = `${formatNumber(taxAmount)} ${curSymbol}`;
    document.getElementById('viewTotal').textContent = `${formatNumber(grandTotal)} ${curSymbol}`;

    const tbody = document.getElementById('viewItemsBody');
    tbody.innerHTML = items.map(item => `
        <tr>
            <td style="color:var(--text-muted)">${item.idx}</td>
            <td><strong>${escapeHtml(item.desc)}</strong></td>
            <td style="text-align:center">${item.qty}</td>
            <td style="text-align:left">${formatNumber(item.price)} ${curSymbol}</td>
            <td style="text-align:left;font-weight:700">${formatNumber(item.total)} ${curSymbol}</td>
        </tr>
    `).join('');

    const area = document.getElementById('resultArea');
    area.style.display = 'block';
    area.classList.add('show');
    area.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    showToast('تم إنشاء الفاتورة بنجاح!');
}

function printInvoice() {
    window.print();
}

function copyInvoiceText(btn) {
    const number = document.getElementById('viewInvNumber').textContent;
    const to = document.getElementById('viewToName').textContent;
    const total = document.getElementById('viewTotal').textContent;
    const summary = `فاتورة مبيعات: ${number}\nإلى: ${to}\nالمبلغ الإجمالي المستحق: ${total}\nتاريخ: ${document.getElementById('viewInvDate').textContent}`;
    copyToClipboard(summary, btn);
}

function downloadInvoiceHTML() {
    const printContent = document.getElementById('invoicePrintArea').outerHTML;
    const html = `<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>فاتورة - ${document.getElementById('viewInvNumber').textContent}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Cairo', sans-serif; direction: rtl; padding: 2rem; background: #fff; color: #1e293b; max-width: 800px; margin: 0 auto; }
        table { width: 100%; border-collapse: collapse; margin: 1rem 0; }
        th, td { border-bottom: 1px solid #e2e8f0; padding: 0.75rem; text-align: right; }
        th { background: #f8fafc; font-weight: 700; }
    </style>
</head>
<body>
${printContent}
</body>
</html>`;

    const blob = new Blob([html], { type: 'text/html;charset=utf-8' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `invoice-${document.getElementById('viewInvNumber').textContent}.html`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
    showToast('تم تحميل الفاتورة كملف HTML!');
}

function resetInvoice() {
    document.getElementById('invoiceForm').reset();
    document.getElementById('resultArea').style.display = 'none';
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
