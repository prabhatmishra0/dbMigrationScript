<?php

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


$sql = "SELECT COUNT(*) FROM productstable_01";

$result = $conn1->query($sql);

if($result->num_rows > 0){
    $row = $result->fetch_assoc();
    $count = $row['COUNT(*)'];
}

echo "<br> product start from here";


$sql = "SELECT * FROM productstable_01";

// Execute the query
$result = $conn1->query($sql);

// Check for results
if ($result->num_rows > 0) {
// Output data

$a = 0;
echo $a++ . "product start <br>";
$Total_addons = 0;

while ($pro = $result->fetch_assoc()) {


        // Prepare the SQL statement
        $sql = "INSERT INTO products (outlet_id, category_id, name, thumbnail, short_description, rate, have_addons, have_variant, dietary,
                status, created_by, updated_by, created_on, updated_on) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        // Prepare the statement
        $stmt = $conn2->prepare($sql);

        // Bind parameters
        // iisssiiissiiss myne
        $stmt->bind_param(
            "iisssiiissiiss",
            $outlet_id,
            $category_id,
            $name,
            $thumbnail,
            $short_description,
            $rate,
            $have_addons,
            $have_variant,
            $dietary,
            $status,
            $created_by,
            $updated_by,
            $created_on,
            $updated_on
        );

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
            echo "<br> <b color='red'> going on this scenarion </b>";
            $have_variant = 0;
        }

        $have_addons = 0;
        $dietary = "veg"; // Replace with your desired dietary value
        $status = "active"; // Replace with your desired status
        $created_by = 0; // Replace with your desired created_by value
        $updated_by = 0; // Replace with your desired updated_by value
        $created_on = date("Y-m-d H:i:s");
        $updated_on = date("Y-m-d H:i:s");

        $last_product_id = 0;

        // Execute the statement
        if ($stmt->execute()) {
            $last_product_id = $stmt->insert_id;
            echo "<br>". $a++  . "product inserted <br>" . "product id is :" . $last_product_id;
            // if($have_variant == 1) {
            // create_product_variant($stmt->insert_id, $pro);
            // }
        } else {
            echo "Error: " . $stmt->error;
        }

        if($pro["pCustomization"] == 1){
            
            $loop_length = 0;
            if ($pro["s"] && $pro["m"] && $pro["l"]) {
                $loop_length = 3;
                $Total_addons = $Total_addons + 3;
                echo " <br> here see this product have 3 variant";
            } else if ($pro["s"] && $pro["m"]) {
                $loop_length = 2;
                $Total_addons = $Total_addons + 2;
                echo "<br> here see this product have 2 variant";
            } else if ($pro["s"]) {
                $loop_length = 1;
                $Total_addons = $Total_addons + 1;
                echo "<br> here see this product have 1 variant";
            }
            
            for ($i = 1; $i <= $loop_length; $i++) {
                $insert_sql = "INSERT INTO product_variants (product_id, name, rate, created_by, updated_by, created_on, updated_on) VALUES (?, ?, ?, ?, ?, ?, ?)"; 
                // Prepare the statement 
                $stmt=$conn2->prepare($insert_sql);

                // Bind parameters
                // iisiiis gemini
                // isiiiss
                $stmt->bind_param("isiiiss", $product_id, $name, $rate, $created_by, $updated_by, $created_on, $updated_on);


                echo "<br> product id :" . $last_product_id;
                // Set values for the parameters
                $product_id = $last_product_id; // Replace with your desired product ID

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

                $last_variant_id = 0;

                // Execute the statement
                if ($stmt->execute()) {
                    $last_variant_id = $stmt->insert_id;
                    echo "New varients created <br>" . $a;
                } else {
                    echo "Error: " . $stmt->error;
                }
                
                $stmt->close();

                // here we insert the addons on product_addons table
                // Prepare the SQL statement  to select the last ID

                echo "<br> last variant id : " . $last_variant_id . "<br>";

                if ($i == 1) {
                    insert_product_addons($last_variant_id, "topping_for_small");
                } else if ($i == 2) {
                    insert_product_addons($last_variant_id, "topping_for_medium");
                } else if ($i == 3) {
                    insert_product_addons($last_variant_id, "topping_for_large");
                }

                echo "<br> product variants created";
            }
        } else {
            echo "<br> we don't have variant";
        }

        echo " <br>Total addons : " . $Total_addons;
}

} else {
    echo "product not found";
}