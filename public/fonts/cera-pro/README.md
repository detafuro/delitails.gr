# Cera Pro webfonts

Drop your licensed Cera Pro `.woff2` files here with these exact names:

- `CeraPro-Regular.woff2` — weight 400 (body text)
- `CeraPro-Medium.woff2` — weights 500–600
- `CeraPro-Bold.woff2` — weight 700
- `CeraPro-Black.woff2` — weights 800–900 (headings, buttons)

If your license package has `.otf`/`.ttf` only, convert to `.woff2` first
(e.g. https://cloudconvert.com/otf-to-woff2 or `woff2_compress`).

The `@font-face` rules live in `resources/css/app.css`. Until the files are
present the site silently falls back to system fonts (`font-display: swap`).
