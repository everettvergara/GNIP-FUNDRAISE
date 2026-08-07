<?php

declare(strict_types=1);

$source = __DIR__.'/../public/images/design/logo-paper-plane.png';
$outputIco = __DIR__.'/../public/favicon.ico';

$sourceImage = imagecreatefrompng($source);
if ($sourceImage === false) {
    fwrite(STDERR, "Failed to load source image.\n");
    exit(1);
}

$sourceWidth = imagesx($sourceImage);
$sourceHeight = imagesy($sourceImage);

// Square crop around the left ampersand-heart mark.
$cropSize = (int) round($sourceHeight * 1.05);
$cropX = 0;
$cropY = (int) max(0, ($sourceHeight - $cropSize) / 2);

$cropped = imagecreatetruecolor($cropSize, $cropSize);
imagealphablending($cropped, false);
imagesavealpha($cropped, true);
$transparent = imagecolorallocatealpha($cropped, 0, 0, 0, 127);
imagefill($cropped, 0, 0, $transparent);

imagecopyresampled(
    $cropped,
    $sourceImage,
    0,
    0,
    $cropX,
    $cropY,
    $cropSize,
    $cropSize,
    min($cropSize, $sourceWidth),
    min($cropSize, $sourceHeight),
);

// Replace near-black background with transparency.
for ($y = 0; $y < $cropSize; $y++) {
    for ($x = 0; $x < $cropSize; $x++) {
        $rgba = imagecolorat($cropped, $x, $y);
        $red = ($rgba >> 16) & 0xFF;
        $green = ($rgba >> 8) & 0xFF;
        $blue = $rgba & 0xFF;

        if ($red < 24 && $green < 24 && $blue < 24) {
            imagesetpixel($cropped, $x, $y, $transparent);
        }
    }
}

$sizes = [16, 32, 48];
$pngFrames = [];

foreach ($sizes as $size) {
    $frame = imagecreatetruecolor($size, $size);
    imagealphablending($frame, false);
    imagesavealpha($frame, true);
    imagefill($frame, 0, 0, $transparent);
    imagecopyresampled($frame, $cropped, 0, 0, 0, 0, $size, $size, $cropSize, $cropSize);
    $pngFrames[$size] = $frame;
}

// Build a simple ICO with 16x16 and 32x32 PNG payloads.
$icoData = pack('vvv', 0, 1, count($pngFrames));
$offset = 6 + (16 * count($pngFrames));
$entries = '';
$payload = '';

foreach ($pngFrames as $size => $frame) {
    ob_start();
    imagepng($frame);
    $png = ob_get_clean();

    $entries .= pack('CCCCvvVV', $size, $size, 0, 0, 1, 32, strlen($png), $offset);
    $payload .= $png;
    $offset += strlen($png);
}

file_put_contents($outputIco, $icoData.$entries.$payload);

imagedestroy($sourceImage);
imagedestroy($cropped);

foreach ($pngFrames as $frame) {
    imagedestroy($frame);
}

echo "Generated {$outputIco}\n";
