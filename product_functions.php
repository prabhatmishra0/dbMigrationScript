<?php

function category_id($name) {
    include("db.php");

    $conn = new mysqli($servername2, $username2, $password2, $dbname2);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Prepare the SQL statement with a WHERE clause using the variable
    $sql = "SELECT id FROM category WHERE name LIKE ?";

    // Prepare the statement
    $stmt2 = $conn->prepare($sql);

    // Bind the parameter
    $stmt2->bind_param("s", $name);

    // Execute the query
    $stmt2->execute();
    $result = $stmt2->get_result();

    // Check for results
    if ($result->num_rows > 0) {
        // Output data
        $row = $result->fetch_assoc();
        // $category_id = $row["id"];
        return $row["id"];
    } else {
        echo $name;
        echo "No results found";
    }

    $stmt2->close();
}


function have_variant() {

    include("db.php");

    $conn = new mysqli($servername2, $username2, $password2, $dbname2);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    $sql = "SELECT have_variant, MAX(id) AS last_id FROM products where have_variant = 1";

    // Execute the query
    $result = $conn->query($sql);

    // Check for results
    if ($result->num_rows > 0) {

        // Fetch the result as an associative array
        $row = $result->fetch_assoc();    
        return array($row["have_variant"], $row["last_id"]);
        
    } else {
        echo "No results found";
    }
}

function insert_product_addons($products_variants_id, $add_key) { 
    include('db.php');

    // Create connection
    $conn = new mysqli($servername2, $username2, $password2, $dbname2);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }   

    // Prepare the SQL statement
    $sql = "INSERT INTO product_addons (source_id, addon_key) VALUES (?, ?)";

    // Prepare the statement
    $stmt = $conn->prepare($sql);

    // Bind parameters
    $stmt->bind_param("is", $source_id, $addon_key);

    // Set values for the parameters
    $source_id = $products_variants_id; // Replace with your desired source_id
    $addon_key = $add_key; // Replace with your desired addon_key

    // Execute the statement
    if ($stmt->execute()) {
        echo "New addons created successfully <br>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

function last_id_variants() {
    include('db.php');

    // Create connection
    $conn = new mysqli($servername2, $username2, $password2, $dbname2);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT MAX(id) AS last_id FROM product_variants";

    // Execute the query
    $result = $conn->query($sql);

    // Check for results
    if ($result->num_rows > 0) {
        // Fetch the result as an associative array
        $row = $result->fetch_assoc();
        return $row['last_id'];
    } else {
        echo "No results found";
    }

}

function create_product_variant($id, $product) {

    if(empty($id) || !is_array($product)) return;
    if(empty($product["s"])){
        $name = "Small";
        $rate = $product["s"];
    }

    // if(empty($product["m"]))
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
    echo "last variant id : " . $last_variant_id . "<br>";

    if ($i == 1) {
        insert_product_addons($last_variant_id, "topping_for_small");
    } else if ($i == 2) {
        insert_product_addons($last_variant_id, "topping_for_medium");
    } else if ($i == 3) {
        insert_product_addons($last_variant_id, "topping_for_large");
    }   
}

?>