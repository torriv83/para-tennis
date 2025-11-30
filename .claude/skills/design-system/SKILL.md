---
name: design-system
description: Use when creating new components, UI elements, Blade templates, Livewire components, or styling anything in this project. Ensures consistent design following the project's design system.
---

# Design System Skill

When creating or modifying UI elements in this project, you MUST follow these design guidelines.

## Color Palette

### Core Colors
| Name | Tailwind Class | Hex | Usage |
|------|----------------|-----|-------|
| Background | `bg-background` | `#05070B` | Main page background |
| Surface | `bg-surface` | `#0D0F14` | Cards, elevated elements |
| Surface Light | `bg-surface-light` | `#1A1D24` | Hover states, borders |

### Accent Colors
| Name | Tailwind Class | Hex | Usage |
|------|----------------|-----|-------|
| Primary | `text-primary`, `bg-primary` | `#FF793F` | Buttons, links, highlights |
| Primary Hover | `hover:bg-primary-hover` | `#FF8F5A` | Button hover states |
| Secondary | `text-secondary`, `bg-secondary` | `#C387FF` | Secondary actions, tags |

### Semantic Colors
| Name | Hex | Usage |
|------|-----|-------|
| Success | `#4ADE80` | Win indicators, positive scenarios |
| Danger | `#F87171` | Loss indicators, warnings |
| Muted | `#6B7280` | Secondary text, disabled states |

### Text Colors
| Name | Tailwind Class | Hex | Usage |
|------|----------------|-----|-------|
| Text Primary | `text-white` | `#FFFFFF` | Headings, important text |
| Text Secondary | `text-text-secondary` | `#A1A1AA` | Body text, labels |
| Text Muted | `text-text-muted` | `#6B7280` | Hints, placeholders |

## Typography

**Font Family:** Space Grotesk (already configured)

| Element | Classes |
|---------|---------|
| H1 | `text-4xl font-semibold` |
| H2 | `text-3xl font-semibold` |
| H3 | `text-xl font-medium` |
| Body | `text-base` |
| Small | `text-sm` |
| Caption | `text-xs` |

## Components

### Cards
```html
<!-- Base card -->
<div class="rounded-xl border border-white/[0.08] bg-surface p-6 backdrop-blur-sm">
    ...
</div>

<!-- Elevated card (hover or important) -->
<div class="rounded-xl bg-gradient-to-br from-surface to-surface-light p-6 shadow-lg">
    ...
</div>
```

### Primary Button
```html
<button class="cursor-pointer rounded-md bg-primary px-6 py-3 font-medium text-white transition hover:bg-primary-hover">
    Button Text
</button>
```

### Secondary Button
```html
<button class="cursor-pointer rounded-md border border-white/20 bg-transparent px-6 py-3 text-white transition hover:border-white/30 hover:bg-white/5">
    Button Text
</button>
```

### Ghost Button
```html
<button class="cursor-pointer px-4 py-2 text-text-secondary transition hover:text-white">
    Button Text
</button>
```

### Inputs
```html
<input type="text" class="rounded-md border border-white/10 bg-surface px-4 py-3 text-white placeholder:text-text-muted focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20">
```

### Result Badges
```html
<!-- Win badge -->
<span class="rounded-full bg-green-400/15 px-3 py-1 text-sm font-medium text-green-400">Win</span>

<!-- Loss badge -->
<span class="rounded-full bg-red-400/15 px-3 py-1 text-sm font-medium text-red-400">Loss</span>
```

## Important Rules

1. **ALWAYS add `cursor-pointer`** to clickable elements (buttons, links, interactive items)
2. **Dark mode only** - no light mode variants needed
3. **Use primary (orange) for highlights** - NOT secondary (purple)
4. **Use existing Tailwind theme colors** defined in the project (`primary`, `surface`, `background`, etc.)
5. **Border radius:** Use `rounded-md` (6px) for small elements, `rounded-xl` (12px) for cards
6. **Transitions:** Always add `transition` class to interactive elements

## Background Gradient (for hero/special sections)
```html
<div class="bg-gradient-to-b from-background to-[#1A0A05]">
    <!-- Subtle warm tint complementing the orange accent -->
</div>
```
