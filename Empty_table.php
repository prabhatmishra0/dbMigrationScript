<?php

echo "<pre>";

include('db.php');

// Connect to Database 2
$conn2 = new mysqli($servername2, $username2, $password2, $dbname2);

// Check connection
if ($conn2->connect_error) {
    die("Connection failed: " . $conn2->connect_error);
}

// here we clean up product table 

// Truncate the tables
$tables = array("category", "addons", "products", "product_addons", "product_variants");
foreach ($tables as $table) {
    $sql = "TRUNCATE TABLE " . $table;
    if ($conn2->query($sql) === TRUE) {
        echo "Table " . $table . " <b color='red' > truncated successfully <b> <br>";
    } else {
        echo "Error truncating table " . $table . ": " . $conn->error . "<br>";
    }
}

?>