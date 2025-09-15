# Calendar Button Component

A reusable Blade component for adding calendar functionality to hearings across the application.

## Usage

### Basic Usage (Full Button)
```blade
<x-calendar-button :hearing="$hearing" />
```

### Compact Usage (Icon Only)
```blade
<x-calendar-button :hearing="$hearing" compact="true" />
```

## Props

- `hearing` (required): The hearing model instance
- `compact` (optional, default: false): Whether to show the compact version

## Versions

### Full Version (`compact="false"` or omitted)
- Shows "Add to Calendar" text with calendar icon
- Larger button with border and shadow
- Best for detail pages or when space allows

### Compact Version (`compact="true"`)
- Shows only calendar icon with dropdown arrow
- Smaller, gray background button
- Best for list views or tight spaces

## Calendar Options

Both versions provide the same dropdown options:
- **Google Calendar** - Opens Google Calendar with pre-filled event
- **Outlook Calendar** - Opens Outlook web calendar
- **Yahoo Calendar** - Opens Yahoo Calendar
- **Download ICS File** - Downloads universal calendar file

## JavaScript Requirements

Include the calendar button JavaScript on any page using this component:

```blade
<script src="{{ asset('js/calendar-button.js') }}"></script>
```

## Features

- **Multiple instances**: Supports multiple calendar buttons on the same page
- **Unique dropdowns**: Each button has its own dropdown that works independently
- **Click outside to close**: Professional UX behavior
- **Proper escaping**: Handles special characters in event details
- **Timezone support**: Converts times to UTC for universal compatibility

## File Structure

- `resources/views/components/calendar-button.blade.php` - The component
- `public/js/calendar-button.js` - JavaScript functionality
- `app/Http/Controllers/HearingController.php` - Backend calendar methods

## Examples

### On a hearing detail page:
```blade
<div>
    <p class="text-sm font-medium text-gray-500 mb-2">Add to Calendar</p>
    <x-calendar-button :hearing="$hearing" />
</div>
```

### In a hearing list (compact):
```blade
<div class="flex gap-2">
    <a href="{{ route('hearings.show', $hearing) }}" class="btn-primary">
        View Details
    </a>
    <x-calendar-button :hearing="$hearing" compact="true" />
</div>
```
