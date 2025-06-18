<?php
session_start();
include 'includes/db_connection.php'; // Database connection





if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || !isset($_SESSION['user_table'])) {
  echo "Unauthorized access!";
  exit();
}


// $user_id = $_SESSION['user_id'];
// $user_role = $_SESSION['user_role'];
// $user_table = $_SESSION['user_table']; // Get correct table name

// $full_name = $mobile = $email = $building = $room = $registered = "";




// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $mobile = $_POST['mobile'];
    $email = $_POST['email'];
    $address = $_POST['address'] ?? null;
    $building = $_POST['building'] ?? null;
    $room = $_POST['room'] ?? null;

    $photoName = null;
    if (!empty($_FILES['photo']['name'])) {
        $photoName = 'uploads/' . time() . '_' . $_FILES['photo']['name'];
        move_uploaded_file($_FILES['photo']['tmp_name'], $photoName);
    }

    if ($_SESSION['user_role'] === 'admin') {
        $query = "UPDATE vms_admin_master SET aname = ?, amobile = ?, aemail = ?" . ($photoName ? ", aphoto = ?" : "") . " WHERE amid = ?";
        $params = $photoName ? [$name, $mobile, $email, $photoName, $user_id] : [$name, $mobile, $email, $user_id];
    } elseif ($_SESSION['user_role'] === 'security') {
        $query = "UPDATE vms_security_master SET sname = ?, smobile = ?, semail = ?, saddress = ?" . ($photoName ? ", sphoto = ?" : "") . " WHERE smid = ?";
        $params = $photoName ? [$name, $mobile, $email, $address, $photoName, $user_id] : [$name, $mobile, $email, $address, $user_id];
    } elseif ($_SESSION['user_role'] === 'owner') {
        // Try updating in ownera first
        $query = "UPDATE vms_ownera_master SET oname = ?, omobile = ?, oemail = ?, obuilding = ?, oroom = ?" . ($photoName ? ", ophoto = ?" : "") . " WHERE omid = ?";
        $params = $photoName ? [$name, $mobile, $email, $building, $room, $photoName, $user_id] : [$name, $mobile, $email, $building, $room, $user_id];

        $stmt = $conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param(str_repeat('s', count($params) - 1) . 'i', ...$params);
            $stmt->execute();
            if ($stmt->affected_rows === 0) {
                // If no row updated in ownera, try ownerb
                $query = "UPDATE vms_ownerb_master SET oname = ?, omobile = ?, oemail = ?, obuilding = ?, oroom = ?" . ($photoName ? ", ophoto = ?" : "") . " WHERE omid = ?";
                $params = $photoName ? [$name, $mobile, $email, $building, $room, $photoName, $user_id] : [$name, $mobile, $email, $building, $room, $user_id];
            }
        }
    }

    if (!isset($stmt) || !$stmt) {
        $stmt = $conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param(str_repeat('s', count($params) - 1) . 'i', ...$params);
        } else {
            die("Database error: " . $conn->error);
        }
    }

    if ($stmt->execute()) {
        $update_message = "Profile updated successfully!";
    } else {
        $update_message = "Error updating profile: " . $stmt->error;
    }
}

// Fetch profile data
$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['user_role']; // Use this instead of $role
$user_table = $_SESSION['user_table']; // Get the correct table name

$query = "";

if ($user_role === 'admin') {
    $query = "SELECT aname, amobile, aemail, aphoto, aregistered FROM vms_admin_master WHERE amid = ?";
} elseif ($user_role === 'security') {
    $query = "SELECT sname, smobile, semail, sphoto, saddress, sregistered FROM vms_security_master WHERE smid = ?";
} elseif ($user_role === 'owner') {
    // Use the correct owner table from session
    $query = "SELECT oname, omobile, oemail, obuilding, oroom, ophoto, oregistered FROM $user_table WHERE omid = ?";
}

$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Database error: " . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
} elseif ($_SESSION['user_role'] === 'owner') {
    // Check in ownerb if not found in ownera
    $query = "SELECT oname, omobile, oemail, obuilding, oroom, ophoto, oregistered FROM vms_ownerb_master WHERE omid = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Database error: " . $conn->error);
    }
    $stmt->bind_param("i", $user_id);
    $stmt->execute(); 
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
    }
}

