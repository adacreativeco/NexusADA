/* ================================================================
   Nexus Admin 2.0 — Client-Side Logic
   Command Palette, Sidebar Toggle, Chart Helpers
   ================================================================ */

// ── Command Palette ────────────────────────────────────────────
const commandPalette = {
  el: null,
  searchInput: null,
  resultsEl: null,

  // Navigation items for search
  items: [
    { label: 'Dashboard', url: '/admin', icon: 'grid' },
    { label: 'Müşteriler', url: '/admin/clients', icon: 'users' },
    { label: 'Projeler', url: '/admin/projects', icon: 'briefcase' },
    { label: 'Kampanyalar', url: '/admin/campaigns', icon: 'megaphone' },
    { label: 'İçerikler', url: '/admin/content-items', icon: 'document' },

    { label: 'Etkinlikler', url: '/admin/events', icon: 'calendar' },
    { label: 'Basın İletişimi', url: '/admin/press-contacts', icon: 'id' },
    { label: 'Gelen Kutusu', url: '/admin/emails', icon: 'mail' },
    { label: 'Araçlar', url: '/admin/tools', icon: 'stack' },
    { label: 'Marka Varlıkları', url: '/admin/brand-assets', icon: 'sparkles' },
    { label: 'Departmanlar', url: '/admin/departments', icon: 'building' },
    { label: 'Teklif Motoru', url: '/admin/proposal', icon: 'document-duplicate' },
    { label: 'Yeni Müşteri', url: '/admin/clients?action=create', icon: 'plus' },
    { label: 'Yeni Proje', url: '/admin/projects?action=create', icon: 'plus' },
    { label: 'Telescope', url: '/telescope', icon: 'bug' },
  ],

  initialized: false,

  init() {
    this.el = document.getElementById('nx-command-palette');
    this.searchInput = document.getElementById('nx-command-search');
    this.resultsEl = document.getElementById('nx-command-results');

    if (!this.el) return;

    if (this.initialized) return;
    this.initialized = true;

    // Keyboard shortcut: Ctrl+K
    document.addEventListener('keydown', (e) => {
      if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        this.toggle();
      }
      if (e.key === 'Escape' && this.isOpen()) {
        this.close();
      }
    });

    // Custom event listener
    window.addEventListener('open-command-palette', () => this.open());

    // Search input event delegation
    document.addEventListener('input', (e) => {
      if (e.target && e.target.id === 'nx-command-search') {
        this.search(e.target.value);
      }
    });

    // Keyboard navigation event delegation
    document.addEventListener('keydown', (e) => {
      if (e.target && e.target.id === 'nx-command-search') {
        const resultsEl = document.getElementById('nx-command-results');
        if (!resultsEl) return;
        const items = resultsEl.querySelectorAll('.nx-command-item');
        const highlighted = resultsEl.querySelector('.nx-command-item.highlighted');
        let index = Array.from(items).indexOf(highlighted);

        if (e.key === 'ArrowDown') {
          e.preventDefault();
          if (highlighted) highlighted.classList.remove('highlighted');
          index = Math.min(index + 1, items.length - 1);
          items[index]?.classList.add('highlighted');
        } else if (e.key === 'ArrowUp') {
          e.preventDefault();
          if (highlighted) highlighted.classList.remove('highlighted');
          index = Math.max(index - 1, 0);
          items[index]?.classList.add('highlighted');
        } else if (e.key === 'Enter') {
          e.preventDefault();
          const active = resultsEl.querySelector('.nx-command-item.highlighted');
          if (active) {
            window.location.href = active.dataset.url;
          }
        }
      }
    });
  },

  isOpen() {
    return this.el && this.el.style.display !== 'none';
  },

  toggle() {
    this.isOpen() ? this.close() : this.open();
  },

  open() {
    if (!this.el) return;
    this.el.style.display = 'block';
    this.searchInput.value = '';
    this.searchInput.focus();
    this.renderResults(this.items.slice(0, 8));
    document.body.style.overflow = 'hidden';
  },

  close() {
    if (!this.el) return;
    this.el.style.display = 'none';
    document.body.style.overflow = '';
  },

  searchTimeout: null,

  search(query) {
    if (this.searchTimeout) {
      clearTimeout(this.searchTimeout);
    }

    if (!query.trim()) {
      this.renderResults(this.items.slice(0, 8));
      return;
    }

    const q = query.toLowerCase();
    const staticResults = this.items.filter(item =>
      item.label.toLowerCase().includes(q)
    );

    this.searchTimeout = setTimeout(() => {
      fetch(`/admin/search/universal?q=${encodeURIComponent(query)}`)
        .then(res => res.json())
        .then(dbResults => {
          const combined = [...staticResults, ...dbResults];
          this.renderResults(combined);
        })
        .catch(err => {
          console.error('Search error:', err);
          this.renderResults(staticResults);
        });
    }, 150);
  },

  renderResults(items) {
    if (!this.resultsEl) return;

    if (items.length === 0) {
      this.resultsEl.innerHTML = '<div class="nx-command-empty">Sonuç bulunamadı</div>';
      return;
    }

    this.resultsEl.innerHTML = items.map((item, i) => {
      let icon = item.icon || 'arrow_right_alt';
      if (icon === 'grid') icon = 'grid_view';
      if (icon === 'users') icon = 'group';
      if (icon === 'briefcase') icon = 'folder';
      if (icon === 'megaphone') icon = 'campaign';
      if (icon === 'document' || icon === 'document-duplicate') icon = 'description';
      if (icon === 'calendar') icon = 'event';
      if (icon === 'id') icon = 'contact_page';
      if (icon === 'mail') icon = 'inbox';
      if (icon === 'stack') icon = 'build';
      if (icon === 'sparkles') icon = 'palette';
      if (icon === 'building') icon = 'corporate_fare';
      if (icon === 'plus') icon = 'add';
      if (icon === 'bug') icon = 'bug_report';

      const categoryBadge = item.category 
        ? `<span style="font-size: 10px; background: rgba(16, 185, 129, 0.15); color: #10b981; padding: 2px 6px; border-radius: 4px; font-weight: 700; text-transform: uppercase;">${item.category}</span>` 
        : `<span style="font-size: 10px; background: rgba(255, 255, 255, 0.05); color: var(--nx-text-muted); padding: 2px 6px; border-radius: 4px; font-weight: 700; text-transform: uppercase;">Menü</span>`;

      return `
        <div class="nx-command-item ${i === 0 ? 'highlighted' : ''}" data-url="${item.url}" onclick="window.location.href='${item.url}'" style="display: flex; align-items: center; justify-content: space-between; padding: 10px 14px; cursor: pointer; transition: background 0.15s; border-radius: var(--nx-radius-sm); margin: 2px 0;">
          <div style="display: flex; align-items: center; gap: 12px;">
            <span class="material-symbols-outlined" style="font-size: 20px; color: var(--nx-text-secondary);">${icon}</span>
            <span style="font-size: 13px; font-weight: 500; color: var(--nx-text-primary);">${item.label}</span>
          </div>
          ${categoryBadge}
        </div>
      `;
    }).join('');
  }
};

