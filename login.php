<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$database = "vmsdb";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['username']) && !empty($_POST['password']) && !empty($_POST['role']) )  {
        $usernameOrMobile = trim($_POST['username']);
        $password = trim($_POST['password']);
        $role = $_POST['role'];

        if ($role == "admin") {
            $query = "SELECT * FROM vms_admin_master WHERE (aemail = ? OR amobile = ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('ss', $usernameOrMobile, $usernameOrMobile);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                $storedPassword = $user['apassword'];

                if ($password === $storedPassword || password_verify($password, $storedPassword)) {
                    $_SESSION['user_id'] = $user['amid'];
                    $_SESSION['user_role'] = "admin";
                    $_SESSION['username'] = $user['aname'];


                    header("Location: admin_home.php");
                    exit;
                } else {
                    $error_message = "Incorrect password!";
                }
            } else {
                $error_message = "Invalid email or mobile number!";
            }

        } elseif ($role == "security") {
            $query = "SELECT * FROM vms_security_master WHERE (semail = ? OR smobile = ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('ss', $usernameOrMobile, $usernameOrMobile);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                $storedPassword = $user['spassword'];

                if ($password === $storedPassword || password_verify($password, $storedPassword)) {
                    $_SESSION['user_id'] = $user['smid'];
                    $_SESSION['user_role'] = "security";
                    $_SESSION['username'] = $user['sname'];


                    header("Location: security_home.php");
                    exit;
                } else {
                    $error_message = "Incorrect password!";
                }
            } else {
                $error_message = "Invalid email or mobile number!";
            }

        } elseif ($role == "owner") {
            // Check in vms_ownera_master first
            $queryA = "SELECT * FROM vms_ownera_master WHERE (oemail = ? OR omobile = ?)";
            $stmtA = $conn->prepare($queryA);
            $stmtA->bind_param('ss', $usernameOrMobile, $usernameOrMobile);
            $stmtA->execute();
            $resultA = $stmtA->get_result();
        
            if ($resultA->num_rows > 0) {
                $user = $resultA->fetch_assoc();
                $storedPassword = $user['opassword'];
        
                if ($password === $storedPassword || password_verify($password, $storedPassword)) {
                    $_SESSION['user_id'] = $user['omid'];
                    $_SESSION['user_role'] = "owner";
                    $_SESSION['username'] = $user['oname'];
                    $_SESSION['user_table'] = "vms_ownera_master"; // Store the table name
                    $_SESSION['building'] = $user['obuilding'];

        
                    header("Location: owner_home.php");
                    exit;
                } else {
                    $error_message = "Incorrect password!";
                }
            }
        
            // If not found in A, check in vms_ownerb_master
            $queryB = "SELECT * FROM vms_ownerb_master WHERE (oemail = ? OR omobile = ?)";
            $stmtB = $conn->prepare($queryB);
            $stmtB->bind_param('ss', $usernameOrMobile, $usernameOrMobile);
            $stmtB->execute();
            $resultB = $stmtB->get_result();
        
            if ($resultB->num_rows > 0) {
                $user = $resultB->fetch_assoc();
                $storedPassword = $user['opassword'];
        
                if ($password === $storedPassword || password_verify($password, $storedPassword)) {
                    $_SESSION['user_id'] = $user['omid'];
                    $_SESSION['user_role'] = "owner";
                    $_SESSION['username'] = $user['oname'];
                    $_SESSION['user_table'] = "vms_ownerb_master"; // Store the table name
                    $_SESSION['building'] = $user['obuilding'];

        
                    header("Location: owner_home.php");
                    exit;
                } else {
                    $error_message = "Incorrect password!";
                }
            } else {
                $error_message = "Invalid email or mobile number!";
            }
        }
        

        } else {
            $error_message = "Invalid role selected!";
        }

    } else {
        $error_message = "Please fill in all fields!";
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="loginbody">
        <div class="login-container">
            <h2>LOGIN</h2>

            <?php if (!empty($error_message)): ?>
                <div class="error-message" style="color: red; font-weight: bold;">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="login.php">
                <div class="role-selection">
                    <input type="radio" id="admin" name="role" value="admin" required>
                    <label for="admin">👨‍💼 Admin</label>

                    <input type="radio" id="owner" name="role" value="owner" required>
                    <label for="owner">🏠 Owner</label>

                    <input type="radio" id="security" name="role" value="security" required>
                    <label for="security">🔒 Security</label>
                </div>

                <input type="text" placeholder="Email or Mobile Number" name="username" required>
                <input type="password" placeholder="Password" name="password" required>
                <input type="submit" value="LOGIN">
            </form>

            <a href="forget_password.php">Forgot password?</a>
        </div>
    </div>
</body>
</html>