<?php
session_start();
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "vms";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}       

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id']; 
    $role = $_SESSION['user_role']; 
    $oldPassword = $_POST['oldPassword'];
    $newPassword = $_POST['newPassword'];
    
    // Determine table based on role
    $table = "";
    if ($role == "admin") {
        $table = "admin_master";
    } elseif ($role == "security") {
        $table = "security_master";
    } elseif ($role == "owner") {
        $table = "owner_master";
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid role"]);
        exit;
    }

    function hashPasswords($table) {
        global $conn;
        $query = "SELECT id, password FROM $table";
        $result = $conn->query($query);
    
        while ($row = $result->fetch_assoc()) {
            $hashedPassword = password_hash($row['password'], PASSWORD_DEFAULT);
            $updateQuery = "UPDATE $table SET password = ? WHERE id = ?";
            $stmt = $conn->prepare($updateQuery);
            $stmt->bind_param("si", $hashedPassword, $row['id']);
            $stmt->execute();
        }
        echo "Passwords updated for $table\n";
    }
    
    // Run for all user roles
    hashPasswords("admin_master");
    hashPasswords("security_master");
    hashPasswords("owner_master");

    // Check old password
    $query = "SELECT password FROM $table WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($storedPassword);
    $stmt->fetch();
    $stmt->close();

    if (!$storedPassword || !password_verify($oldPassword, $storedPassword)) {
        echo json_encode(["status" => "error", "message" => "Old password is incorrect"]);
        exit;
    }

    // Hash new password
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    // Update password
    $updateQuery = "UPDATE $table SET password = ? WHERE id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("si", $hashedPassword, $user_id);
    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Password changed successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to change password"]);
    }
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <div class="forgetbody">
        <div class="reset-container">
            <h2>Change Password</h2>
            <form id="changePasswordForm">
                <input type="password" id="oldPassword" placeholder="Old Password" required>
                <input type="password" id="newPassword" placeholder="New Password" required>
                <input type="password" id="confirmPassword" placeholder="Confirm Password" required>
                <input type="submit" value="Reset">
            </form>
            <p id="errorMessage" style="color: red;"></p>
        </div>
    </div>

    <script src="js/main.js"></script>
</body>

</html>