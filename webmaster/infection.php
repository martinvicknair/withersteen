<?php
/**
 * This script fetches the file info of the payload file 'infectado.jpg' and outputs it as a downloadable attachment.
 * If the file does not exist, it will output an error message.
 */

// Fetch the file info.
$filePath = 'infectado.jpg';

if (file_exists($filePath)) {
    $fileName = basename($filePath);
    $fileSize = filesize($filePath);

    // Output headers.
    header("Cache-Control: private");
    header("Content-Type: application/stream");
    header("Content-Length: " . $fileSize);
    header("Content-Disposition: attachment; filename=" . $fileName);

    // Output file.
    readfile($filePath);
    exit();
} else {
    die('The provided file path is not valid.');
}
?>
