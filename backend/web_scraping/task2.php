<?php
 
//Kaynak: https://www.metacritic.com/browse/movies/release-date/theaters/metascore
 
ini_set('display_errors', 1);
error_reporting(E_ALL);

$url = "https://www.metacritic.com/browse/movies/release-date/theaters/metascore";

$ch = curl_init($url); // get with url

curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true, // not print on screen , return string
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_USERAGENT      => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124 Safari/537.36",
    CURLOPT_HTTPHEADER     => [
      "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
      "Accept-Language: en-US,en;q=0.9",
    ], // content type that we accept
    CURLOPT_TIMEOUT        => 20,
  ]);

  $html = curl_exec($ch); //send request

// control 
  if ($html === false) {
    $err = curl_error($ch);
    curl_close($ch);
    die("cURL error: " . htmlspecialchars($err, ENT_QUOTES, 'UTF-8'));
  }
  $code = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
  curl_close($ch);
  if ($code < 200 || $code >= 300) {
    die("HTTP $code");
  }

  // clean white space between html tags
  $html = trim(preg_replace('/>\s+</', '><', $html));
  $pageSource = preg_replace('/\s+/', ' ', $html);

  //var_dump($pageSource);
  //exit;

  //preg_match_all('/<div class="c-finderProductCard_info u-flexbox-column">(.*?)<\/div>/',$pageSource,$matchedMovies);
 // print_r($matchedMovies[1]);
 // Title block
$patternCard = '~<div\s+data-testid="filter-results"\s+class="c-finderProductCard">(.*?)<div\s+data-testid="filter-results"~si';
preg_match_all($patternCard, $pageSource, $matchedCards, PREG_SET_ORDER);

$movies = [];

if (!empty($matchedCards)) {
    foreach ($matchedCards as $c) {

        $block = $c[1];

        // TITLE 
        $title = '';
        if (preg_match('/data-title="([^"]+)"/i', $block, $mTitle)) {
            $title = trim($mTitle[1]); }

        // INFO 
        $info = '';

        if (preg_match('/<div class="c-finderProductCard_description"><span>(.*?)<\/span>/', $block, $mInfo)) {
            $info = trim(preg_replace('/\s+/', ' ', strip_tags($mInfo[1])));
        } 
        // DATE
        $date = '';

        if (preg_match('/<div class="c-finderProductCard_meta"><span class="u-text-uppercase">(.*?)<\/span>/', $block, $mdate)) {
            $date = trim($mdate[1]);
        }


        if ($title !== '') {
            $movies[] = [
                'name'      => $title,
                'title'     => $title,
                'info'      => $info,
                'date'      => $date,
            ];
        }
    }
} else {
    if (preg_match_all('/data-title="([^"]+)"/i', $pageSource, $allTitles)) {
        foreach ($allTitles[1] as $t) {
            $title = trim($t);
            $movies[] = [
                'name'      => $title,
                'title'     => $title,
                'info'      => $info,   
                'date'      => $date,
            ];
        }
    }
}

// test
?>
<!doctype html>
<html lang="en">
<head><meta charset="utf-8"><title>Movies (Titles + Info + Date)</title></head>
<body>
<h1>Movies — Titles + Info + Date</h1>
<?php if (empty($movies)) { ?>
  <p>Movie not found.</p>
<?php } else { ?>
    <table>
    <tr>
      <th>#</th>
      <th>Title</th>
      <th>Info</th>
      <th>Date</th>
    </tr>
    <?php foreach ($movies as $i => $m) { ?>
      <tr>
        <td><?= $i+1 ?></td>
        <td><?= htmlspecialchars($m['title'], ENT_QUOTES, 'UTF-8') ?></td>
        <td><?= htmlspecialchars($m['info'], ENT_QUOTES, 'UTF-8') ?></td>
        <td><?= htmlspecialchars($m['date'], ENT_QUOTES, 'UTF-8') ?></td>
      </tr>
    <?php } ?>
  </table>
<?php } ?>
</body>
</html>