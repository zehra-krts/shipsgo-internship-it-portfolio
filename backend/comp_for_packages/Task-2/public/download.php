<?php
declare(strict_types=1);
ini_set('display_errors', '1');
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && empty($_GET['url'])) {
    header('Location: index.php');
    exit;
}

$url = trim($_POST['url'] ?? $_GET['url'] ?? '');
if ($url === '') {
    exit(" No URL provided.");
}
if (!filter_var($url, FILTER_VALIDATE_URL)) {
    exit(" Invalid URL format.");
}
if (stripos($url, 'https://') !== 0) {
    exit(" Only HTTPS downloads are allowed.");
}

$basename = basename(parse_url($url, PHP_URL_PATH) ?: '');
if ($basename === '' || $basename === '/') {
    $basename = 'downloaded_file';
}

$tempFile = tempnam(sys_get_temp_dir(), 'dl_');
$fp = fopen($tempFile, 'w');
$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_FILE            => $fp,
    CURLOPT_FOLLOWLOCATION  => true,
    CURLOPT_TIMEOUT         => 60,
    CURLOPT_FAILONERROR     => true,
    CURLOPT_USERAGENT       => 'Task-0004-Downloader/1.0',
]);
$success  = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$errorMsg = curl_error($ch);
curl_close($ch);
fclose($fp);

if (!$success || $httpCode >= 400) {
    @unlink($tempFile);
    exit("Could not download file. ($errorMsg, HTTP $httpCode)");
}

$filesize = filesize($tempFile) ?: 0;
$mime = mime_content_type($tempFile) ?: 'application/octet-stream';

if (ob_get_length()) { ob_end_clean(); }
header('Content-Description: File Transfer');
header('Content-Type: ' . $mime);
header('Content-Disposition: attachment; filename="' . $basename . '"');
header('Content-Length: ' . (string)$filesize);
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Expires: 0');

readfile($tempFile);
@unlink($tempFile);
exit;