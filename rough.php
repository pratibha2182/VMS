<?php
session_start();
include("includes/db_connection.php"); // your DB connection file

// Check user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role'])) {
  header("Location: login.php");
  exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['user_role'];

$full_name = $mobile = $email = $address = $building = $room = $photo = $regDate = "";

if ($role === 'admin') {
  $query = "SELECT aname, amobile, aemail, aphoto, aregistered FROM vms_admin_master WHERE amid = ?";
} elseif ($role === 'security') {
  $query = "SELECT sname, smobile, semail, sphoto, saddress, sregistered FROM vms_security_master WHERE smid = ?";
} elseif ($role === 'owner') {
  $query = "SELECT oname, omobile, oemail, obuilding, oroom, ophoto, oregistered FROM vms_ownera_master WHERE omid = ?";
} else {
  // Invalid role
  echo "Invalid user role.";
  exit();
}

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 1) {
  $row = $result->fetch_assoc();
  if ($role === 'admin') {
    $full_name = $row['aname'];
    $mobile = $row['amobile'];
    $email = $row['aemail'];
    $photo = $row['aphoto'];
    $regDate = $row['aregistered'];
  } elseif ($role === 'security') {
    $full_name = $row['sname'];
    $mobile = $row['smobile'];
    $email = $row['semail'];
    $photo = $row['sphoto'];
    $address = $row['saddress'];
    $regDate = $row['sregistered'];
  } elseif ($role === 'owner') {
    $full_name = $row['oname'];
    $mobile = $row['omobile'];
    $email = $row['oemail'];
    $building = $row['obuilding'];
    $room = $row['oroom'];
    $photo = $row['ophoto'];
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
            <h5><?php echo ucfirst($role); ?></h5>

            <button type="button" id="photoBtn" class="btn btn-sm btn-primary mt-2" onclick="handlePhotoButtonClick()">Change Photo</button>
          </div>

          <!-- Right (Profile Form) -->
          <div class="col-md-8">
            <h3 class="text-primary">Profile Details</h3>
            <form id="profileForm" method="POST" action="profile_update.php" enctype="multipart/form-data">
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

              <?php if ($role === 'security'): ?>
                <div class="mb-3">
                  <label class="form-label">Address</label>
                  <input type="text" name="address" id="address" value="<?php echo $address; ?>" class="form-control" readonly>
                </div>
              <?php endif; ?>

              <?php if ($role === 'owner'): ?>
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
      <?php if ($role === 'security'): ?>
        fields.push("address");
      <?php endif; ?>
      <?php if ($role === 'owner'): ?>
        fields.push("building", "room");
      <?php endif; ?>

      if (btn.innerText === "Edit Profile") {
        fields.forEach(id => document.getElementById(id).readOnly = false);
        btn.innerText = "Save Profile";
      } else {
        document.getElementById("profileForm").submit();
      }
    }

    function handlePhotoButtonClick() {
      const photoBtn = document.getElementById("photoBtn");
      const imageInput = document.getElementById("imageUpload");
      if (photoBtn.innerText === "Change Photo") {
        imageInput.click();
      } else if (photoBtn.innerText === "Save Photo") {
        document.getElementById("profileForm").submit();
      }
    }

    function updateProfileImage() {
      const file = document.getElementById("imageUpload").files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = e => {
          document.getElementById("profileImage").src = e.target.result;
          document.getElementById("photoBtn").innerText = "Save Photo";
        };
        reader.readAsDataURL(file);
      }
    }

    setTimeout(() => {
      const alertBox = document.getElementById("updateAlert");
      if (alertBox) {
        alertBox.style.opacity = '0';
        setTimeout(() => alertBox.style.display = 'none', 500);
      }
    }, 5000);

    const editButton = document.querySelector('.edit-button');
    const profileCard = document.querySelector('.profilecard');

    editButton.addEventListener('click', () => {
      profileCard.classList.toggle('editing');
    });
  </script>

  <?php include("includes/footer.php"); ?>
  <?php include("includes/javalib.php"); ?>
</body>

</html>







document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".delete-btn").forEach(button => {
        button.addEventListener("click", function () {
            let userId = this.getAttribute("data-id");
            let userRole = this.getAttribute("data-role");
            let row = this.closest("tr");

            if (confirm("Are you sure you want to delete this user?")) {
                fetch("delete_user.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: `id=${userId}&role=${userRole}`
                })
                .then(response => response.text())
                .then(data => {
                    if (data.trim() === "success") {
                        row.remove(); // Remove the row from the table
                    } else {
                        alert("Error deleting user.");
                    }
                })
                .catch(error => console.error("Error:", error));
            }
        });
    });
});


<button class='delete-btn' data-id='{$row['omid']}' data-role='owner'><i class='fa-solid fa-trash'></i></button>


$sqlOwners = "SELECT omid, oname, oemail, omobile, obuilding, oroom, oregistered 
              FROM vms_ownera_master 
              UNION ALL 
              SELECT omid, oname, oemail, omobile, obuilding, oroom, oregistered 
              FROM vms_ownerb_master 
              ORDER BY oregistered DESC 
              LIMIT $startFromOwner, $rowsPerPageOwner"; 
