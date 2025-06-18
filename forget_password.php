<?php
session_start();
include('includes/db_connection.php'); 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST['token'];
    $newPassword = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

    // Check token validity
    $stmt = $conn->prepare("SELECT email, user_role FROM password_forget WHERE token = ? AND expires_at > NOW()");
    if (!$stmt) {
        die("Query Error: " . $conn->error);
    }
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    $userData = $result->fetch_assoc();

    if ($userData) {
        $email = $userData['email'];
        $userRole = $userData['user_role'];

        // Determine the correct table to update
        $tableMap = [
            "admin" => "vms_admin_master",
            "security" => "vms_security_master",
            "ownerA" => "vms_ownera_master",
            "ownerB" => "vms_ownerb_master"
        ];

        if (isset($tableMap[$userRole])) {
            $tableName = $tableMap[$userRole];

            // Update the password in the correct table
            $stmt = $conn->prepare("UPDATE $tableName SET password = ? WHERE email = ?");
            if (!$stmt) {
                die("Update Query Error: " . $conn->error);
            }
            $stmt->bind_param("ss", $newPassword, $email);
            $stmt->execute();

            echo "<p style='color: green;'>Password successfully updated. You can now <a href='login.php'>login</a>.</p>";
        } else {
            echo "<p style='color: red;'>Invalid user role.</p>";
        }
    } else {
        echo "<p style='color: red;'>Invalid or expired token.</p>";
    }
}
?>

<!-- Password Reset Form -->
<form method="POST">
    <input type="hidden" name="token" value="<?php echo $_GET['token']; ?>">
    <input type="password" name="new_password" required placeholder="Enter new password">
    <button type="submit">Reset Password</button>
</form>
