<?php
declare(strict_types=1);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Task-0004 · Task-2 File Downloader</title>
</head>
<body>
  <h1>Simple File Downloader</h1>
  <form action="download.php" method="post">
    <label for="url">File URL:</label><br>
    <input type="url" id="url" name="url" placeholder="https://example.com/file.pdf" required style="width:400px;" />
    <br><br>
    <button type="submit">Download</button>
  </form>
</body>
</html>