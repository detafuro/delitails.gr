<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

/**
 * On-demand responsive images. Originals live in storage/app/public/{path};
 * resized WebP variants are written to public/media/{width}/{path}.webp on first
 * request (served statically by nginx afterwards).
 */
class Media
{
    public const WIDTHS = [320, 480, 768, 1024, 1400, 1920];
    public const QUALITY = 82;

    public static function original(string $path): ?string
    {
        $full = storage_path('app/public/'.ltrim($path, '/'));

        return is_file($full) ? $full : null;
    }

    /** [width, height] of the original, cached (keyed by mtime so re-uploads refresh). */
    public static function dimensions(string $path): ?array
    {
        $full = self::original($path);
        if (! $full) return null;

        return Cache::rememberForever('media.dim.'.md5($path.'|'.filemtime($full)), function () use ($full) {
            $info = @getimagesize($full);
            return $info ? [$info[0], $info[1]] : null;
        });
    }

    public static function supports(string $path): bool
    {
        return (bool) preg_match('/\.(jpe?g|png|webp)$/i', $path);
    }

    /** Public URL of a variant (generated lazily by MediaController when first hit). */
    public static function url(string $path, int $width): string
    {
        return url('/media/'.$width.'/'.ltrim($path, '/').'.webp');
    }

    /** Candidate widths for srcset (never upscale beyond the original). */
    public static function widths(string $path, array $wanted = self::WIDTHS): array
    {
        $dim = self::dimensions($path);
        $max = $dim[0] ?? max($wanted);
        $w = array_values(array_filter($wanted, fn ($x) => $x <= $max));
        if (! $w || max($w) < $max && $max <= max($wanted)) {
            $w[] = $max; // include the original size when it sits between steps (or is smaller than all)
        }
        sort($w);

        return array_values(array_unique($w));
    }

    /** Build the resized WebP file; returns its absolute path or null on failure. */
    public static function generate(string $path, int $width): ?string
    {
        $src = self::original($path);
        if (! $src || ! self::supports($path)) return null;

        $target = public_path('media/'.$width.'/'.ltrim($path, '/').'.webp');
        if (is_file($target)) return $target;

        $info = @getimagesize($src);
        if (! $info) return null;
        [$ow, $oh, $type] = $info;

        $img = match ($type) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($src),
            IMAGETYPE_PNG => @imagecreatefrompng($src),
            IMAGETYPE_WEBP => @imagecreatefromwebp($src),
            default => null,
        };
        if (! $img) return null;

        $width = min($width, $ow); // no upscaling
        $height = (int) round($oh * $width / $ow);

        $dst = imagecreatetruecolor($width, $height);
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        imagefill($dst, 0, 0, imagecolorallocatealpha($dst, 0, 0, 0, 127));
        imagecopyresampled($dst, $img, 0, 0, 0, 0, $width, $height, $ow, $oh);
        imagedestroy($img);

        @mkdir(dirname($target), 0775, true);
        $ok = imagewebp($dst, $target, self::QUALITY);
        imagedestroy($dst);

        return $ok ? $target : null;
    }
}
