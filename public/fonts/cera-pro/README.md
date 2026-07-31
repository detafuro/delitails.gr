# Cera Pro webfonts

Licensed Cera Pro, converted from the original TTFs to `.woff2`.
Full pan-European character set — Greek coverage verified (930 glyphs/style).

| File | font-weight | font-style |
| --- | --- | --- |
| `CeraPro-Light.woff2` | 100–300 | normal |
| `CeraPro-Medium.woff2` | 400–600 (no Regular in the set; Medium doubles as body weight) | normal |
| `CeraPro-Bold.woff2` | 700 | normal |
| `CeraPro-Black.woff2` | 800–900 (headings/buttons) | normal |
| `CeraPro-Italic.woff2` | 100–600 | italic |
| `CeraPro-BlackItalic.woff2` | 700–900 | italic |

`@font-face` rules live in `resources/css/app.css`. Medium + Black are
preloaded in `resources/views/components/layout.blade.php`.

The original `.otf`/`.ttf` sources live in `resources/fonts-src/cera-pro/`
(gitignored, kept out of the public webroot for licensing reasons).
