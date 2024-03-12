<?php
include 'db_connection.php';
echo "Hello";

// Endpoint to retrieve comments for a product
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];
    $sql = "SELECT * FROM comments WHERE product_id = $product_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $comments = array();
        while ($row = $result->fetch_assoc()) {
            $comments[] = $row;
        }
        echo json_encode($comments);
    } else {
        echo json_encode(array());
    }
}

// Endpoint to retrieve comment by ID
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM comments WHERE id = $id";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $comment = $result->fetch_assoc();
        echo json_encode($comment);
    } else {
        echo json_encode(array("error" => "Comment not found"));
    }
}

// Endpoint to create a new comment
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Assuming comment data is sent in JSON format in the request body
    $data = json_decode(file_get_contents("php://input"), true);

    // Validate and sanitize input data
    // Example validation: ensure required fields are present
    if (isset($data['product_id']) && isset($data['user_id']) && isset($data['text'])) {
        $product_id = $conn->real_escape_string($data['product_id']);
        $user_id = $conn->real_escape_string($data['user_id']);
        $text = $conn->real_escape_string($data['text']);

        // Insert the new comment into the database
        $sql = "INSERT INTO comments (product_id, user_id, text) VALUES ('$product_id', '$user_id', '$text')";
        if ($conn->query($sql) === TRUE) {
            echo json_encode(array("message" => "Comment created successfully"));
        } else {
            echo json_encode(array("error" => "Error creating comment: " . $conn->error));
        }
    } else {
        echo json_encode(array("error" => "Invalid comment data"));
    }
}

// Endpoint to update a comment
if ($_SERVER['REQUEST_METHOD'] === 'PUT' && isset($_GET['id'])) {
    $id = $_GET['id'];
    // Assuming comment data is sent in JSON format in the request body
    $data = json_decode(file_get_contents("php://input"), true);

    // Validate and sanitize input data
    // Example validation: ensure required fields are present
    if (isset($data['text'])) {
        $text = $conn->real_escape_string($data['text']);

        // Update the comment in the database
        $sql = "UPDATE comments SET text = '$text' WHERE id = $id";
        if ($conn->query($sql) === TRUE) {
            echo json_encode(array("message" => "Comment updated successfully"));
        } else {
            echo json_encode(array("error" => "Error updating comment: " . $conn->error));
        }
    } else {
        echo json_encode(array("error" => "Invalid comment data"));
    }
}

// Endpoint to delete a comment
if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($_GET['id'])) {
    $id = $_GET['id'];
    // Delete the comment from the database
    $sql = "DELETE FROM comments WHERE id = $id";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(array("message" => "Comment deleted successfully"));
    } else {
        echo json_encode(array("error" => "Error deleting comment: " . $conn->error));
    }
}

$conn->close();
?>
