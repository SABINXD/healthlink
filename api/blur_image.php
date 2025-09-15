<?php
header('Content-Type: image/jpeg');
header('Access-Control-Allow-Origin: *');
require_once(__DIR__ . "/config/db.php");

// Get image path from URL
$imagePath = isset($_GET['path']) ? $_GET['path'] : '';
$blurLevel = isset($_GET['blur']) ? (int)$_GET['blur'] : 25;

if (empty($imagePath)) {
    // Return a blank image if no path provided
    $img = imagecreatetruecolor(200, 200);
    $white = imagecolorallocate($img, 255, 255, 255);
    imagefill($img, 0, 0, $white);
    imagejpeg($img);
    imagedestroy($img);
    exit;
}

// Security: Prevent directory traversal
$imagePath = str_replace('../', '', $imagePath);
$fullPath = __DIR__ . '/../web/assets/img/posts/' . $imagePath;

if (!file_exists($fullPath)) {
    // Return a blank image if file doesn't exist
    $img = imagecreatetruecolor(200, 200);
    $white = imagecolorallocate($img, 255, 255, 255);
    imagefill($img, 0, 0, $white);
    imagejpeg($img);
    imagedestroy($img);
    exit;
}

// Load the image
$imageType = exif_imagetype($fullPath);

switch ($imageType) {
    case IMAGETYPE_JPEG:
        $image = imagecreatefromjpeg($fullPath);
        break;
    case IMAGETYPE_PNG:
        $image = imagecreatefrompng($fullPath);
        break;
    case IMAGETYPE_GIF:
        $image = imagecreatefromgif($fullPath);
        break;
    default:
        // Unsupported image type
        $img = imagecreatetruecolor(200, 200);
        $white = imagecolorallocate($img, 255, 255, 255);
        imagefill($img, 0, 0, $white);
        imagejpeg($img);
        imagedestroy($img);
        exit;
}

// Apply blur effect
for ($i = 0; $i < $blurLevel; $i++) {
    imagefilter($image, IMG_FILTER_GAUSSIAN_BLUR);
}

// Output the blurred image
imagejpeg($image, null, 90);
imagedestroy($image);
?>