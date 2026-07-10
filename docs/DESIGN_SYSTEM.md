# ADA Co-OS — UI Design System

Bu döküman, ADA Co-OS'nın özel dark design system'ini ve CSS token'larını detaylandırır.

---

## 1. Tasarım Felsefesi

ADA Co-OS, **Linear** ve **Stripe Dashboard** estetiğinden ilham alan minimal, dark-first bir tasarım dili kullanır:

- 🌑 **Dark-first** — Göz yorgunluğunu azaltan koyu tema
- 🎯 **Content-focused** — İçeriğe odaklanan temiz layout
- ✨ **Subtle animations** — Dikkat dağıtmayan mikro animasyonlar
- 🧩 **Token-based** — CSS custom properties ile merkezi yönetim

## 2. Renk Paleti

### Arka Planlar
```css
--nx-bg-base:    #0a0a0f;    /* Ana arka plan */
--nx-bg-card:    #111118;    /* Kart arka planları */
--nx-bg-input:   #1a1a24;    /* Form elemanları */
--nx-bg-hover:   #1e1e2a;    /* Hover durumu */
--nx-bg-sidebar: rgba(14, 14, 22, 0.85);  /* Sidebar (blur) */
```

### Kenarlıklar
```css
--nx-border:        rgba(255, 255, 255, 0.08);   /* Varsayılan */
--nx-border-active: rgba(16, 185, 129, 0.5);     /* Aktif/focus */
--nx-border-hover:  rgba(255, 255, 255, 0.15);   /* Hover */
```

### Metin
```css
--nx-text-primary:   #f0f0f5;   /* Ana metin */
--nx-text-secondary: #8888a0;   /* İkincil metin */
--nx-text-muted:     #555568;   /* Soluk metin */
```

### Aksan Renkler
```css
--nx-accent:       #10b981;   /* Emerald — ana aksan */
--nx-accent-hover: #34d399;   /* Hover durumu */
--nx-danger:       #ef4444;   /* Kırmızı — tehlike */
--nx-warning:      #f59e0b;   /* Turuncu — uyarı */
--nx-success:      #10b981;   /* Yeşil — başarı */
--nx-info:         #3b82f6;   /* Mavi — bilgi */
```

### Badge Renkleri
```css
/* Her badge için bg + text çifti */
--nx-badge-success-bg:   rgba(16, 185, 129, 0.15);
--nx-badge-success-text: #34d399;

--nx-badge-warning-bg:   rgba(245, 158, 11, 0.15);
--nx-badge-warning-text: #fbbf24;

--nx-badge-danger-bg:    rgba(239, 68, 68, 0.15);
--nx-badge-danger-text:  #f87171;

--nx-badge-info-bg:      rgba(59, 130, 246, 0.15);
--nx-badge-info-text:    #60a5fa;

--nx-badge-gray-bg:      rgba(255, 255, 255, 0.08);
--nx-badge-gray-text:    #8888a0;
```

## 3. Tipografi

| Kullanım | Font | Weight |
|:---|:---|:---|
| UI metin | Inter | 300–700 |
| Kod / monospace | JetBrains Mono | 400–700 |

```css
--nx-font-ui:   'Inter', system-ui, -apple-system, sans-serif;
--nx-font-mono: 'JetBrains Mono', 'Fira Code', monospace;
```

## 4. Spacing & Radius

```css
/* Radius */
--nx-radius-sm: 6px;
--nx-radius-md: 8px;
--nx-radius-lg: 12px;
--nx-radius-xl: 16px;

/* Layout */
--nx-sidebar-width:     256px;
--nx-sidebar-collapsed: 64px;
--nx-topbar-height:     56px;
```

## 5. Gölgeler

```css
--nx-shadow-sm:   0 1px 2px rgba(0, 0, 0, 0.4);
--nx-shadow-md:   0 4px 12px rgba(0, 0, 0, 0.5);
--nx-shadow-lg:   0 8px 32px rgba(0, 0, 0, 0.6);
--nx-shadow-glow: 0 0 0 1px var(--nx-border-active),
                  0 0 12px rgba(16, 185, 129, 0.15);
```

