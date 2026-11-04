---
name: hyva-alpine-component
description: Write interactive frontend JavaScript for this Hyvä theme as a CSP-safe Alpine.js component inside a .phtml template. Use when adding anything that clicks, toggles, slides, counts down, validates or remembers state (banners, sliders, tabs, timers, forms).
---

# CSP-safe Alpine component in a Hyvä template

Hyvä loads Alpine.js once on every page (`vendor/hyva-themes/magento2-theme-module/src/view/base/web/js/alpine3.min.js`).
Our own JS is **not** a separate file. It lives in the template, next to its HTML.

Working examples in this theme:
- `app/design/frontend/Codilar/hyva/Magento_Theme/templates/floating-banner.phtml`: toggle plus sessionStorage
- `app/design/frontend/Codilar/hyva/Magento_Theme/templates/home/hero.phtml`: auto slider with setInterval, dots, `data-*` values

## Pattern

```php
<?php
declare(strict_types=1);

use Hyva\Theme\ViewModel\HyvaCsp;
use Magento\Framework\Escaper;
use Magento\Framework\View\Element\Template;

/** @var Escaper $escaper */
/** @var Template $block */
/** @var HyvaCsp $hyvaCsp */
?>
<div x-data="myThing" data-delay="3000">
    <button type="button" @click="toggle">Toggle</button>
    <div x-show="open" x-cloak>...</div>
    <div x-show="closed" x-cloak>...</div>
    <span :class="badgeClass"></span>
</div>
<script>
    window.addEventListener('alpine:init', () => Alpine.data('myThing', () => ({
        open: false,
        get closed() { return !this.open; },               // negation as a getter
        init() { this.delay = Number(this.$el.dataset.delay) || 3000; },
        toggle() { this.open = !this.open; },
        badgeClass() { return { 'bg-primary text-white': this.open, 'bg-white/50': !this.open }; }
    })), { once: true });
</script>
<?php $hyvaCsp->registerInlineScript() ?>
```

## Rules

- **HTML only names things:** `@click="toggle"`, `x-show="open"`, `:class="badgeClass"`. No expressions such as `open = !open`, `!open`, `active === 1` or `fn(2)` in attributes, because the Alpine CSP build refuses them.
  - Negation: use a getter (`get closed()`).
  - Per-element values: put `data-index="2"` on the element and read `this.$el.dataset.index` in the method, or `event.currentTarget.dataset.index` in a click handler (Alpine passes the event).
- **PHP passes values to JS through `data-*` attributes** on the element, never by echoing PHP into the `<script>`.
- **Always** follow every `<script>` with `<?php $hyvaCsp->registerInlineScript() ?>`.
- Use `{ once: true }` on the `alpine:init` listener, and a unique component name.
- Render the correct **initial state in PHP** (for example first slide visible), so the page looks right before Alpine starts. Add `x-cloak` to things hidden until Alpine runs.
- Timers: keep the id (`this.timer`) and `clearInterval` before starting a new one.
- Wrap `localStorage`/`sessionStorage` in `try { } catch (e) {}`.
- Class names used in `:class` objects must be written out in full in the file, so Tailwind finds them. Then follow the `hyva-rebuild` skill.

## Verify

Load the page in headless Chrome over CDP (Node 22, `--remote-debugging-port`), then:
- click the controls
- read the DOM state with `Runtime.evaluate`
- check that no `Runtime.exceptionThrown` events occurred
