<?php
include 'includes/db_connection.php';

if (isset($_GET['token'])) {
    $token = mysqli_real_escape_string($conn, $_GET['token']);
    $query = mysqli_query($conn, "SELECT * FROM vms_users WHERE reset_token='$token' AND reset_expiry > NOW()");
    
    if (mysqli_num_rows($query) > 0) {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $new_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            mysqli_query($conn, "UPDATE vms_users SET password='$new_password', reset_token=NULL, reset_expiry=NULL WHERE reset_token='$token'");
            echo "Your password has been reset. <a href='login.php'>Login here</a>";
        }
    } else {
        echo "Invalid or expired token.";
    }
} else {
    echo "Invalid request.";
}
?>

<form method="POST">
    <input type="password" name="password" required placeholder="New Password">
    <button type="submit">Update Password</button>
</form>
