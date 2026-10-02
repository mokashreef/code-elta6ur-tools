<?php
/**
 * أداة: مولد ألوان (Color Palette)
 */
include __DIR__ . '/../includes/header.php';
?>
<div class="tool-page-header">
    <div class="tool-page-icon"><i class="fas <?= $tool['icon'] ?>"></i></div>
    <div class="tool-page-info"><h1><?= sanitize($tool['name']) ?></h1><p><?= sanitize($tool['description']) ?></p></div>
</div>
<div class="card">
    <div class="form-row">
        <div class="form-group">
            <label class="form-label">اللون الأساسي</label>
            <div style="display:flex;gap:0.5rem;align-items:center">
                <input type="color" id="baseColor" value="#6c63ff" style="width:60px;height:40px;border:none;cursor:pointer;border-radius:8px">
                <input type="text" id="baseColorHex" class="form-control" value="#6c63ff" style="max-width:120px" oninput="document.getElementById('baseColor').value=this.value">
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">نوع اللوحة</label>
            <select id="paletteType" class="form-control">
                <option value="analogous">ألوان متجاورة</option>
                <option value="complementary">ألوان متتامّة</option>
                <option value="triadic">ألوان ثلاثية</option>
                <option value="monochromatic">درجات اللون الواحد</option>
                <option value="random">عشوائي</option>
            </select>
        </div>
    </div>
    <button class="btn btn-primary btn-lg" onclick="generatePalette()"><i class="fas fa-palette"></i> توليد لوحة ألوان</button>
</div>

<div class="result-area" id="resultArea">
    <div class="result-header">
        <span class="result-title"><i class="fas fa-palette"></i> لوحة الألوان</span>
        <div class="result-actions">
            <button class="btn btn-ghost btn-sm" onclick="copyPaletteCSS()"><i class="fas fa-copy"></i> نسخ CSS</button>
            <button class="btn btn-secondary btn-sm" onclick="generatePalette()"><i class="fas fa-sync"></i> جديد</button>
        </div>
    </div>
    <div id="paletteGrid" style="display:grid;grid-template-columns:repeat(5,1fr);gap:0.75rem;margin-bottom:1rem"></div>
    <div class="result-content" id="resultContent" style="display:none"></div>
</div>

<script>
document.getElementById('baseColor').addEventListener('input', function() {
    document.getElementById('baseColorHex').value = this.value;
});

function hexToHSL(hex) {
    let r = parseInt(hex.slice(1, 3), 16) / 255;
    let g = parseInt(hex.slice(3, 5), 16) / 255;
    let b = parseInt(hex.slice(5, 7), 16) / 255;
    let max = Math.max(r, g, b), min = Math.min(r, g, b);
    let h, s, l = (max + min) / 2;
    if (max === min) { h = s = 0; }
    else {
        let d = max - min;
        s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
        switch (max) {
            case r: h = ((g - b) / d + (g < b ? 6 : 0)) / 6; break;
            case g: h = ((b - r) / d + 2) / 6; break;
            case b: h = ((r - g) / d + 4) / 6; break;
        }
    }
    return [Math.round(h * 360), Math.round(s * 100), Math.round(l * 100)];
}

function hslToHex(h, s, l) {
    h /= 360; s /= 100; l /= 100;
    let r, g, b;
    if (s === 0) { r = g = b = l; }
    else {
        const hue2rgb = (p, q, t) => { if (t < 0) t += 1; if (t > 1) t -= 1; if (t < 1/6) return p + (q - p) * 6 * t; if (t < 1/2) return q; if (t < 2/3) return p + (q - p) * (2/3 - t) * 6; return p; };
        const q = l < 0.5 ? l * (1 + s) : l + s - l * s;
        const p = 2 * l - q;
        r = hue2rgb(p, q, h + 1/3);
        g = hue2rgb(p, q, h);
        b = hue2rgb(p, q, h - 1/3);
    }
    return '#' + [r, g, b].map(x => Math.round(x * 255).toString(16).padStart(2, '0')).join('');
}

let currentPalette = [];

function generatePalette() {
    const baseHex = document.getElementById('baseColor').value;
    const type = document.getElementById('paletteType').value;
    const [h, s, l] = hexToHSL(baseHex);
    
    let colors = [];
    switch (type) {
        case 'analogous':
            for (let i = -2; i <= 2; i++) colors.push(hslToHex((h + i * 30 + 360) % 360, s, l));
            break;
        case 'complementary':
            colors = [hslToHex(h, s, l), hslToHex(h, s, Math.min(l + 15, 90)), hslToHex((h + 180) % 360, s, l), hslToHex((h + 180) % 360, s, Math.min(l + 15, 90)), hslToHex(h, Math.max(s - 20, 10), l)];
            break;
        case 'triadic':
            colors = [hslToHex(h, s, l), hslToHex((h + 120) % 360, s, l), hslToHex((h + 240) % 360, s, l), hslToHex(h, s, Math.min(l + 20, 90)), hslToHex(h, s, Math.max(l - 20, 10))];
            break;
        case 'monochromatic':
            for (let i = 0; i < 5; i++) colors.push(hslToHex(h, s, 15 + i * 17));
            break;
        case 'random':
            for (let i = 0; i < 5; i++) colors.push(hslToHex(Math.random() * 360, 50 + Math.random() * 40, 40 + Math.random() * 30));
            break;
    }
    
    currentPalette = colors;
    const grid = document.getElementById('paletteGrid');
    grid.innerHTML = colors.map(c => {
        const [ch, cs, cl] = hexToHSL(c);
        const textColor = cl > 55 ? '#000' : '#fff';
        return `<div class="color-swatch" style="background:${c}" onclick="copyToClipboard('${c}')">
            <span style="color:${textColor}">${c.toUpperCase()}</span>
        </div>`;
    }).join('');
    
    document.getElementById('resultArea').classList.add('show');
}

function copyPaletteCSS() {
    if (!currentPalette.length) return;
    let css = ':root {\n';
    currentPalette.forEach((c, i) => css += `  --color-${i + 1}: ${c};\n`);
    css += '}';
    copyToClipboard(css);
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
