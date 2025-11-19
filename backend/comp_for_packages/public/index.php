<?php
// public/index.php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Intervention\Zodiac\Calculator;
use Intervention\Image\ImageManagerStatic as Image;


$DESIRED_SIZE = 512; 
$BG = '#ffffff';
$FG = '#111111';
$FONT_TTF = __DIR__ . '/fonts/DejaVuSans.ttf'; 

// English descriptions
$descriptions = [
    'aries'       => 'Bold, energetic, and action-oriented.',
    'gemini'      => 'Curious, communicative, adaptable.',
    'cancer'      => 'Caring, intuitive, protective of loved ones.',
    'leo'         => 'Confident, creative, loves to shine.',
    'virgo'       => 'Practical, detail-focused, service-oriented.',
    'libra'       => 'Diplomatic, balanced, relationship-driven.',
    'scorpio'     => 'Intense, determined, emotionally deep.',
    'sagittarius' => 'Adventurous, optimistic, seeks truth.',
    'capricorn'   => 'Disciplined, ambitious, responsibility-first.',
    'aquarius'    => 'Independent, innovative, humanitarian.',
    'pisces'      => 'Empathetic, imaginative, spiritually attuned.',
];

$unicodeIcons = [
    'aries' => '♈︎','taurus' => '♉︎','gemini' => '♊︎','cancer' => '♋︎','leo' => '♌︎','virgo' => '♍︎',
    'libra' => '♎︎','scorpio' => '♏︎','sagittarius' => '♐︎','capricorn' => '♑︎','aquarius' => '♒︎','pisces' => '♓︎',
];


function ensurePngIcon(string $name, string $title, ?string $unicode, string $targetPath, string $bg, string $fg, int $size, ?string $ttf): void
{
    if (file_exists($targetPath)) return;

 
    $dir = dirname($targetPath);
    if (!is_dir($dir)) @mkdir($dir, 0777, true);

    
    $img = Image::canvas($size, $size, $bg);

    
    $circleMargin = (int)round($size * 0.06);
    $img->circle($size - 2*$circleMargin, $size/2, $size/2, function ($draw) use ($fg) {
        $draw->background('#f2f2f2');
        $draw->border(2, $fg);
    });

    $hasTtf = $ttf && is_file($ttf);
    if ($unicode && $hasTtf) {
        $img->text($unicode, $size/2, $size/2 - $size*0.04, function($font) use ($ttf, $fg, $size) {
            $font->file($ttf);
            $font->size((int)round($size * 0.52));
            $font->color($fg);
            $font->align('center');
            $font->valign('middle');
        });
    } else {
        
        $img->text($title, $size/2, $size/2, function($font) use ($ttf, $fg, $size) {
            if ($ttf && is_file($ttf)) $font->file($ttf);
            $font->size((int)round($size * 0.14));
            $font->color($fg);
            $font->align('center');
            $font->valign('middle');
        });
    }

    $img->text(ucfirst($title), $size/2, $size - $size*0.08, function($font) use ($ttf, $fg, $size) {
        if ($ttf && is_file($ttf)) $font->file($ttf);
        $font->size((int)round($size * 0.09));
        $font->color('#555555');
        $font->align('center');
        $font->valign('bottom');
    });

    $img->encode('png', 90)->save($targetPath);
}


$inputDate = $_GET['date'] ?? '';
$error = null;
$result = null;

if ($inputDate !== '') {
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $inputDate)) {
        try {
            $zodiac = Calculator::make($inputDate);
            $name   = $zodiac->name();    
            $title  = ucfirst($name);

            $explanation = $descriptions[$name] ?? 'No description available.';
            $pngRel = "zodiac/{$name}.png";
            $pngAbs = __DIR__ . '/' . $pngRel;

           
            ensurePngIcon(
                $name,
                $title,
                $unicodeIcons[$name] ?? null,
                $pngAbs,
                $BG,
                $FG,
                $DESIRED_SIZE,
                file_exists($FONT_TTF) ? $FONT_TTF : null
            );

            $result = [
                'date'        => $inputDate,
                'name'        => $name,
                'title'       => $title,
                'explanation' => $explanation,
                'pngRel'      => $pngRel,
            ];
        } catch (Throwable $e) {
            $error = "Could not calculate zodiac for the given date.";
        }
    } else {
        $error = "Invalid date format. Please use the date picker.";
    }
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Task-0004 / Zodiac Calculator</title>
<style>
    body { font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; margin: 24px; }
    .card { max-width: 680px; border: 1px solid #ccc; border-radius: 12px; padding: 20px; }
    form { display: flex; gap: 12px; margin-bottom: 16px; flex-wrap: wrap; }
    input[type="date"] { padding: 8px 10px; font-size: 1rem; }
    button { padding: 8px 14px; font-size: 1rem; cursor: pointer; border-radius: 8px; border: 1px solid #888; background: #f5f5f5; }
    .row { display: flex; gap: 20px; align-items: center; }
    .imgbox { width: 160px; height: 160px; display: grid; place-items: center; border: 1px dashed #bbb; border-radius: 12px; }
    .imgbox img { max-width: 100%; max-height: 100%; border-radius: 10px; }
    .title { font-size: 1.5rem; font-weight: 700; margin: 0; }
    .muted { color: #666; margin: 4px 0 12px; }
    .err { color: #b00020; margin-top: 10px; }
    .tip { margin-top: 18px; font-size: .95rem; }
</style>
</head>
<body>
    <h1>Task-0004 · Zodiac Calculator</h1>

    <div class="card">
        <form method="get" action="">
            <label>
                <strong>Birth date:</strong>
                <input type="date" name="date" value="<?= htmlspecialchars($inputDate) ?>" required />
            </label>
            <button type="submit">Calculate</button>
        </form>

        <?php if ($error): ?>
            <div class="err"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if (!empty($result)): ?>
            <div class="row">
                <div class="imgbox">
                    <img src="<?= htmlspecialchars($result['pngRel']) ?>" alt="<?= htmlspecialchars($result['title']) ?> icon" />
                </div>
                <div>
                    
                    <p class="title"><?= htmlspecialchars($result['title']) ?></p>
                    <p class="muted">Date: <strong><?= htmlspecialchars($result['date']) ?></strong></p>
                    <p><?= htmlspecialchars($result['explanation']) ?></p>
                </div>
            </div>
        <?php else: ?>
            <p class="tip">Pick a date and click <em>Calculate</em> to auto-generate a PNG with your zodiac.</p>
        <?php endif; ?>
    </div>
</body>
</html>