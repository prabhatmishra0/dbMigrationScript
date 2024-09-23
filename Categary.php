<?php

include('db.php');

$conn = new mysqli($servername1, $username1, $password1, $dbname1);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Select the data
$sql = "SELECT * FROM categorytable_01";

$array = array();

if ($result = $conn->query($sql)) {
    // Process the selected data
    while ($row = $result->fetch_assoc()) {
        array_push($array, $row["categoryName"]);
    }
    // we close connection after getting data 
    $conn->close();
    echo "Data get from mdesk database <br>";

    echo "Start Sending data to mdesk_v1 <br>";
    
    foreach ($array as $arr) {
        
        //Create connection
        $conn = new mysqli($servername2, $username2, $password2, $dbname2);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Prepare the SQL statement
        $sql = "INSERT INTO category (outlet_id, name, status, created_by, updated_by, created_on, updated_on) VALUES (?, ?, ?, ?, ?, ?, ?)";

        // Prepare the statement
        $stmt = $conn->prepare($sql);

        // Bind parameters
        $stmt->bind_param("issiiss", $outlet_id, $name, $status, $created_by, $updated_by, $created_on, $updated_on);

        // Set values for the parameters
        $outlet_id = $ot_id; // Replace with your desired outlet ID
        $name = $arr; // Replace with your desired name
        $status = "active"; // Replace with your desired status
        $created_by = 0; // Replace with your desired created_by value
        $updated_by = 0; // Replace with your desired updated_by value
        $created_on = date("Y-m-d H:i:s");
        $updated_on = date("Y-m-d H:i:s");

        // Execute the statement
        if ($stmt->execute()) {
            echo $arr . "New record created successfully <br> ";
        } else {
            echo "Error: from inserting the data record " . $arr . $stmt->error;
        }

        $stmt->close();
        $conn->close();
    }

    echo "Data inserted to mdesk_v1";

    // $row = $result->fetch_assoc();
    // print_r($row["categoryName"]);
} else {
    echo "Error: from fetching Data " . $conn->error;
}

?>