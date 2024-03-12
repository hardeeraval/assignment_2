<?php
include 'db_connection.php';
echo "Hello";
// Endpoint to retrieve all products
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $sql = "SELECT * FROM products";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $products = array();
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        echo json_encode($products);
    } else {
        echo json_encode(array());
    }
}

// Endpoint to retrieve product by ID
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM products WHERE id = $id";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $product = $result->fetch_assoc();
        echo json_encode($product);
    } else {
        echo json_encode(array("error" => "Product not found"));
    }
}

// Endpoint to create a new product
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Assuming product data is sent in JSON format in the request body
    $data = json_decode(file_get_contents("php://input"), true);

    // Validate and sanitize input data
    // Example validation: ensure required fields are present
    if (isset($data['description']) && isset($data['image']) && isset($data['pricing']) && isset($data['shipping_cost'])) {
        $description = $conn->real_escape_string($data['description']);
        $image = $conn->real_escape_string($data['image']);
        $pricing = $conn->real_escape_string($data['pricing']);
        $shipping_cost = $conn->real_escape_string($data['shipping_cost']);

        // Insert the new product into the database
        $sql = "INSERT INTO products (description, image, pricing, shipping_cost) VALUES ('$description', '$image', '$pricing', '$shipping_cost')";
        if ($conn->query($sql) === TRUE) {
            echo json_encode(array("message" => "Product created successfully"));
        } else {
            echo json_encode(array("error" => "Error creating product: " . $conn->error));
        }
    } else {
        echo json_encode(array("error" => "Invalid product data"));
    }
}

// Endpoint to update a product
if ($_SERVER['REQUEST_METHOD'] === 'PUT' && isset($_GET['id'])) {
    $id = $_GET['id'];
    // Assuming product data is sent in JSON format in the request body
    $data = json_decode(file_get_contents("php://input"), true);

    // Validate and sanitize input data
    // Example validation: ensure required fields are present
    if (isset($data['description']) && isset($data['image']) && isset($data['pricing']) && isset($data['shipping_cost'])) {
        $description = $conn->real_escape_string($data['description']);
        $image = $conn->real_escape_string($data['image']);
        $pricing = $conn->real_escape_string($data['pricing']);
        $shipping_cost = $conn->real_escape_string($data['shipping_cost']);

        // Update the product in the database
        $sql = "UPDATE products SET description = '$description', image = '$image', pricing = '$pricing', shipping_cost = '$shipping_cost' WHERE id = $id";
        if ($conn->query($sql) === TRUE) {
            echo json_encode(array("message" => "Product updated successfully"));
        } else {
            echo json_encode(array("error" => "Error updating product: " . $conn->error));
        }
    } else {
        echo json_encode(array("error" => "Invalid product data"));
    }
}

// Endpoint to delete a product
if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($_GET['id'])) {
    $id = $_GET['id'];
    // Delete the product from the database
    $sql = "DELETE FROM products WHERE id = $id";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(array("message" => "Product deleted successfully"));
    } else {
        echo json_encode(array("error" => "Error deleting product: " . $conn->error));
    }
}

$conn->close();
?>
