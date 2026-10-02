/**
 * Code Elta6ur Tools - JavaScript الرئيسي
 */

document.addEventListener('DOMContentLoaded', function() {
    initSidebar();
    initUserMenu();
    initSearch();
    initFlashMessages();
    initNavGroups();
});

/* ============================================
   Sidebar
   ============================================ */
function initSidebar() {
    const toggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');

    if (toggle) {
        toggle.addEventListener('click', () => {
            sidebar.classList.toggle('open');
            overlay.classList.toggle('show');
        });
    }

    if (overlay) {
        overlay.addEventListener('click', () => {
            sidebar.classList.remove('open');
            overlay.classList.remove('show');
        });
    }
}

/* ============================================
   Navigation Groups (Collapsible)
   ============================================ */
function initNavGroups() {
    document.querySelectorAll('.nav-group-title').forEach(title => {
        title.addEventListener('click', () => {
            const group = title.closest('.nav-group');
            group.classList.toggle('collapsed');
        });
    });
}

/* ============================================
   User Menu Dropdown
   ============================================ */
function initUserMenu() {
    const btn = document.getElementById('userMenuBtn');
    const dropdown = document.getElementById('userDropdown');

    if (btn && dropdown) {
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            dropdown.classList.toggle('show');
        });

        document.addEventListener('click', (e) => {
            if (!dropdown.contains(e.target) && !btn.contains(e.target)) {
                dropdown.classList.remove('show');
            }
        });
    }
}

/* ============================================
   Global Search
   ============================================ */
function initSearch() {
    const searchInput = document.getElementById('globalSearch');
    const searchResults = document.getElementById('searchResults');
    let debounceTimer;

    if (!searchInput || !searchResults) return;

    searchInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        const query = this.value.trim();

        if (query.length < 2) {
            searchResults.classList.remove('show');
            searchResults.innerHTML = '';
            return;
        }

        debounceTimer = setTimeout(() => {
            fetch(`${BASE_URL}api/search-tools.php?q=${encodeURIComponent(query)}`)
                .then(res => res.json())
                .then(data => {
                    if (data.length > 0) {
                        searchResults.innerHTML = data.map(tool => `
                            <a href="${BASE_URL}tool.php?slug=${tool.slug}" class="search-result-item">
                                <i class="fas ${tool.icon}"></i>
                                <div>
                                    <div style="font-weight:600;color:var(--text-primary)">${tool.name}</div>
                                    <div style="font-size:0.75rem;color:var(--text-muted)">${tool.description || ''}</div>
                                </div>
                            </a>
                        `).join('');
                        searchResults.classList.add('show');
                    } else {
                        searchResults.innerHTML = '<div class="search-result-item" style="justify-content:center;color:var(--text-muted)">لا توجد نتائج</div>';
                        searchResults.classList.add('show');
                    }
                })
                .catch(() => {
                    searchResults.classList.remove('show');
                });
        }, 300);
    });

    searchInput.addEventListener('focus', function() {
        if (this.value.trim().length >= 2 && searchResults.innerHTML) {
            searchResults.classList.add('show');
        }
    });

    document.addEventListener('click', (e) => {
        if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.classList.remove('show');
        }
    });
}

/* ============================================
   Flash Messages Auto-dismiss
   ============================================ */
function initFlashMessages() {
    const flash = document.getElementById('flashAlert');
    if (flash) {
        setTimeout(() => {
            flash.style.opacity = '0';
            flash.style.transform = 'translateY(-10px)';
            setTimeout(() => flash.remove(), 300);
        }, 4000);
    }
}

/* ============================================
   Toast Notification
   ============================================ */
function showToast(message, type = 'success') {
    // إزالة أي toast سابق
    const existing = document.querySelector('.toast');
    if (existing) existing.remove();

    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'info-circle'}"></i> ${message}`;
    document.body.appendChild(toast);

    requestAnimationFrame(() => {
        toast.classList.add('show');
    });

    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 2500);
}

/* ============================================
   Copy to Clipboard
   ============================================ */
function copyToClipboard(text, btn) {
    navigator.clipboard.writeText(text).then(() => {
        showToast('تم النسخ بنجاح!');
        if (btn) {
            const originalHTML = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-check"></i>';
            btn.classList.add('btn-success');
            setTimeout(() => {
                btn.innerHTML = originalHTML;
                btn.classList.remove('btn-success');
            }, 1500);
        }
    }).catch(() => {
        // Fallback
        const textarea = document.createElement('textarea');
        textarea.value = text;
        textarea.style.position = 'fixed';
        textarea.style.opacity = '0';
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);
        showToast('تم النسخ بنجاح!');
    });
}

/* ============================================
   Copy Result
   ============================================ */
function copyResult() {
    const content = document.getElementById('resultContent');
    if (content) {
        copyToClipboard(content.textContent);
    }
}

/* ============================================
   Download as Text
   ============================================ */
function downloadAsText(filename) {
    const content = document.getElementById('resultContent');
    if (!content) return;

    const blob = new Blob([content.textContent], { type: 'text/plain;charset=utf-8' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename || 'result.txt';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
    showToast('تم التحميل!');
}

/* ============================================
   Download as HTML
   ============================================ */
function downloadAsHTML(content, filename) {
    const blob = new Blob([content], { type: 'text/html;charset=utf-8' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename || 'page.html';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
    showToast('تم التحميل!');
}

/* ============================================
   Save Output via AJAX
   ============================================ */
function saveOutputAjax(toolId, title, content) {
    return fetch(`${BASE_URL}api/save-output.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ tool_id: toolId, title: title, content: content })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showToast('تم الحفظ بنجاح!');
        } else {
            showToast(data.message || 'حدث خطأ أثناء الحفظ', 'error');
        }
        return data;
    })
    .catch(() => {
        showToast('حدث خطأ في الاتصال', 'error');
    });
}

/* ============================================
   Toggle Favorite
   ============================================ */
function toggleFavorite(toolId, btn) {
    fetch(`${BASE_URL}api/toggle-favorite.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ tool_id: toolId })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            if (data.is_favorite) {
                btn.classList.add('active');
                btn.innerHTML = '<i class="fas fa-heart"></i>';
                showToast('تمت الإضافة للمفضلة');
            } else {
                btn.classList.remove('active');
                btn.innerHTML = '<i class="far fa-heart"></i>';
                showToast('تمت الإزالة من المفضلة');
            }
        }
    })
    .catch(() => {
        showToast('يجب تسجيل الدخول أولاً', 'error');
    });
}

/* ============================================
   Show/Hide Result Area
   ============================================ */
function showResult(content) {
    const area = document.getElementById('resultArea');
    const contentEl = document.getElementById('resultContent');
    if (area && contentEl) {
        contentEl.textContent = content;
        area.classList.add('show');
        area.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
}

function showResultHTML(html) {
    const area = document.getElementById('resultArea');
    const contentEl = document.getElementById('resultContent');
    if (area && contentEl) {
        contentEl.innerHTML = html;
        area.classList.add('show');
        area.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
}

/* ============================================
   BASE_URL global (set from PHP)
   ============================================ */
const BASE_URL = document.querySelector('link[rel="stylesheet"]')?.href.split('assets/')[0] || '/';
