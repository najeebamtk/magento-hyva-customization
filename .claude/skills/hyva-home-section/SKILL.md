---
name: hyva-home-section
description: Add or change a home page section (banner, tiles, promo strip, icons, newsletter, etc.) in the Codilar/hyva theme with a template plus layout XML arguments. Use when the user asks for a new block or section on the home page or wants to edit hero slides or category tiles.
---

# Add a home page section

Home page layout: `app/design/frontend/Codilar/hyva/Magento_Theme/layout/cms_index_index.xml`
(handle `cms_index_index`, merged with the parent's). Templates go in
`app/design/frontend/Codilar/hyva/Magento_Theme/templates/home/`.

Current order inside `content`:
1. `home.hero` (slider, `before="-"`)
2. `home.categories`
3. Hyvä's product sliders `slider-1` (Shirts, category 3) and `slider-2` (Shoes, category 6)

## Steps

1. **Template** `Magento_Theme/templates/home/<name>.phtml`:
   - Read settings from `$block->getData('<argument>')` and cast them: `(array)`, `(string)`.
   - Escape all output.
   - Build links with `$block->getBaseUrl() . ltrim($link, '/')`.
   - Build images with `$block->getViewFileUrl('images/<file>')`.
   - Style with Tailwind classes only, mobile first (`grid-cols-1 md:grid-cols-3`).
   - Any JS follows the `hyva-alpine-component` skill.
2. **Layout** inside `<referenceContainer name="content">` in `cms_index_index.xml`:
   ```xml
   <block name="home.<name>" template="Magento_Theme::home/<name>.phtml" after="home.categories">
       <arguments>
           <argument name="title" xsi:type="string">...</argument>
           <argument name="items" xsi:type="array">
               <item name="first" xsi:type="array">
                   <item name="title" xsi:type="string">...</item>
                   <item name="link" xsi:type="string">shoes.html</item>
                   <item name="image" xsi:type="string">images/....jpg</item>
               </item>
           </argument>
       </arguments>
   </block>
   ```
   - Position with `before`/`after`.
   - Texts, links and images belong here, not in the template, so they can be changed without code changes.
   - To remove a parent block: `<referenceBlock name="..." remove="true"/>`.
3. **Images** go in `app/design/frontend/Codilar/hyva/web/images/`.
   - Resize with PHP gd (`imagecopyresampled`) to the display size: hero 1920×1000, tiles 800×1000.
   - Keep the original as `*-original.*`.
4. **Validate the XML** with `php -r 'new SimpleXMLElement(file_get_contents("<file>"));'`, then follow the `hyva-rebuild` skill.
5. **Check desktop (1280) and mobile (390) screenshots.** Make sure nothing is covered by the floating banner (fixed, bottom-right).

## Copy guidelines

Give each section its own job:
- hero: campaigns
- tiles: navigation
- sliders: products
- promo, banner: offers

Use specific button and link text ("Shop Shirts →", "Discover Leather Shoes"), not the same "Shop Now" everywhere.
