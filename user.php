<?php
include 'db_connection.php';

// Endpoint to retrieve user by ID
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM users WHERE id = $id";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        echo json_encode($user);
    } else {
        echo json_encode(array("error" => "User not found"));
    }
}

// Endpoint to create a new user
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Assuming user data is sent in JSON format in the request body
    $data = json_decode(file_get_contents("php://input"), true);

    // Validate and sanitize input data
    // Example validation: ensure required fields are present
    if (isset($data['email']) && isset($data['password']) && isset($data['username'])) {
        $email = $conn->real_escape_string($data['email']);
        $password = $conn->real_escape_string($data['password']);
        $username = $conn->real_escape_string($data['username']);

        // Hash the password before storing it
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert the new user into the database
        $sql = "INSERT INTO users (email, password, username) VALUES ('$email', '$hashed_password', '$username')";
        if ($conn->query($sql) === TRUE) {
            echo json_encode(array("message" => "User created successfully"));
        } else {
            echo json_encode(array("error" => "Error creating user: " . $conn->error));
        }
    } else {
        echo json_encode(array("error" => "Invalid user data"));
    }
}

// Endpoint to update a user
if ($_SERVER['REQUEST_METHOD'] === 'PUT' && isset($_GET['id'])) {
    $id = $_GET['id'];
    // Assuming user data is sent in JSON format in the request body
    $data = json_decode(file_get_contents("php://input"), true);

    // Validate and sanitize input data
    // Example validation: ensure required fields are present
    if (isset($data['email']) && isset($data['password']) && isset($data['username'])) {
        $email = $conn->real_escape_string($data['email']);
        $password = $conn->real_escape_string($data['password']);
        $username = $conn->real_escape_string($data['username']);

        // Hash the password before storing it
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Update the user in the database
        $sql = "UPDATE users SET email = '$email', password = '$hashed_password', username = '$username' WHERE id = $id";
        if ($conn->query($sql) === TRUE) {
            echo json_encode(array("message" => "User updated successfully"));
        } else {
            echo json_encode(array("error" => "Error updating user: " . $conn->error));
        }
    } else {
        echo json_encode(array("error" => "Invalid user data"));
    }
}

// Endpoint to delete a user
if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($_GET['id'])) {
    $id = $_GET['id'];
    // Delete the user from the database
    $sql = "DELETE FROM users WHERE id = $id";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(array("message" => "User deleted successfully"));
    } else {
        echo json_encode(array("error" => "Error deleting user: " . $conn->error));
    }
}

$conn->close();
?>
