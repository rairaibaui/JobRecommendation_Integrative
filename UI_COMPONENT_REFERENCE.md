# UI Component Quick Reference Guide

## 🎨 Color Palette

```css
/* Primary Colors */
--primary-blue: #648EB5;
--primary-dark: #4E8EA2;
--navy: #334A5E;
--dark-navy: #2B4053;

/* Status Colors */
--success: #28a745;
--success-dark: #218838;
--warning: #ffc107;
--warning-dark: #e0a800;
--danger: #dc3545;
--danger-dark: #c82333;

/* Neutrals */
--gray-50: #F9FAFB;
--gray-100: #F3F4F6;
--gray-200: #E5E7EB;
--gray-500: #6B7280;
--gray-700: #4B5563;
--gray-900: #111827;

/* Badge Colors */
--badge-success-light: #d4edda;
--badge-success-dark: #c3e6cb;
--badge-warning-light: #fff3cd;
--badge-warning-dark: #ffeaa7;
--badge-danger-light: #f8d7da;
--badge-danger-dark: #f5c6cb;
```

---

## 📏 Spacing Scale

```css
/* 4px Base Unit System */
--space-xs: 4px;   /* Tight gaps, icon spacing */
--space-sm: 8px;   /* Small gaps, button spacing */
--space-md: 12px;  /* Default gaps */
--space-lg: 16px;  /* Section spacing */
--space-xl: 20px;  /* Card padding */
--space-2xl: 24px; /* Large card padding */
--space-3xl: 28px; /* Section padding */
```

---

## 🔤 Typography Scale

```css
/* Font Families */
--font-heading: 'Poppins', sans-serif;
--font-body: 'Roboto', sans-serif;

/* Font Sizes */
--text-xs: 12px;   /* Badges, small labels */
--text-sm: 13px;   /* Secondary text */
--text-base: 14px; /* Body text */
--text-lg: 15px;   /* Buttons */
--text-xl: 18px;   /* Card headings */
--text-2xl: 20px;  /* Section titles */
--text-3xl: 24px;  /* Page titles */
--text-4xl: 32px;  /* Stat numbers */

/* Font Weights */
--font-normal: 400;
--font-medium: 500;
--font-semibold: 600;
--font-bold: 700;
```

---

## 🎯 Button Styles

### Primary Button
```css
.btn-primary {
  background: linear-gradient(135deg, #648EB5 0%, #4E8EA2 100%);
  color: #fff;
  padding: 12px 24px;
  border-radius: 10px;
  font-size: 15px;
  font-weight: 600;
  box-shadow: 0 2px 8px rgba(100, 142, 181, 0.25);
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(100, 142, 181, 0.35);
}
```

### Edit Button
```css
.btn-edit {
  background: linear-gradient(135deg, #648EB5 0%, #4E8EA2 100%);
  color: #fff;
  padding: 8px 16px;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 500;
}
```

### Delete Button
```css
.btn-delete {
  background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
  color: #fff;
  padding: 8px 16px;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 500;
}
```

### Close Button
```css
.btn-close {
  background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
  color: #000;
  padding: 8px 16px;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 500;
}
```

### Reopen Button
```css
.btn-reopen {
  background: linear-gradient(135deg, #28a745 0%, #218838 100%);
  color: #fff;
  padding: 8px 16px;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 500;
}
```

### Icon Button
```css
.btn-icon {
  width: 36px;
  height: 36px;
  border-radius: 8px;
  background: #F3F4F6;
  color: #6B7280;
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

.btn-icon:hover {
  background: linear-gradient(135deg, #648EB5 0%, #4E8EA2 100%);
  color: #FFF;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}
```

---

## 🏷️ Badge Styles

### Active Badge
```css
.badge-active {
  background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
  color: #0f5132;
  border: 1px solid #c3e6cb;
  padding: 6px 14px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}
```

### Closed Badge
```css
.badge-closed {
  background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
  color: #842029;
  border: 1px solid #f5c6cb;
}
```

