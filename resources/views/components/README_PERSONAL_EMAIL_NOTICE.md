# Personal Email Notice Component - Usage Guide

## Overview

A reusable Blade component that displays an informational alert for employers using personal email addresses (Gmail, Yahoo, Hotmail, Outlook, Live, AOL, iCloud), explaining the higher verification confidence threshold.

## Features

✅ **Auto-detection** - Automatically detects personal email domains  
✅ **Dismissible** - Users can close the notice with a close button  
✅ **Persistent dismissal** - Remembers dismissal for 7 days using localStorage  
✅ **Responsive** - Adapts to mobile/tablet/desktop screens  
✅ **Dark mode support** - Auto-adjusts shadow for dark mode  
✅ **Animated** - Smooth slide-down entrance and fade-out exit  
✅ **Accessible** - Proper ARIA labels and semantic HTML  
✅ **Reusable** - Clean component that can be used anywhere  

---

## Basic Usage

### Simple (Default)

```blade
<x-personal-email-notice />
```

Shows the full notice with:
- Shield icon
- Full text explaining 90% vs 80% threshold
- User's email address highlighted
- Dismissible close button

---

### Compact Mode

```blade
<x-personal-email-notice :compact="true" />
```

Shows a more compact version suitable for forms/settings pages.

---

### Non-dismissible

```blade
<x-personal-email-notice :dismissible="false" />
```

Shows the notice without a close button (always visible).

---

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `dismissible` | boolean | `true` | Whether the alert can be closed |
| `compact` | boolean | `false` | Use compact styling (smaller padding, font) |

---

## Examples

### Example 1: Employer Dashboard (Full Notice)

**File:** `resources/views/employer/dashboard.blade.php`

```blade
<div class="welcome">Welcome, {{ $user->company_name }}! 👋</div>

{{-- Show personal email notice --}}
<x-personal-email-notice />

{{-- Rest of dashboard content --}}
@if($validation)
    ...
@endif
```

**Result:**
- Full-size notice with dismiss button
- Auto-hides if user dismisses it
- Reappears after 7 days if still using personal email

---

### Example 2: Settings Page (Compact)

**File:** `resources/views/employer/settings.blade.php`

```blade
<div class="field">
    <label>Business Permit (PDF/JPG/PNG)</label>
    
    {{-- Compact notice for form context --}}
    <x-personal-email-notice :compact="true" />
    
    <input type="file" name="business_permit" accept=".pdf,.jpg,.jpeg,.png">
</div>
```

**Result:**
- Compact styling fits well in forms
- Still dismissible and persistent

---

### Example 3: Non-dismissible Notice

```blade
{{-- Critical notice that must always be visible --}}
<x-personal-email-notice :dismissible="false" />
```

**Result:**
- No close button
- Always visible (cannot be dismissed)

---

## Detected Email Domains

The component automatically detects these personal email domains:

- `@gmail.com` / `@gmail.*`
- `@yahoo.com` / `@yahoo.*`
- `@hotmail.com` / `@hotmail.*`
- `@outlook.com` / `@outlook.*`
- `@live.com` / `@live.*`
- `@aol.com` / `@aol.*`
- `@icloud.com` / `@icloud.*`

**Not detected (business emails):**
- `@company.com`
- `@yourbusiness.ph`
- `@store.co`
- Custom domain emails

---

## Dismissal Behavior

### How It Works

1. **User clicks close button** → Notice fades out and is removed
2. **localStorage saves dismissal** → Key: `personalEmailNotice_dismissed_{hash}`
3. **Expiry set to 7 days** → Notice won't show for 7 days
4. **After 7 days** → Notice reappears automatically
5. **Per-user persistence** → Each email address has its own dismissal state

### localStorage Key Format

```javascript
personalEmailNotice_dismissed_{md5_hash_of_email}
```

**Example:**
- Email: `alex@gmail.com`
- Key: `personalEmailNotice_dismissed_2c6ee24b987d4f8e91e6e7b8a7a8b9c0`

### Manual Reset (Testing)

To manually reset the dismissal state:

