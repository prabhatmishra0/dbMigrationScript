<?php
// Database 1 connection details
$servername1 = "localhost";
$username1 = "root";
$password1 = "";
$dbname1 = "mdesk";

// Database 2 connection details
$servername2 = "localhost";
$username2 = "root";
$password2 = "";
$dbname2 = "mdesk_v1";

// Connect to Database 1
$conn1 = new mysqli($servername1, $username1, $password1, $dbname1);

// Check connection
if ($conn1->connect_error) {
    die("Connection failed: " . $conn1->connect_error);
}

// Connect to Database 2
$conn2 = new mysqli($servername2, $username2, $password2, $dbname2);

// Check connection
if ($conn2->connect_error) {
    die("Connection failed: " . $conn2->connect_error);
}

// Prepare the SQL statement to select data from the first table
$sql1 = "SELECT * FROM producttoppings"; // Replace toppings1 with your actual table name

// Execute the query
$result1 = $conn1->query($sql1);

// Check for results
if ($result1->num_rows > 0) {
    // Process the data from the selected rows
    while ($row = $result1->fetch_assoc()) {

        for ($i = 1; $i <= 3; $i++) {
            // this test and script for adding smallRate (other things are same) ✋✋
            // for other rate changing $rate = $row["SmallRate"] to $rate = $row["MediumRate"] ✋✋

            // Set values for the parameters
            $outlet_id = "20210101001"; // Replace with your desired outlet ID
            
            if($i == 1) {
                $addons_key = "topping_for_small";
                $rate = $row["SmallRate"];
            }else if($i==2) {
                $addons_key = "topping_for_medium";
                $rate = $row["MediumRate"];
            } else {
                $addons_key = "topping_for_large";
                $rate = $row["LargeRate"];
            }
            $name = $row["ToppingName"];
            $addon_group = $row["GroupName"]; 
            $created_by = 0; // Replace with your desired created_by value
            $updated_by = 0; // Replace with your desired updated_by value
            $created_on = date("Y-m-d H:i:s");
            $updated_on = date("Y-m-d H:i:s");

            // Prepare the SQL statement
            $sql2 = "INSERT INTO addons (outlet_id, addons_key, name, addon_group, rate, created_by, updated_by, created_on, updated_on) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

            // Prepare the statement for inserting data
            $stmt2 = $conn2->prepare($sql2);

            // Bind parameters
            $stmt2->bind_param(
                "isssiiiss",
                $outlet_id,
                $addons_key,
                $name,
                $addon_group,
                $rate,
                $created_by,
                $updated_by,
                $created_on,
                $updated_on
            );
            // isssiiiss

            $a = 0;

            // Execute the insert statement
            if ($stmt2->execute()) {
                echo $a++ .  "Successfully inserted data for topping: " . $name . "<br>";
            } else {
                echo "Error inserting data: " . $stmt2->error . "<br>";
            }
            // Close the prepared statement
            $stmt2->close();
        }
    }
} else {
    echo "0 results from the first table";
}

// Close both connections
$conn1->close();
$conn2->close();