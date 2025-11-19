<?php
namespace App;

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class ImdbTopScraper {
    private Client $http;
    private string $baseUrl = 'https://www.imdb.com/search/title/?groups=top_100&sort=user_rating,desc';

    public function __construct(?Client $client = null)
    {
        $this->http = $client ?? new Client([
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 '
                              . '(KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                'Accept-Language' => 'en-US,en;q=0.9',
            ],
            'timeout' => 15,
        ]);
    }

    public function fetchFirstPage(): array {

        $html = $this->http->get($this->baseUrl)->getBody()->getContents();
        $crawler = new Crawler($html);

    
        // card
        $items = $crawler->filter('.ipc-metadata-list-summary-item__tc');

        $movies = [];
        $items->each(function (Crawler $item) use (&$movies) {
            $title = $this->textOrNull($item->filter('h3.ipc-title__text.ipc-title__text--reduced'));
            $year = $this->textOrNull($item->filter('span.sc-15ac7568-7.cCsint.dli-title-metadata-item'));
            $description = $this->textOrNull($item->filter('div.ipc-html-content-inner-div'));
            $image = $item->filter('div.ipc-media.ipc-media--poster-27x40.ipc-image-media-ratio--poster-27x40.ipc-media--media-radius.ipc-media--base.ipc-media--poster-s.ipc-poster__poster-image.ipc-media__img > img')->attr('src');

                $movies[] = [
                    'title'     => $title,
                    'year'     => $year,
                    'description'     => $description,
                    'image'     => $image,
                ];
        });
       
         return $movies ; 

    }
    private function textOrNull(Crawler $node): ?string
    {
        if ($node->count() === 0) return null;
        $txt = trim($node->first()->text(''));
        return $txt !== '' ? $txt : null;
    }

    private function attrOrNull(Crawler $node, string $attr): ?string
    {
        if ($node->count() === 0) return null;
        $val = $node->first()->attr($attr);
        return $val !== '' ? $val : null;
    }
}