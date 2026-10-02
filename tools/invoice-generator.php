<?php
/**
 * أداة: مولد فاتورة
 */
include __DIR__ . '/../includes/header.php';
?>
<div class="tool-page-header">
    <div class="tool-page-icon"><i class="fas <?= $tool['icon'] ?>"></i></div>
    <div class="tool-page-info"><h1><?= sanitize($tool['name']) ?></h1><p><?= sanitize($tool['description']) ?></p></div>
</div>
<div class="card">
    <form onsubmit="event.preventDefault();generateInvoice()">
        <div class="form-row">
            <div class="form-group"><label class="form-label">رقم الفاتورة</label><input type="text" id="invNumber" class="form-control" value="INV-001"></div>
            <div class="form-group"><label class="form-label">التاريخ</label><input type="date" id="invDate" class="form-control" value="<?= date('Y-m-d') ?>"></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label class="form-label">من (اسمك)</label><input type="text" id="invFromName" class="form-control"></div>
            <div class="form-group"><label class="form-label">بريدك</label><input type="email" id="invFromEmail" class="form-control"></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label class="form-label">إلى (اسم العميل)</label><input type="text" id="invToName" class="form-control"></div>
            <div class="form-group"><label class="form-label">بريد العميل</label><input type="email" id="invToEmail" class="form-control"></div>
        </div>
        
        <h3 class="section-title" style="margin-top:1.5rem"><i class="fas fa-list"></i> البنود</h3>
        <div id="invoiceItems">
            <div class="form-row item-row" style="margin-bottom:0.5rem">
                <div class="form-group" style="flex:3"><input type="text" class="form-control item-desc" placeholder="وصف الخدمة"></div>
                <div class="form-group" style="flex:1"><input type="number" class="form-control item-qty" placeholder="الكمية" value="1" min="1"></div>
                <div class="form-group" style="flex:1"><input type="number" class="form-control item-price" placeholder="السعر" value="0"></div>
                <div class="form-group" style="flex:0"><button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.item-row').remove()"><i class="fas fa-trash"></i></button></div>
            </div>
        </div>
        <button type="button" class="btn btn-ghost btn-sm" onclick="addInvoiceItem()" style="margin-bottom:1rem"><i class="fas fa-plus"></i> إضافة بند</button>
        
        <div class="form-row">
            <div class="form-group"><label class="form-label">العملة</label><select id="invCurrency" class="form-control"><option value="$">دولار ($)</option><option value="₺">ليرة تركية (₺)</option><option value="€">يورو (€)</option><option value="ل.س">ليرة سورية (ل.س)</option></select></div>
            <div class="form-group"><label class="form-label">الضريبة %</label><input type="number" id="invTax" class="form-control" value="0" min="0" max="100"></div>
        </div>
        <div class="form-group"><label class="form-label">ملاحظات</label><textarea id="invNotes" class="form-control" rows="2" placeholder="شروط الدفع، ملاحظات إضافية..."></textarea></div>
        <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-file-invoice"></i> توليد الفاتورة</button>
    </form>
</div>
<div class="result-area" id="resultArea">
    <div class="result-header">
        <span class="result-title"><i class="fas fa-check-circle"></i> الفاتورة جاهزة!</span>
        <div class="result-actions">
            <button class="btn btn-ghost btn-sm" onclick="copyResult()"><i class="fas fa-copy"></i> نسخ</button>
            <button class="btn btn-ghost btn-sm" onclick="downloadAsText('invoice.txt')"><i class="fas fa-download"></i> تحميل</button>
        </div>
    </div>
    <div class="result-content" id="resultContent"></div>
</div>
<script>
function addInvoiceItem() {
    const html = `<div class="form-row item-row" style="margin-bottom:0.5rem">
        <div class="form-group" style="flex:3"><input type="text" class="form-control item-desc" placeholder="وصف الخدمة"></div>
        <div class="form-group" style="flex:1"><input type="number" class="form-control item-qty" placeholder="الكمية" value="1" min="1"></div>
        <div class="form-group" style="flex:1"><input type="number" class="form-control item-price" placeholder="السعر" value="0"></div>
        <div class="form-group" style="flex:0"><button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.item-row').remove()"><i class="fas fa-trash"></i></button></div>
    </div>`;
    document.getElementById('invoiceItems').insertAdjacentHTML('beforeend', html);
}
function generateInvoice() {
    const cur = document.getElementById('invCurrency').value;
    const tax = parseFloat(document.getElementById('invTax').value) || 0;
    let items = [];
    let subtotal = 0;
    document.querySelectorAll('.item-row').forEach(row => {
        const desc = row.querySelector('.item-desc').value || 'خدمة';
        const qty = parseInt(row.querySelector('.item-qty').value) || 1;
        const price = parseFloat(row.querySelector('.item-price').value) || 0;
        const total = qty * price;
        subtotal += total;
        items.push(`  ${desc}  |  الكمية: ${qty}  |  السعر: ${cur}${price}  |  المجموع: ${cur}${total}`);
    });
    const taxAmount = subtotal * tax / 100;
    const total = subtotal + taxAmount;
    
    let invoice = `═══════════════════════════════════════════
                    فاتورة
═══════════════════════════════════════════

رقم الفاتورة: ${document.getElementById('invNumber').value}
التاريخ: ${document.getElementById('invDate').value}

من: ${document.getElementById('invFromName').value}
    ${document.getElementById('invFromEmail').value}

إلى: ${document.getElementById('invToName').value}
     ${document.getElementById('invToEmail').value}

───────────────────────────────────────────
البنود:
${items.join('\n')}
───────────────────────────────────────────

المجموع الفرعي: ${cur}${subtotal.toFixed(2)}
${tax > 0 ? `الضريبة (${tax}%): ${cur}${taxAmount.toFixed(2)}` : ''}
═══════════════════════════════════════════
المجموع الكلي: ${cur}${total.toFixed(2)}
═══════════════════════════════════════════
${document.getElementById('invNotes').value ? `\nملاحظات: ${document.getElementById('invNotes').value}` : ''}`;

    document.getElementById('resultContent').textContent = invoice;
    document.getElementById('resultArea').classList.add('show');
}
</script>
<?php include __DIR__ . '/../includes/footer.php'; ?>
