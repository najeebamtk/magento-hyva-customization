# Hyvä store (Magento 2.4.9)

Local Magento 2.4.9 store on PHP 8.5 with the Hyvä theme (Tailwind CSS v4, Alpine.js 3) and Hyvä React Checkout.
URL: http://hyva.local/ · database: `hyva` · mode: **developer**

## Where things live

| What | Path |
|---|---|
| **Our theme (work here)** | `app/design/frontend/Codilar/hyva/` (child of `Hyva/default`, active on store view `default`) |
| Theme templates | `app/design/frontend/Codilar/hyva/<Module_Name>/templates/` |
| Theme layout XML | `app/design/frontend/Codilar/hyva/<Module_Name>/layout/` |
| Theme images | `app/design/frontend/Codilar/hyva/web/images/` |
| Tailwind source | `app/design/frontend/Codilar/hyva/web/tailwind/` (`tailwind-source.css`, `base/`, `components/`, `theme/`, `utilities/`) |
| Built CSS (output) | `app/design/frontend/Codilar/hyva/web/css/styles.css`, generated, never edit by hand |
| Our modules | `app/code/Codilar/` (`ReactCheckoutCompat` = PHP 8.4+ fixes for React Checkout) |
| Parent theme (read only) | `vendor/hyva-themes/magento2-default-theme/` |
| Hyvä core / Alpine (read only) | `vendor/hyva-themes/magento2-theme-module/` |
| React Checkout (read only) | `vendor/hyva-themes/magento2-react-checkout/` (prebuilt `react-checkout.js`) |

Home page pieces built so far: hero slider (`Magento_Theme/templates/home/hero.phtml`), category tiles
(`home/categories.phtml`), product sliders (configured in `Magento_Theme/layout/cms_index_index.xml`) and the floating
banner (`Magento_Theme/templates/floating-banner.phtml`, added in `Magento_Theme/layout/default.xml`).

## Rules

- **Never edit `vendor/`.** To change a parent template, copy it into the theme at the same `<Module_Name>/templates/...` path and edit the copy.
- **Layout XML must stay valid.** One broken file (for example line 1 not being `<?xml version="1.0"?>`) gives a 500 error on every page. Check with `php -r 'new SimpleXMLElement(file_get_contents("path.xml"));'`.
- Content that may change (texts, links, images, slides, tiles) goes in **layout XML `<arguments>`**, not hard-coded in templates.
- Tailwind only generates classes it can find in theme `.phtml`/`.xml` files. Write **full class names** (`'opacity-100'`), never build them from pieces (`'opacity-' . $x`).
- Always escape output in templates: `$escaper->escapeHtml()`, `escapeHtmlAttr()`, `escapeUrl()`.
- Resize images before adding them (hero 1920×1000, tiles 800×1000). Keep the original as `*-original.*`.

## JavaScript (Alpine.js, CSP-safe)

There are no separate JS files in the theme. Component JS goes inline in the `.phtml`. Use the pattern from the `hyva-alpine-component` skill:
`x-data="name"` + `Alpine.data('name', ...)` inside `alpine:init`, HTML only references property/method names,
and `<?php $hyvaCsp->registerInlineScript() ?>` right after every `<script>`.

## Commands

Node 22 is installed through nvm, not system-wide. In non-interactive shells load it first:
```bash
export NVM_DIR="$HOME/.nvm"; . "$NVM_DIR/nvm.sh"
```

| Changed | Run |
|---|---|
| Tailwind classes in `.phtml` / `.xml` | `cd app/design/frontend/Codilar/hyva/web/tailwind && npm run build` (or `npm run watch` while working), then cache flush |
| Files in `web/` (images, css) | `rm -rf pub/static/frontend/Codilar var/view_preprocessed/pub/static/frontend/Codilar pub/static/deployed_version.txt`, then cache flush |
| Layout XML, template text | `php bin/magento cache:flush` |
| `di.xml`, new module, PHP classes | `php bin/magento setup:upgrade` (or `setup:di:compile`), then cache flush |

The full routine is in the `hyva-rebuild` skill.

## Git

Commit the theme source **and** the built `web/css/styles.css`. Never commit `node_modules/`, `pub/static/`, `var/`, `generated/` or `app/etc/env.php`.
