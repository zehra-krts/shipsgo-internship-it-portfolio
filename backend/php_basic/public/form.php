<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Form Example</title>
<head>
<body>
    <h2>User Form</h2>
    <form action="index.php" method="POST"> 
        <label for="name">Name:</label>
        <input type="text" name="name" id="name" required><br><br>
        
        <label for="email">Email:</label>
        <input type="text" name="email" id="email" required><br><br>

        <button type="submit">Submit</button>
    </form>
</body>
</html>