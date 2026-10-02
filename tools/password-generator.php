<?php
/**
 * أداة: مولد كلمات مرور
 */
include __DIR__ . '/../includes/header.php';
?>
<div class="tool-page-header">
    <div class="tool-page-icon"><i class="fas <?= $tool['icon'] ?>"></i></div>
    <div class="tool-page-info"><h1><?= sanitize($tool['name']) ?></h1><p><?= sanitize($tool['description']) ?></p></div>
</div>
<div class="card">
    <div class="form-group">
        <label class="form-label">الطول</label>
        <input type="range" id="passLength" class="form-range" min="6" max="64" value="16" oninput="document.getElementById('lengthVal').textContent=this.value">
        <div style="display:flex;justify-content:space-between;font-size:0.75rem;color:var(--text-muted)"><span>6</span><span id="lengthVal">16</span><span>64</span></div>
    </div>
    <div class="form-row" style="margin-bottom:1.5rem">
        <label class="form-check"><input type="checkbox" id="passUpper" checked> أحرف كبيرة (A-Z)</label>
        <label class="form-check"><input type="checkbox" id="passLower" checked> أحرف صغيرة (a-z)</label>
        <label class="form-check"><input type="checkbox" id="passNumbers" checked> أرقام (0-9)</label>
        <label class="form-check"><input type="checkbox" id="passSymbols" checked> رموز (!@#$%)</label>
    </div>
    <div class="form-group">
        <label class="form-label">العدد</label>
        <input type="number" id="passCount" class="form-control" value="5" min="1" max="20" style="max-width:120px">
    </div>
    <button class="btn btn-primary btn-lg" onclick="generatePasswords()"><i class="fas fa-key"></i> توليد</button>
</div>

<div class="result-area" id="resultArea">
    <div class="result-header">
        <span class="result-title"><i class="fas fa-key"></i> كلمات المرور</span>
        <div class="result-actions">
            <button class="btn btn-ghost btn-sm" onclick="copyResult()"><i class="fas fa-copy"></i> نسخ الكل</button>
        </div>
    </div>
    <div class="result-content" id="resultContent"></div>
</div>

<script>
function generatePasswords() {
    const length = parseInt(document.getElementById('passLength').value);
    const count = parseInt(document.getElementById('passCount').value);
    const useUpper = document.getElementById('passUpper').checked;
    const useLower = document.getElementById('passLower').checked;
    const useNumbers = document.getElementById('passNumbers').checked;
    const useSymbols = document.getElementById('passSymbols').checked;
    
    let charset = '';
    if (useUpper) charset += 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    if (useLower) charset += 'abcdefghijklmnopqrstuvwxyz';
    if (useNumbers) charset += '0123456789';
    if (useSymbols) charset += '!@#$%^&*()_+-=[]{}|;:,.<>?';
    
    if (!charset) { alert('اختر نوع حرف واحد على الأقل'); return; }
    
    let passwords = [];
    for (let i = 0; i < count; i++) {
        let pass = '';
        const array = new Uint32Array(length);
        crypto.getRandomValues(array);
        for (let j = 0; j < length; j++) {
            pass += charset[array[j] % charset.length];
        }
        passwords.push(pass);
    }
    
    const resultContent = document.getElementById('resultContent');
    resultContent.innerHTML = passwords.map((p, i) => 
        `<div style="display:flex;align-items:center;justify-content:space-between;padding:0.5rem 0;border-bottom:1px solid var(--border-color)">
            <span style="font-family:monospace;font-size:0.9rem;letter-spacing:1px">${i+1}. ${p}</span>
            <button class="btn btn-ghost btn-sm" onclick="copyToClipboard('${p}', this)" style="flex-shrink:0"><i class="fas fa-copy"></i></button>
        </div>`
    ).join('');
    
    document.getElementById('resultArea').classList.add('show');
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
