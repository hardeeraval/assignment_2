<?php
include 'db_connection.php';
echo "Hello";

// Endpoint to retrieve cart items for a user
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];
    $sql = "SELECT * FROM cart WHERE user_id = $user_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $cart_items = array();
        while ($row = $result->fetch_assoc()) {
            $cart_items[] = $row;
        }
        echo json_encode($cart_items);
    } else {
        echo json_encode(array());
    }
}

// Endpoint to add a product to the cart
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Assuming cart item data is sent in JSON format in the request body
    $data = json_decode(file_get_contents("php://input"), true);

    // Validate and sanitize input data
    // Example validation: ensure required fields are present
    if (isset($data['user_id']) && isset($data['product_id']) && isset($data['quantity'])) {
        $user_id = $conn->real_escape_string($data['user_id']);
        $product_id = $conn->real_escape_string($data['product_id']);
        $quantity = $conn->real_escape_string($data['quantity']);

        // Insert the new cart item into the database
        $sql = "INSERT INTO cart (user_id, product_id, quantity) VALUES ('$user_id', '$product_id', '$quantity')";
        if ($conn->query($sql) === TRUE) {
            echo json_encode(array("message" => "Product added to cart successfully"));
        } else {
            echo json_encode(array("error" => "Error adding product to cart: " . $conn->error));
        }
    } else {
        echo json_encode(array("error" => "Invalid cart item data"));
    }
}

// Endpoint to update a cart item
if ($_SERVER['REQUEST_METHOD'] === 'PUT' && isset($_GET['id'])) {
    $id = $_GET['id'];
    // Assuming cart item data is sent in JSON format in the request body
    $data = json_decode(file_get_contents("php://input"), true);

    // Validate and sanitize input data
    // Example validation: ensure required fields are present
    if (isset($data['quantity'])) {
        $quantity = $conn->real_escape_string($data['quantity']);

        // Update the cart item in the database
        $sql = "UPDATE cart SET quantity = '$quantity' WHERE id = $id";
        if ($conn->query($sql) === TRUE) {
            echo json_encode(array("message" => "Cart item updated successfully"));
        } else {
            echo json_encode(array("error" => "Error updating cart item: " . $conn->error));
        }
    } else {
        echo json_encode(array("error" => "Invalid cart item data"));
    }
}

// Endpoint to delete a cart item
if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($_GET['id'])) {
    $id = $_GET['id'];
    // Delete the cart item from the database
    $sql = "DELETE FROM cart WHERE id = $id";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(array("message" => "Cart item deleted successfully"));
    } else {
        echo json_encode(array("error" => "Error deleting cart item: " . $conn->error));
    }
}

$conn->close();
?>
