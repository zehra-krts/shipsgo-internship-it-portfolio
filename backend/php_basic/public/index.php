<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $errors = []; 

    $name  = htmlspecialchars(trim($_POST['name']  ?? ''), ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars(trim($_POST['email'] ?? ''), ENT_QUOTES, 'UTF-8');

    // Name control
    if (!preg_match('/^[A-Za-zÇĞİÖŞÜçğıöşü\s]{2,}$/u', $name)) {
        $errors[] = "Name: \"$name\" is not valid!";
    }

    // Email kcontrol
    if (!preg_match('/^[A-Za-z0-9._%+\-]+@[A-Za-z0-9.\-]+\.[A-Za-z]{2,}$/', $email)) {
        $errors[] = "Email: \"$email\" is not valid!";
    }

    // List errors
    if (!empty($errors)) {
        echo "<h3>Form Errors:</h3><ul>";
        foreach ($errors as $error) {
            echo "<li>$error</li>"; 
        }
        echo "</ul>";
    } else {
        echo "Form başarıyla doğrulandı ✅";

        $data = "Name: " . $name . " | Email: " . $email . "\n"; 
        file_put_contents("data.txt", $data, FILE_APPEND);

        echo "<h2>Form Data:</h2>";
        echo "Name: " . $name . "<br>";
        echo "Email: " . $email;
    }

} else {
    echo "Please <a href='form.php'>fill the form</a>.";
}
?>
