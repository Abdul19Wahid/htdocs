# Smart Finance - Accessibility Features

## Overview
Smart Finance is built with accessibility in mind to ensure that all users, including those with disabilities, can use the application effectively.

## Keyboard Navigation

### Global Shortcuts
- **Tab** - Navigate forward through interactive elements
- **Shift + Tab** - Navigate backward through interactive elements
- **Enter/Space** - Activate buttons and links
- **Esc** - Close modals and dropdowns
- **Alt + /Q** - Quick add menu (when FAB is visible)

### Form Navigation
- **Tab** - Move to next form field
- **Shift + Tab** - Move to previous form field
- **Enter** - Submit form (when focus is on submit button)
- **Alt + S** - Submit form (with label for attribute setup)

### Navigation Menu
- **Tab** - Focus on navigation links
- **Enter** - Activate navigation link
- **Arrow Keys** - Navigate within submenus (when applicable)

## Screen Reader Support

### ARIA Labels
All interactive elements include appropriate ARIA labels:
- Buttons: `aria-label` for icon-only buttons
- Forms: `aria-label` for form groups, `aria-required` for required fields
- Tables: `role="table"` with proper header associations
- Alerts: `role="alert"` for notifications
- Regions: Semantic HTML (`main`, `nav`, `aside`) and ARIA landmarks

### Skip Links
- Skip to main content link (hidden, visible on focus)
- Skip to navigation link
- Skip to sidebar link

### Semantic HTML
- Proper heading hierarchy (h1, h2, h3, etc.)
- Semantic elements: `<main>`, `<nav>`, `<section>`, `<article>`
- Form elements use proper `<label>` associations
- Lists use `<ul>`, `<ol>`, `<li>` elements

## Color Contrast
- All text meets WCAG AA standards for color contrast
- Alert colors are not the only distinguishing feature
- Icon-only buttons have text labels in ARIA

## Focus States
- All interactive elements have clear focus indicators
- Focus outline is 2px solid #2563eb with 2px offset
- Focus states are visible in light and dark modes
- Focus is never hidden

## Motion & Animation
- All animations respect `prefers-reduced-motion`
- Animations are used for enhancement, not essential information
- Transitions are smooth but quick (< 0.5s typically)

## Forms

### Form Labels
- Every form input has an associated `<label>` element
- Label `for` attribute matches input `id`
- Required fields are marked with `aria-required="true"` and visual indicator (*)

### Form Feedback
- Error messages are associated with inputs via `aria-describedby`
- Help text is associated with inputs via `aria-describedby`
- Validation feedback is announced to screen readers
- Form validation respects browser native validation

### Input Types
- Correct `type` attributes used (email, number, date, etc.)
- `inputmode` hints provided for mobile keyboards
- Placeholder text does not replace labels

## Data Tables
- Table headers use `<th>` with `scope="col"` or `scope="row"`
- Complex tables have `<caption>` or `aria-label`
- Sortable columns have proper ARIA attributes

## Images & Icons
- All images have descriptive `alt` attributes
- Decorative icons are marked with `aria-hidden="true"`
- Icon-only buttons have text in `aria-label`

## Dark Mode
- Dark mode is automatically applied based on system preferences
- Can be manually toggled (preference saved)
- Both themes meet WCAG color contrast requirements
- No color-only indicators used for important information

## Testing Checklist

### Automated Tests
- [ ] axe DevTools - No critical or serious issues
- [ ] WAVE - No errors
- [ ] Lighthouse - Accessibility score > 90

### Manual Tests
- [ ] Navigate entire site using only keyboard (no mouse)
- [ ] Test with screen reader (NVDA, JAWS, or VoiceOver)
- [ ] Test color contrast with contrast checker
- [ ] Test with browser zoom at 200%
- [ ] Test with animations disabled
- [ ] Test all forms with keyboard only
- [ ] Verify proper heading hierarchy

### Browser & Assistive Technology Support
- [ ] Chrome + NVDA
- [ ] Firefox + NVDA
- [ ] Safari + VoiceOver (Mac)
- [ ] Edge + Narrator

## Known Limitations
- Chart.js charts require manual navigation (accessible data table alternative provided)
- File uploads limited to browser support
- Real-time collaboration features may have limited screen reader support

## Resources

### Guidelines
- [WCAG 2.1](https://www.w3.org/WAI/WCAG21/quickref/)
- [ARIA Authoring Practices](https://www.w3.org/WAI/ARIA/apg/)

### Tools
- [axe DevTools](https://www.deque.com/axe/devtools/)
- [WAVE](https://wave.webaim.org/)
- [Lighthouse](https://developers.google.com/web/tools/lighthouse)

## Reporting Accessibility Issues
If you encounter any accessibility issues, please report them with:
- Browser and version
- Assistive technology used (if applicable)
- Steps to reproduce
- Expected vs actual behavior
