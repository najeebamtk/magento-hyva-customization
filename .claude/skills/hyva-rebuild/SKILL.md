---
name: hyva-rebuild
description: Rebuild the Codilar/hyva theme CSS and clear Magento caches so frontend changes show up. Use after changing Tailwind classes, theme templates, layout XML or files in web/ (images, css), or when a change "does not show" on hyva.local.
---

# Rebuild the Hyvä theme and refresh caches

Run from `/var/www/hyva`. Pick the steps that match what changed:

| Changed | Steps |
|---|---|
| Tailwind classes in `.phtml` / `.xml`, or files in `web/tailwind/` | 1, 2, 3, 4 |
| Images or other files in `web/` | 2, 3, 4 |
| Layout XML or template text only | 3, 4 |

## 1. Build the CSS

```bash
export NVM_DIR="$HOME/.nvm"; . "$NVM_DIR/nvm.sh"
cd /var/www/hyva/app/design/frontend/Codilar/hyva/web/tailwind && npm run build
```
This writes `app/design/frontend/Codilar/hyva/web/css/styles.css`. If `node_modules/` is missing, run `npm install` first.

## 2. Remove published static files

```bash
cd /var/www/hyva
rm -rf pub/static/frontend/Codilar var/view_preprocessed/pub/static/frontend/Codilar pub/static/deployed_version.txt
```
In developer mode Magento recreates these as symlinks on the next request, and deleting `deployed_version.txt` gives the static URL a new version number (cache busting).

## 3. Clear Magento caches

```bash
php bin/magento cache:flush
```

## 4. Verify

```bash
curl -s -o /dev/null -w '%{http_code}\n' http://hyva.local/
```
- It must print `200`. A `500` usually means invalid layout XML: check `var/log/exception.log` and validate the file with
  `php -r 'new SimpleXMLElement(file_get_contents("<file>"));'`.
- For a new class, confirm it was built: `grep -c '<escaped-class>' app/design/frontend/Codilar/hyva/web/css/styles.css`.
- For visual changes, take a screenshot:
  `google-chrome --headless=new --no-sandbox --hide-scrollbars --window-size=1280,900 --screenshot=<scratchpad>/desktop.png http://hyva.local/`
  (and again at `390,844` for mobile).

Remind the user to hard refresh the browser (Ctrl+Shift+R).