### Draft Badge
```css
.badge-draft {
  background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
  color: #856404;
  border: 1px solid #ffeaa7;
}
```

---

## 📦 Card Styles

### Basic Card
```css
.card {
  background: #FFF;
  border-radius: 12px;
  padding: 28px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  border: 1px solid rgba(100, 142, 181, 0.1);
}
```

### Job Card
```css
.job-card {
  background: #FFF;
  border-radius: 12px;
  padding: 24px;
  border: 1px solid rgba(100, 142, 181, 0.15);
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative;
  overflow: hidden;
}

.job-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 4px;
  height: 100%;
  background: linear-gradient(180deg, #648EB5 0%, #4E8EA2 100%);
  transform: scaleY(0);
  transform-origin: top;
  transition: transform 0.3s ease;
}

.job-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 12px 28px rgba(100, 142, 181, 0.18);
  border-color: rgba(100, 142, 181, 0.3);
}

.job-card:hover::before {
  transform: scaleY(1);
}
```

### Stat Card
```css
.stat-card {
  background: #FFF;
  border-radius: 12px;
  padding: 24px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  border: 1px solid rgba(100, 142, 181, 0.1);
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative;
  overflow: hidden;
}

.stat-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 3px;
  background: linear-gradient(90deg, #648EB5 0%, #4E8EA2 100%);
  transform: scaleX(0);
  transform-origin: left;
  transition: transform 0.4s ease;
}

.stat-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 24px rgba(100, 142, 181, 0.15);
  border-color: rgba(100, 142, 181, 0.3);
}

.stat-card:hover::before {
  transform: scaleX(1);
}
```

---

## 🖼️ Icon Guidelines

### Sizes
```css
/* Sidebar Icons */
.sidebar-btn-icon {
  font-size: 18px;
  min-width: 20px;
  text-align: center;
}

/* Stat Card Icons */
.stat-icon i {
  font-size: 30px;
  color: #FFF;
}

/* Job Detail Icons */
.job-detail-item i {
  width: 18px;
  font-size: 14px;
  color: #648EB5;
  text-align: center;
}

/* Action Button Icons */
.btn-icon i {
  font-size: 15px;
}

/* Badge Icons */
.badge i {
  font-size: 12px;
}
```

### Icon Containers
```css
/* Stat Card Icon Container */
.stat-icon {
  width: 64px;
  height: 64px;
  border-radius: 12px;
  background: linear-gradient(135deg, #648EB5 0%, #4E8EA2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 12px rgba(100, 142, 181, 0.25);
  transition: all 0.3s ease;
}

.stat-card:hover .stat-icon {
  transform: scale(1.05) rotate(-5deg);
  box-shadow: 0 6px 16px rgba(100, 142, 181, 0.35);
}
```

---

## 🎭 Shadow System

```css
/* Shadow Levels */
--shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.08);
--shadow-md: 0 4px 12px rgba(0, 0, 0, 0.1);
--shadow-lg: 0 8px 24px rgba(100, 142, 181, 0.15);
--shadow-xl: 0 12px 28px rgba(100, 142, 181, 0.18);

/* Colored Shadows */
--shadow-blue: 0 4px 12px rgba(100, 142, 181, 0.3);
--shadow-success: 0 4px 12px rgba(40, 167, 69, 0.3);
--shadow-warning: 0 4px 12px rgba(255, 193, 7, 0.3);
--shadow-danger: 0 4px 12px rgba(220, 53, 69, 0.3);
```

---

## 🎬 Animations

### Hover Lift
```css
.element {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.element:hover {
  transform: translateY(-4px);
}
```

### Slide In
```css
.element {
  transition: transform 0.3s ease;
}

.element:hover {
  transform: translateX(4px);
}
```

### Scale + Rotate
```css
.icon {
  transition: transform 0.3s ease;
}

.element:hover .icon {
  transform: scale(1.05) rotate(-5deg);
}
```