## 6. Animasyonlar

```css
--nx-transition-fast: 100ms ease;
--nx-transition:      150ms ease;
--nx-transition-slow: 300ms ease;
```

## 7. CSS Sınıf Referansı

### Layout
| Sınıf | Açıklama |
|:---|:---|
| `.nx-admin` | Root class (html & body) |
| `.nx-layout` | Sidebar + main grid container |
| `.nx-sidebar` | Sol sidebar |
| `.nx-main` | Ana içerik alanı |
| `.nx-topbar` | Üst breadcrumb bar |
| `.nx-content` | Sayfa içeriği wrapper |

### Navigasyon
| Sınıf | Açıklama |
|:---|:---|
| `.nx-sidebar-brand` | Logo alanı |
| `.nx-sidebar-nav` | Navigasyon listesi |
| `.nx-nav-group` | Grup container |
| `.nx-nav-group-label` | Grup başlığı (uppercase) |
| `.nx-nav-item` | Navigasyon linki |
| `.nx-nav-item.active` | Aktif sayfa |
| `.nx-nav-icon` | İkon (20x20 SVG) |

### Kartlar & Bileşenler
| Sınıf | Açıklama |
|:---|:---|
| `.nx-card` | Kart container |
| `.nx-badge` | Badge |
| `.nx-badge-success/warning/danger/info` | Badge varyantları |
| `.nx-btn` | Buton base |
| `.nx-btn-primary` | Ana buton (emerald) |
| `.nx-btn-danger` | Tehlike butonu |
| `.nx-btn-ghost` | Şeffaf buton |

### Form
| Sınıf | Açıklama |
|:---|:---|
| `.nx-input` | Input / select / textarea |
| `.nx-label` | Form label |
| `.nx-form-group` | Label + input wrapper |
| `.nx-error-text` | Hata mesajı |

### Tablo
| Sınıf | Açıklama |
|:---|:---|
| `.nx-table` | Tablo |
| `.nx-table th` | Başlık hücresi |
| `.nx-table td` | Veri hücresi |
| `.nx-table tr:hover` | Satır hover |

### Sayfa
| Sınıf | Açıklama |
|:---|:---|
| `.nx-page-title` | Sayfa başlığı (h1) |
| `.nx-page-subtitle` | Alt başlık |
| `.nx-page-actions` | Sağ üst butonlar |
| `.nx-breadcrumb` | Breadcrumb bar |

## 8. İkon Sistemi

Tüm ikonlar **Heroicons (Outline, 24x24)** kullanılarak inline SVG olarak yerleştirilir:

```html
<svg class="nx-nav-icon" xmlns="http://www.w3.org/2000/svg"
     fill="none" viewBox="0 0 24 24"
     stroke-width="1.5" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round" d="..."/>
</svg>
```

- Boyut: `width: 20px; height: 20px;`
- Renk: `currentColor` (parent'tan miras)
- Stroke: `1.5`

## 9. Responsive Breakpoints

| Breakpoint | Davranış |
|:---|:---|
| `≥ 1024px` | Tam sidebar + genişletilmiş layout |
| `768–1023px` | Collapsed sidebar (64px) |
| `< 768px` | Sidebar gizli, hamburger menü |

## 10. Dark Theme Kuralları

1. **Arka plan hiyerarşisi:** `base` → `card` → `input` (koyu → açık)
2. **Kenarlıklar:** Sadece `rgba(255, 255, 255, 0.08)` — kesinlikle katı renk yok
3. **Metin kontrastı:** `primary > secondary > muted` (WCAG AA uyumlu)
4. **Hover:** `+0.05` opacity artışı veya `bg-hover` kullan
5. **Focus:** Emerald glow ring (`--nx-shadow-glow`)
6. **Scrollbar:** İnce 6px, koyu track, emerald thumb

---

*Kaynak dosya: `resources/css/nexus-admin.css` (~1200 satır)*  
*Son güncelleme: Nisan 2026 — v1.2*