if (!empty($row)) {
    if ($user_role === 'admin') {
        $full_name = $row['aname'];
        $mobile = $row['amobile'];
        $email = $row['aemail'];
        // $photo = $row['aphoto'];
        $regDate = $row['aregistered'];
    } elseif ($user_role === 'security') {
        $full_name = $row['sname'];
        $mobile = $row['smobile'];
        $email = $row['semail'];
        // $photo = $row['sphoto'];
        $address = $row['saddress'];
        $regDate = $row['sregistered'];
    } elseif ($user_role === 'owner') {
        $full_name = $row['oname'];
        $mobile = $row['omobile'];
        $email = $row['oemail'];
        $building = $row['obuilding'];
        $room = $row['oroom'];
        // $photo = $row['ophoto'];
        $regDate = $row['oregistered'];
    }
} else {
    echo "User not found.";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>CONSULT - Consultancy Website Template</title>
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <!-- Favicon -->
  <link href="img/favicon.ico" rel="icon">

  <!-- Google Web Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@500;600;700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">

  <!-- Icon Font Stylesheet -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

  <!-- Libraries Stylesheet -->
  <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

  <!-- Customized Bootstrap Stylesheet -->
  <link href="css/bootstrap.min.css" rel="stylesheet">

  <!-- Template Stylesheet -->
  <link href="css/style.css" rel="stylesheet">
</head>

<body>
  <!-- Topbar -->
  <?php include("includes/topbar.php"); ?>

  <!-- Flash Message -->
  <?php if (!empty($update_message)): ?>
    <div class="alert alert-info text-center fade-out" role="alert" id="updateAlert">
      <?php echo $update_message; ?>
    </div>
  <?php endif; ?>

  <!-- Profile Section -->
  <div class="container py-4">
    <div class="row justify-content-center">
      <div class="col-md-10 col-lg-8 bg-white shadow rounded p-4">
        <div class="row">
          <!-- Left (Profile Image & Role) -->
          <div class="col-md-4 text-center border-end">
            <img id="profileImage" src="<?php echo $photo; ?>" alt="Profile Image" class="img-fluid rounded-circle mb-3" style="width: 160px; height: 160px; object-fit: cover;">
            <h3 class="text-primary mt-3"><?php echo $full_name; ?></h3>
            <h5><?php echo ucfirst($user_role); ?></h5>

            <button type="button" id="photoBtn" class="btn btn-sm btn-primary mt-2" onclick="handlePhotoButtonClick()">Change Photo</button>
          </div>

          <!-- Right (Profile Form) -->
          <div class="col-md-8">
            <h3 class="text-primary">Profile Details</h3>
            <form id="profileForm" method="POST" action="profile.php" enctype="multipart/form-data">
              <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" name="name" id="full_name" value="<?php echo $full_name; ?>" class="form-control" readonly>
              </div>
              <div class="mb-3">
                <label class="form-label">Mobile Number</label>
                <input type="text" name="mobile" id="mobile_number" value="<?php echo $mobile; ?>" class="form-control" readonly>
              </div>
              <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" id="email" value="<?php echo $email; ?>" class="form-control" readonly>
              </div>

              <?php if ($user_role === 'security'): ?>
                <div class="mb-3">
                  <label class="form-label">Address</label>
                  <input type="text" name="address" id="address" value="<?php echo $address; ?>" class="form-control" readonly>
                </div>
              <?php endif; ?>

              <?php if ($user_role === 'owner'): ?>
                <div class="mb-3">
                  <label class="form-label">Building</label>
                  <input type="text" name="building" id="building" value="<?php echo $building; ?>" class="form-control" readonly>
                </div>
                <div class="mb-3">
                  <label class="form-label">Room</label>
                  <input type="text" name="room" id="room" value="<?php echo $room; ?>" class="form-control" readonly>
                </div>
              <?php endif; ?>

              <div class="mb-3">
                <label class="form-label">Registration Date</label>
                <input type="text" value="<?php echo $regDate; ?>" class="form-control" readonly>
              </div>

              <!-- Hidden input for profile photo upload -->
              <input type="file" id="imageUpload" name="photo" class="d-none" accept="image/*" onchange="updateProfileImage()">

              <!-- Buttons -->
              <div class="d-flex justify-content-between mt-3">
                <button type="button" id="editSaveBtn" class="editing  btn btn-success" onclick="toggleEditMode()">Edit Profile</button>
                <button type="button" class="btn btn-danger" onclick="window.location.href='<?php echo $dashboardUrl; ?>'">Return to Dashboard</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    function toggleEditMode() {
      const btn = document.getElementById("editSaveBtn");
      const fields = ["full_name", "mobile_number", "email"];
      <?php if ($_SESSION['user_role'] === 'security'): ?>
        fields.push("address");
      <?php endif; ?>
      <?php if ($_SESSION['user_role'] === 'owner'): ?>
        fields.push("building", "room");
      <?php endif; ?>

      if (btn.innerText === "Edit Profile") {
        fields.forEach(id => {
          const field = document.getElementById(id);
          field.readOnly = false;
          field.style.color = "black"; // Make text dark when editing
        });
        btn.innerText = "Save Profile";
      } else {
        document.getElementById("profileForm").submit();
      }
    }

    setTimeout(() => {
      const alertBox = document.getElementById("updateAlert");
      if (alertBox) {
        alertBox.style.opacity = '0';
        setTimeout(() => alertBox.style.display = 'none', 500);
      }
    }, 5000);
  </script>

  <?php include("includes/footer.php"); ?>
  <?php include("includes/javalib.php"); ?>
</body>

</html>