### Reveal Border
```css
.element::before {
  content: '';
  position: absolute;
  width: 4px;
  height: 100%;
  background: linear-gradient(180deg, #648EB5, #4E8EA2);
  transform: scaleY(0);
  transform-origin: top;
  transition: transform 0.3s ease;
}

.element:hover::before {
  transform: scaleY(1);
}
```

### Ripple Effect
```css
.button::before {
  content: '';
  position: absolute;
  top: 50%;
  left: 50%;
  width: 0;
  height: 0;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.5);
  transform: translate(-50%, -50%);
  transition: width 0.3s, height 0.3s;
}

.button:hover::before {
  width: 100px;
  height: 100px;
}
```

---

## 📱 Responsive Breakpoints

```css
/* Desktop First Approach */

/* Large Desktop (> 1024px) */
@media (min-width: 1025px) {
  /* Full layout, all features visible */
}

/* Tablet (768px - 1024px) */
@media (max-width: 1024px) {
  .stats-cards { flex-wrap: wrap; }
  .stat-card { min-width: calc(50% - 10px); }
}

/* Mobile (< 768px) */
@media (max-width: 768px) {
  .sidebar { left: -270px; }
  .main { margin-left: 0; }
  .stats-cards { flex-direction: column; }
  .jobs-container { grid-template-columns: 1fr; }
}

/* Small Mobile (< 480px) */
@media (max-width: 480px) {
  .top-navbar { font-size: 16px; }
  .btn-primary { font-size: 14px; }
}
```

---

## 🎨 Gradient Recipes

```css
/* Primary Gradient */
background: linear-gradient(135deg, #648EB5 0%, #4E8EA2 100%);

/* Success Gradient */
background: linear-gradient(135deg, #28a745 0%, #218838 100%);

/* Warning Gradient */
background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);

/* Danger Gradient */
background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);

/* Subtle Background Gradient */
background: linear-gradient(90deg, #e8f4fd 0%, #f0f7fc 100%);

/* Badge Gradient (Success) */
background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);

/* Badge Gradient (Warning) */
background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);

/* Badge Gradient (Danger) */
background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
```

---

## 🔧 Usage Examples

### Creating a New Button Type

```css
.btn-custom {
  /* Base styles */
  background: linear-gradient(135deg, #YOUR_COLOR_1 0%, #YOUR_COLOR_2 100%);
  color: #fff;
  padding: 8px 16px;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 500;
  border: none;
  cursor: pointer;
  
  /* Add transition */
  transition: all 0.3s ease;
  
  /* Add icon gap if needed */
  display: inline-flex;
  align-items: center;
  gap: 6px;
}

.btn-custom:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(YOUR_R, YOUR_G, YOUR_B, 0.3);
}
```

### Creating a New Badge

```css
.badge-custom {
  background: linear-gradient(135deg, #LIGHT_COLOR 0%, #DARK_COLOR 100%);
  color: #TEXT_COLOR;
  border: 1px solid #BORDER_COLOR;
  padding: 6px 14px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  white-space: nowrap;
}
```

### Creating a Hover Card Effect

```css
.custom-card {
  /* Base styles */
  background: #FFF;
  border-radius: 12px;
  padding: 24px;
  border: 1px solid rgba(100, 142, 181, 0.1);
  position: relative;
  overflow: hidden;
  
  /* Transition */
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Accent border (optional) */
.custom-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 4px;
  height: 100%;
  background: linear-gradient(180deg, #648EB5 0%, #4E8EA2 100%);
  transform: scaleY(0);
  transform-origin: top;
  transition: transform 0.3s ease;
}

.custom-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 28px rgba(100, 142, 181, 0.18);
  border-color: rgba(100, 142, 181, 0.3);
}

.custom-card:hover::before {
  transform: scaleY(1);
}
```

---

**Quick Copy-Paste Reference**  
**Last Updated:** November 5, 2025  
**Version:** 1.0
