<?php

namespace App\Http\Controllers;

use App\Support\Media;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/** First hit generates public/media/{w}/{path}.webp; nginx serves it directly afterwards. */
class MediaController extends Controller
{
    public function __invoke(int $width, string $path): BinaryFileResponse
    {
        abort_unless(in_array($width, Media::WIDTHS, true) || $width <= 2400, 404);
        abort_if(str_contains($path, '..'), 404);

        $original = preg_replace('/\.webp$/i', '', $path);
        $file = Media::generate($original, $width);
        abort_unless($file, 404);

        return response()->file($file, [
            'Content-Type' => 'image/webp',
            'Cache-Control' => 'public, max-age=31536000, immutable',
        ]);
    }
}
