<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$apiUrl = "https://fakerapi.it/api/v1/persons?_quantity=10" ;

//start url
$ch = curl_init($apiUrl);

curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true, // return string
    CURLOPT_FOLLOWLOCATION => true, // follow direction if there is 
    CURLOPT_USERAGENT      => 'Mozilla/5.0 (Task-0002 Demo)', // ??
    CURLOPT_TIMEOUT        => 15,   // timeout
  ]);

$response = curl_exec($ch);

if ($response === false) {
    die("cURL hatası: " . curl_error($ch));
}
$httpCode = curl_getinfo($ch, CURLINFO_RESPONSE_CODE); // get http situation code (200+, 404notfound, 500serverror)
curl_close($ch);

if ($httpCode < 200 || $httpCode >= 300) { //succesful
    die("HTTP status: $httpCode");
}

$data = json_decode($response, true); //JSON > PHP array
if (json_last_error() !== JSON_ERROR_NONE) { // checking json to php succesfuly
    die("JSON parse error: " . json_last_error_msg());
}
$persons = []; // get required data
foreach ($data['data'] as $p) {
    $persons[] = [
        'firstName' => $p['firstname'],
        'lastName'  => $p['lastname'],
        'email'     => $p['email'],
        'phone'     => $p['phone'],
        'birthday'  => $p['birthday'],
    ];
}

//html table
echo "<h1>Task-0002 / Task-1 — Faker Persons</h1>";
echo "<table border='1' cellpadding='6' cellspacing='0'>";
echo "<tr>
        <th>#</th>
        <th>First Name</th>
        <th>Last Name</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Birthday</th>
      </tr>";
// convert php data that we get > html table
foreach ($persons as $i => $p) { //list of get JSON , <tr> row, ($i+1)
    echo "<tr> 
            <td>".($i+1)."</td>
            <td>{$p['firstName']}</td>
             <td>{$p['lastName']}</td>
             <td>{$p['email']}</td>
             <td>{$p['phone']}</td>
             <td>{$p['birthday']}</td>
             </tr>";
    }
    
echo "</table>";
/*$count = isset($data['data']) ? count($data['data']) : 0; //how many data 

echo "<h1>Task-0002 / Task-1</h1>";
echo "<p>API succesful | Number of people: <b>{$count}</b></p>";
echo "<pre>" . htmlspecialchars(substr($response, 0, 400), ENT_QUOTES, 'UTF-8') . "...\n</pre>";
*/
/* new celaner code, uses html 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faker Persons</title>
</head>
<body>
    <h1>Task-0002 / Task-1 — Faker Persons</h1>
    <table border='1' cellpadding='6' cellspacing='0'>
        <tr>
            <th>#</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Birthday</th>
        </tr>
        <?php foreach ($persons as $i => $p) {?>
        <tr>
            <td><?php echo ($i+1) ?></td>
            <td><?php echo $p['firstName'] ?></td>
            <td><?php echo $p['lastName'] ?></td>
            <td><?php echo $p['email'] ?></td>
            <td><?php echo $p['phone'] ?></td>
            <td><?php echo $p['birthday'] ?></td>
        </tr>
        <?php } ?>
    </table>
</body>
</html>
*/