<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\ImdbTopScraper;

// CLI’dan: php public/scrape.php
// Tarayıcıdan da açabilirsin.

header('Content-Type: application/json; charset=utf-8');

try {
    $scraper = new ImdbTopScraper();
    $movies = $scraper->fetchFirstPage();

    echo json_encode([
        'count' => count($movies),
        'items' => $movies,
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage(),
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}