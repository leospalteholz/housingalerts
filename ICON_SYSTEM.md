# Icon System Refactor

## Overview
Refactored inline SVG icons to use a clean, reusable icon component system that stores SVG files separately and includes them via a Blade component.

## File Structure
```
resources/
├── icons/                     # SVG icon files
│   ├── megaphone.svg         # How to Help section
│   ├── mail.svg              # Email/comments
│   ├── share.svg             # Share functionality
│   ├── facebook.svg          # Facebook sharing
│   ├── x-twitter.svg         # X (Twitter) sharing
│   ├── linkedin.svg          # LinkedIn sharing
│   ├── calendar.svg          # Calendar/date icons
│   ├── external-link.svg     # External links
│   ├── check.svg             # Checkmarks/success
│   ├── eye.svg               # View/visibility
│   ├── edit.svg              # Edit functionality
│   ├── info-circle.svg       # Information/help
│   └── cog.svg               # Settings/configuration
└── views/
    └── components/
        └── icon.blade.php    # Reusable icon component
```

## How to Use

### Basic Usage
```blade
<x-icon name="mail" class="w-5 h-5" />
```

### With Custom Classes
```blade
<x-icon name="facebook" class="w-4 h-4 text-blue-600" />
```

### With Additional Attributes
```blade
<x-icon name="share" class="w-5 h-5 mr-2" title="Share this content" />
```

### Inline Icons
```blade
<x-icon name="check" class="w-3 h-3" inline />
```

## Component Features

### Automatic Fallback
- If an icon doesn't exist, shows a default info-circle icon with error indication
- Helps with development and debugging

### Flexible Sizing
- Default size: `w-5 h-5`
- Override with any Tailwind classes: `w-4 h-4`, `w-6 h-6`, etc.

### Color Inheritance
- Icons inherit text color from parent: `text-blue-600`, `text-red-500`, etc.
- Works with `fill="currentColor"` and `stroke="currentColor"` SVGs

## Adding New Icons

1. **Create SVG file** in `resources/icons/icon-name.svg`
2. **Use semantic naming**: `mail.svg`, `share.svg`, `user.svg`
3. **Optimize SVG**: Remove unnecessary attributes, use `currentColor` for theming
4. **Use in templates**: `<x-icon name="icon-name" class="w-5 h-5" />`

## Example SVG Format
```svg
<svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M..."/>
</svg>
```

## Benefits

### Clean Code
- **Before**: 5-10 lines of inline SVG per icon
- **After**: Single line `<x-icon>` component

### Maintainability
- **Centralized icons** in one directory
- **Easy updates** - change SVG file, affects all usages
- **Consistent sizing** and styling

### Performance
- **Cached** SVG content (read once per request)
- **No HTTP requests** for icon assets
- **Smaller HTML** output

### Developer Experience
- **Autocomplete** for icon names (in IDEs)
- **Fallback handling** for missing icons
- **Flexible styling** with Tailwind classes

## Migration Status

### ✅ Completed
- `resources/views/hearings/show.blade.php` - Replaced 6 major icons
- Created icon component with fallback handling
- Set up icon directory with 12 common icons

### 🚧 Remaining Files to Migrate
- `resources/views/layouts/navigation.blade.php`
- `resources/views/user/dashboard.blade.php`
- Any other files with inline SVG icons

### 📝 Migration Pattern
```blade
<!-- Before -->
<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M..."/>
</svg>

<!-- After -->
<x-icon name="icon-name" class="w-5 h-5" />
```

## Next Steps

1. **Continue migration** of remaining files
2. **Add more icons** as needed (bell, user, home, etc.)
3. **Consider icon packs** like Heroicons or Feather for consistency
4. **Document icon names** for team reference
