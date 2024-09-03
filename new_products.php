<?php

echo "<pre>";

include('db.php');
include("product_functions.php");

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

// Prepare the SQL statement with a LIMIT clause to retrieve the first 10 rows
// $sql = "SELECT * FROM productstable_01 LIMIT 3 where pcatagary=side";
// $sql = "SELECT * FROM productstable_01 WHERE pCatagory='SIDE' LIMIT 5";
$sql = "SELECT * FROM productstable_01";

// Execute the query
$result = $conn1->query($sql);

// Check for results
if ($result->num_rows > 0) {
    // Output data

    $a = 0;
    echo $a++ . "product start <br>";

    while ($pro = $result->fetch_assoc()) {

        // Prepare the SQL statement
        $sql = "INSERT INTO products 
                    (outlet_id, category_id, name, thumbnail, short_description, rate, have_addons, have_variant, dietary, 
                    status, created_by, updated_by, created_on, updated_on) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        // Prepare the statement
        $stmt = $conn2->prepare($sql);

        // Bind parameters
        // iisssiiissiiss myne
        $stmt->bind_param("iisssiiissiiss", $outlet_id, $category_id, $name, $thumbnail, $short_description, $rate, $have_addons, $have_variant, $dietary, $status, $created_by, $updated_by, $created_on, $updated_on);

        // Set values for the parameters
        $outlet_id = "20210101001"; 
        $category_id = category_id($pro["pCatagory"]);
        $name = $pro["pName"]; // Replace with your desired name
        $thumbnail = $pro["pName"]; // Replace with your desired thumbnail URL
        $short_description = $pro["pShortDescription"]; // Replace with your desired short description
        $rate = $pro["pCustomization"] == 1 ? null : $pro["pDisplayPrice"]; // Replace with your desired rate


        // echo $pro["pCustomization"];

        // check for customization
        if ($pro["pCustomization"] == 1) {
            $have_variant = $pro["pCustomization"];
        } else {
            echo "<br color='   red'> going on this scenarion </br>" ;
            $have_variant = 0;
        }

        $have_addons = 0;
        $dietary = "veg"; // Replace with your desired dietary value
        $status = "active"; // Replace with your desired status
        $created_by = 0; // Replace with your desired created_by value
        $updated_by = 0; // Replace with your desired updated_by value
        $created_on = date("Y-m-d H:i:s");
        $updated_on = date("Y-m-d H:i:s");

        // Execute the statement
        if ($stmt->execute()) {
            echo $a . "product inserted <br>";
            if($have_variant == 1) {
                create_product_variant($stmt->insert_id, $pro);
            }
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();

        // ⭐⭐⭐⭐
        // here we create products variants
        // Prepare the SQL statement

        $have_varient = have_variant();

        if($have_varient[0] == 1) {
            $loop_length = 0;
            if ($pro["s"] && $pro["m"] && $pro["l"]) {
                $loop_length = 3;
            } else if ($pro["s"] && $pro["m"]) {
                $loop_length = 2;
            } else if($pro["s"]){   
                $loop_length = 1;
            }

            for ($i = 1; $i <= $loop_length; $i++) {
                $insert_sql = "INSERT INTO product_variants (product_id, name, rate, created_by, updated_by, created_on, updated_on) VALUES (?, ?, ?, ?, ?, ?, ?)";

                // Prepare the statement
                $stmt = $conn2->prepare($insert_sql);

                // Bind parameters
                // iisiiis gemini
                // isiiiss
                $stmt->bind_param("isiiiss", $product_id, $name, $rate, $created_by, $updated_by, $created_on, $updated_on);

                // Set values for the parameters
                $product_id = $have_varient[1]; // Replace with your desired product ID

                if ($i == 1) {
                    $name = "Small";
                    $rate = $pro["s"];
                } else if ($i == 2) {
                    $name = "Medium";
                    $rate = $pro["m"];
                } else {
                    $name = "Large";
                    $rate = $pro["l"];
                }

                $created_by = 0; // Replace with your desired created_by value
                $updated_by = 0; // Replace with your desired updated_by value
                $created_on = date("Y-m-d H:i:s");
                $updated_on = date("Y-m-d H:i:s");

                // Execute the statement
                if ($stmt->execute()) {
                    echo "New varients created <br>" . $a;
                } else {
                    echo "Error: " . $stmt->error;
                }
                $stmt->close();

                // here we insert the addons on product_addons table
                // Prepare the SQL statement  to select the last ID

                $last_variant_id = last_id_variants();
                echo "last variant id : ".$last_variant_id . "<br>";

                if ($i == 1) {
                    insert_product_addons($last_variant_id, "topping_for_small");
                } else if ($i == 2) {
                    insert_product_addons($last_variant_id, "topping_for_medium");
                } else if ($i == 3) {
                    insert_product_addons($last_variant_id, "topping_for_large");
                }   
                
            }
            
            echo "product variants created";
        } else {
            echo "we don't any varient";
        }

    }
   
    
} else {
    echo "0 results";
}

// Close both connections
$conn1->close();
$conn2->close();



?>