```javascript
// In browser console
localStorage.removeItem('personalEmailNotice_dismissed_2c6ee24b987d4f8e91e6e7b8a7a8b9c0');
```

Or clear all:

```javascript
localStorage.clear();
```

---

## Styling & Customization

### Colors

The component uses a purple gradient:
- **Start:** `#667eea` (Purple-ish blue)
- **End:** `#764ba2` (Deep purple)

To customize, edit the component file:

```blade
style="background: linear-gradient(135deg, #YOUR_START_COLOR 0%, #YOUR_END_COLOR 100%);"
```

### Font Sizes

| Element | Default | Compact |
|---------|---------|---------|
| Icon | 22px | 18px |
| Title | 15px | 14px |
| Text | 13px | 12px |
| Code | 12px | 11px |

### Animation

**Entrance:** Slide down from top (0.4s ease-out)  
**Exit:** Fade out + slide up (0.3s ease)

---

## Responsive Design

### Desktop (> 640px)

- Horizontal layout (icon + text + button)
- Full padding and spacing

### Mobile (≤ 640px)

- Vertical layout (icon and text stack)
- Close button positioned absolutely (top-right)
- Optimized touch targets

---

## Dark Mode Support

Automatically adjusts for dark mode using CSS media query:

```css
@media (prefers-color-scheme: dark) {
    .personal-email-notice {
        box-shadow: 0 4px 8px rgba(102, 126, 234, 0.4);
    }
}
```

**Effect:** Slightly stronger shadow for better visibility in dark mode.

---

## Accessibility

✅ **ARIA role:** `role="alert"`  
✅ **Button labels:** `aria-label="Dismiss notice"`  
✅ **Keyboard accessible:** Close button is focusable  
✅ **Screen reader friendly:** Semantic HTML structure  
✅ **Color contrast:** White text on dark gradient meets WCAG AA  

---

## File Structure

```
resources/
└── views/
    └── components/
        └── personal-email-notice.blade.php  ← Component file
```

**Component location:** `resources/views/components/personal-email-notice.blade.php`

---

## Testing Checklist

- [ ] Notice appears for `@gmail.com` users
- [ ] Notice appears for `@yahoo.com` users
- [ ] Notice does NOT appear for `@company.com` users
- [ ] Close button dismisses the notice
- [ ] Dismissal persists after page reload
- [ ] Notice reappears after 7 days
- [ ] Compact mode renders correctly
- [ ] Non-dismissible mode hides close button
- [ ] Responsive layout works on mobile
- [ ] Dark mode shadow adjusts correctly
- [ ] Animation is smooth

---

## Browser Compatibility

✅ **Chrome/Edge:** Full support  
✅ **Firefox:** Full support  
✅ **Safari:** Full support  
✅ **Mobile browsers:** Full support  

**localStorage requirement:** All modern browsers (IE11+)

---

## Performance

- **Render time:** < 1ms (negligible)
- **Animation:** Hardware-accelerated (GPU)
- **localStorage:** Synchronous but fast
- **No external dependencies**

---

## Common Issues & Solutions

### Issue: Notice doesn't dismiss

**Solution:** Check browser console for JavaScript errors. Ensure the notice has `id="personalEmailNotice"`.

### Issue: Notice reappears immediately

**Solution:** Check localStorage quota. Clear old entries if storage is full.

### Issue: Styling conflicts

**Solution:** The component uses inline styles to avoid conflicts. Check for `!important` CSS overrides.

---

## Future Enhancements

Possible improvements:

1. **Configurable expiry** - Allow custom dismissal duration
2. **Multiple templates** - Different messages for different contexts
3. **i18n support** - Multi-language support
4. **Custom icons** - Allow icon customization
5. **Analytics tracking** - Track dismissal rates

---

## Support

For issues or questions:
1. Check the component file: `resources/views/components/personal-email-notice.blade.php`
2. Review this guide
3. Test with different email addresses
4. Check browser console for errors

---

**Last Updated:** November 5, 2025  
**Version:** 1.0  
**Component:** `<x-personal-email-notice />`
