<?php
    // Include solat.php file
    include 'solat.php';
    // Generate JSON file & save it to local
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate JSON file</title>
</head>
<body>
    <!-- Form generate all JSON file -->
    <form action="generate.php" method="get">
        <label>Year:</label>
        <input type="number" name="tahun" min="1900" max="2099" value="<?php echo date("Y"); ?>">
        <input type="submit" value="Generate">
</body>
</html>