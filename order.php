<?php
include 'db_connection.php';
echo "Hello";

// Endpoint to retrieve orders for a user
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];
    $sql = "SELECT * FROM orders WHERE user_id = $user_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $orders = array();
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }
        echo json_encode($orders);
    } else {
        echo json_encode(array());
    }
}

// Endpoint to retrieve order by ID
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM orders WHERE id = $id";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $order = $result->fetch_assoc();
        echo json_encode($order);
    } else {
        echo json_encode(array("error" => "Order not found"));
    }
}

// Endpoint to create a new order
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Assuming order data is sent in JSON format in the request body
    $data = json_decode(file_get_contents("php://input"), true);

    // Validate and sanitize input data
    // Example validation: ensure required fields are present
    if (isset($data['user_id']) && isset($data['product_id']) && isset($data['quantity'])) {
        $user_id = $conn->real_escape_string($data['user_id']);
        $product_id = $conn->real_escape_string($data['product_id']);
        $quantity = $conn->real_escape_string($data['quantity']);

        // Insert the new order into the database
        $sql = "INSERT INTO orders (user_id, product_id, quantity) VALUES ('$user_id', '$product_id', '$quantity')";
        if ($conn->query($sql) === TRUE) {
            echo json_encode(array("message" => "Order created successfully"));
        } else {
            echo json_encode(array("error" => "Error creating order: " . $conn->error));
        }
    } else {
        echo json_encode(array("error" => "Invalid order data"));
    }
}

// Endpoint to update an existing order
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // Assuming order data is sent in JSON format in the request body
    $data = json_decode(file_get_contents("php://input"), true);

    // Validate and sanitize input data
    // Example validation: ensure required fields are present
    if (isset($data['id']) && isset($data['user_id']) && isset($data['product_id']) && isset($data['quantity'])) {
        $id = $conn->real_escape_string($data['id']);
        $user_id = $conn->real_escape_string($data['user_id']);
        $product_id = $conn->real_escape_string($data['product_id']);
        $quantity = $conn->real_escape_string($data['quantity']);

        // Update the order in the database
        $sql = "UPDATE orders SET user_id='$user_id', product_id='$product_id', quantity='$quantity' WHERE id=$id";
        if ($conn->query($sql) === TRUE) {
            echo json_encode(array("message" => "Order updated successfully"));
        } else {
            echo json_encode(array("error" => "Error updating order: " . $conn->error));
        }
    } else {
        echo json_encode(array("error" => "Invalid order data"));
    }
}

// Endpoint to delete an existing order
if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM orders WHERE id = $id";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(array("message" => "Order deleted successfully"));
    } else {
        echo json_encode(array("error" => "Error deleting order: " . $conn->error));
    }
}

$conn->close();
?>