// Global close function for backdrop click
window.closeCommandPalette = () => commandPalette.close();

// ── Sidebar Toggle Persistence ──────────────────────────────────
const sidebar = {
  initialized: false,
  init() {
    const saved = localStorage.getItem('nx-sidebar-collapsed');
    if (saved === 'true') {
      document.getElementById('nx-app')?.classList.add('sidebar-collapsed');
      document.getElementById('nx-sidebar')?.classList.add('collapsed');
    }

    if (this.initialized) return;
    this.initialized = true;

    // Global event delegation for sidebar toggle click
    document.addEventListener('click', (e) => {
      const toggleBtn = e.target.closest('#nx-sidebar-toggle');
      if (toggleBtn) {
        const app = document.getElementById('nx-app');
        const sidebarEl = document.getElementById('nx-sidebar');
        if (sidebarEl && app) {
          sidebarEl.classList.toggle('collapsed');
          app.classList.toggle('sidebar-collapsed');
          const isCollapsed = sidebarEl.classList.contains('collapsed');
          localStorage.setItem('nx-sidebar-collapsed', isCollapsed ? 'true' : 'false');
        }
      }
    });

    // Watch for toggle
    const observer = new MutationObserver(() => {
      const isCollapsed = document.getElementById('nx-sidebar')?.classList.contains('collapsed');
      localStorage.setItem('nx-sidebar-collapsed', isCollapsed ? 'true' : 'false');
    });

    const sidebarEl = document.getElementById('nx-sidebar');
    if (sidebarEl) {
      observer.observe(sidebarEl, { attributes: true, attributeFilter: ['class'] });
    }
  }
};

// ── Count-Up Animation ──────────────────────────────────────────
window.nxCountUp = (element, target, duration = 1000) => {
  const start = 0;
  const startTime = performance.now();

  const step = (currentTime) => {
    const elapsed = currentTime - startTime;
    const progress = Math.min(elapsed / duration, 1);
    const eased = 1 - Math.pow(1 - progress, 3); // ease-out cubic
    const current = Math.floor(start + (target - start) * eased);

    element.textContent = current.toLocaleString('tr-TR');

    if (progress < 1) {
      requestAnimationFrame(step);
    } else {
      element.textContent = target.toLocaleString('tr-TR');
    }
  };

  requestAnimationFrame(step);
};

// ── Init ────────────────────────────────────────────────────────
const initAdmin = () => {
  commandPalette.init();
  sidebar.init();
};

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initAdmin);
} else {
  initAdmin();
}

document.addEventListener('livewire:navigated', initAdmin);
