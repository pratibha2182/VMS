<?php
session_start();
include 'includes/db_connection.php'; // Include your database connection

// Ensure the user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['user_role'];

// Determine the correct table and columns based on user role
$table = '';
$id_field = '';
$password_field = '';

// Set table and column names
if ($user_role === 'admin') {
    $table = 'vms_admin_master';
    $id_field = 'amid';
    $password_field = 'apassword';
} elseif ($user_role === 'security') {
    $table = 'vms_security_master';
    $id_field = 'smid';
    $password_field = 'spassword';
} elseif ($user_role === 'owner') {
    // Check both owner tables
    $tableA = 'vms_ownera_master';
    $tableB = 'vms_ownerb_master';
    $id_field = 'omid';
    $password_field = 'opassword';

    // Try to find the owner in tableA
    $query = "SELECT '$tableA' as table_name, $password_field FROM $tableA WHERE $id_field = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        // Try to find the owner in tableB
        $query = "SELECT '$tableB' as table_name, $password_field FROM $tableB WHERE $id_field = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
    }

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $table = $row['table_name'];
        $hashed_password = $row[$password_field];
    } else {
        die("❌ Error: Owner not found.");
    }
} else {
    die("❌ Invalid user role.");
}

$message = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old_password = $_POST['opassword'];
    $new_password = $_POST['npassword'];
    $confirm_password = $_POST['cpassword'];

    // Validate fields
    if (empty($old_password) || empty($new_password) || empty($confirm_password)) {
        $message = "⚠️ All fields are required.";
    } elseif ($new_password !== $confirm_password) {
        $message = "❌ New passwords do not match.";
    } else {
        // Fetch the hashed password for Admin & Security
        if ($user_role !== 'owner') {
            $query = "SELECT $password_field FROM $table WHERE $id_field = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows == 1) {
                $row = $result->fetch_assoc();
                $hashed_password = $row[$password_field];
            } else {
                die("❌ Error: User not found.");
            }
        }

        // Verify old password
        if (!password_verify($old_password, $hashed_password)) {
            $message = "❌ Old password is incorrect.";
        } else {
            // Hash new password
            $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            // Update the password
            $update_query = "UPDATE $table SET $password_field = ? WHERE $id_field = ?";
            $update_stmt = $conn->prepare($update_query);
            if (!$update_stmt) {
                $message = "❌ SQL Error: " . $conn->error;
            } else {
                $update_stmt->bind_param("si", $new_hashed_password, $user_id);
                if ($update_stmt->execute()) {
                    $message = "✅ Password updated successfully.";
                } else {
                    $message = "❌ Error updating password.";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .message-box {
            padding: 10px;
            color: #721c24;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
            margin-bottom: 10px;
            text-align: center;
            display: <?php echo empty($message) ? 'none' : 'block'; ?>;
        }
    </style>
</head>
<body>
    <div class="loginbody">
        <div class="login-container">
            <h2>Change Password</h2>
            <?php if (!empty($message)): ?>
                <div class="message-box">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <input type="password" placeholder="Enter Old Password" name="opassword" required>
                <input type="password" placeholder="Enter New Password" name="npassword" required>
                <input type="password" placeholder="Re-Enter Password" name="cpassword" required>
                <input type="submit" value="Change Password">
            </form>
        </div>
    </div>
</body>
</html>
