<?php
include 'includes/db_connection.php';

$totalOwnersSql = "SELECT COUNT(*) as total FROM vms_ownera_master";
$totalOwnersResult = $conn->query($totalOwnersSql);
$totalOwnersRow = $totalOwnersResult->fetch_assoc();
$totalOwners = $totalOwnersRow['total'];

$totalSecuritySql = "SELECT COUNT(*) as total FROM vms_security_master";
$totalSecurityResult = $conn->query($totalSecuritySql);
$totalSecurityRow = $totalSecurityResult->fetch_assoc();
$totalSecurity = $totalSecurityRow['total'];

// Handle Owner Registration
if (isset($_POST['owner_submit'])) {
  $oname = mysqli_real_escape_string($conn, $_POST['oname']);
  $omobile = mysqli_real_escape_string($conn, $_POST['omobile']);
  $oemail = mysqli_real_escape_string($conn, $_POST['oemail']);
  $obuilding = mysqli_real_escape_string($conn, $_POST['obuilding']);
  $oroom = mysqli_real_escape_string($conn, $_POST['oroom']);
  $opassword = mysqli_real_escape_string($conn, $_POST['opassword']);
  $cpassword = mysqli_real_escape_string($conn, $_POST['cpassword']);

  if ($opassword !== $cpassword) {
    echo "<script>alert('Passwords do not match!'); window.location.href = 'users.php';</script>";
    exit;
  }

  // Determine the correct table based on the selected building
  $table_name = ($obuilding == 'A') ? 'vms_ownera_master' : 'vms_ownerb_master';

  if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
  }


  $result_email = mysqli_query($conn, "SELECT * FROM $table_name WHERE oemail = '$oemail'");
  if ($result_email) {  // Check if the query executed successfully
    if ($result_email->num_rows > 0) {
      echo "Email already exists.";
    }
  } else {
    echo "Error in email query: " . mysqli_error($conn);
  }

  $result_mobile = mysqli_query($conn, "SELECT * FROM $table_name WHERE omobile = '$omobile'");
  if ($result_mobile) {  // Check if the query executed successfully
    if ($result_mobile->num_rows > 0) {
      echo "Mobile number already exists.";
    }
  } else {
    echo "Error in mobile query: " . mysqli_error($conn);
  }


  // Hash the password
  $hashed_password = password_hash($opassword, PASSWORD_DEFAULT);

  // Insert into the appropriate table
  $sql = "INSERT INTO $table_name (oname, omobile, oemail, obuilding, oroom, opassword, oregistered) 
          VALUES ('$oname', '$omobile', '$oemail', '$obuilding', '$oroom', '$hashed_password', NOW())";

  if ($conn->query($sql) === TRUE) {
    echo "<script>alert('Owner registered successfully in $table_name!'); window.location.href = 'users.php';</script>";
  } else {
    echo "<script>alert('Error: " . $conn->error . "'); window.location.href = 'users.php';</script>";
  }
}


// Handle Security Registration
if (isset($_POST['security_submit'])) {
  $sname = mysqli_real_escape_string($conn, $_POST['sname']);
  $smobile = mysqli_real_escape_string($conn, $_POST['smobile']);
  $semail = mysqli_real_escape_string($conn, $_POST['semail']);
  $saddress = mysqli_real_escape_string($conn, $_POST['saddress']);
  $spassword = mysqli_real_escape_string($conn, $_POST['spassword']);
  $confirm_password = mysqli_real_escape_string($conn, $_POST['cpassword']); // Corrected variable name

  if ($spassword !== $confirm_password) { // Corrected variable name
    echo "<script>
              alert('Passwords do not match!');
              window.location.href = 'users.php';
            </script>";
    exit;
  }

  // Check if email already exists
  $check_email_query = "SELECT * FROM vms_security_master WHERE semail = '$semail'";
  $result_email = $conn->query($check_email_query);

  if ($result_email === false) {
    die("Error in email query: " . $conn->error);
  }

  if ($result_email->num_rows > 0) {
    echo "<script>
              alert('Email $semail already exists! Please choose a different one.');
              window.location.href = 'users.php';
            </script>";
    exit;
  }

  // Check if mobile number already exists
  $check_mobile_query = "SELECT * FROM vms_security_master WHERE smobile = '$smobile'";
  $result_mobile = $conn->query($check_mobile_query);

  if ($result_mobile === false) {
    die("Error in mobile query: " . $conn->error);
  }

  if ($result_mobile->num_rows > 0) {
    echo "<script>
              alert('Mobile number $smobile already exists! Please choose a different one.');
              window.location.href = 'users.php';
            </script>";
    exit;
  }

  // Hash the password
  $hashed_password = password_hash($spassword, PASSWORD_DEFAULT);

  // Insert into database
  $sql = "INSERT INTO vms_security_master (sname, smobile, semail, saddress, spassword, sregistered) 
          VALUES ('$sname', '$smobile', '$semail', '$saddress', '$hashed_password', NOW())";

  if ($conn->query($sql) === TRUE) {
    echo "<script>
              alert('Security registered successfully!');
              window.location.href = 'users.php';
            </script>";
  } else {
    echo "<script>
              alert('Error: " . $conn->error . "');
              window.location.href = 'users.php';
            </script>";
  }
}

?>

<!DOCTYPE html>
<html lang="en">

<!-- Head Start -->
<?php @include("includes/head.php"); ?>
<!-- Head End -->

<body>
  <!-- Topbar Start -->
  <?php @include("includes/topbar.php"); ?>
  <!-- Topbar End -->

  <!-- Navbar Start -->
  <nav class="navbar navbar-expand-lg bg-white navbar-light shadow-sm px-5 py-3 py-lg-0">
    <a href="index.html" class="navbar-brand p-0">
      <h1 class="m-0 text-uppercase text-primary"><i class="far fa-smile text-primary me-2"></i>VisitorOS</h1>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarCollapse">
      <div class="navbar-nav ms-auto py-0 me-n3">
        <a href="admin_home.php" class="nav-item nav-link">Home</a>
        <a href="admin_profile.php" class="nav-item nav-link">Profile</a>
        <div class="nav-item dropdown">
          <a href="#" class="nav-link dropdown-toggle active" id="navbarDropdown" role="button"
            data-bs-toggle="dropdown" aria-expanded="false">User Management</a>
          <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
            <li><a class="dropdown-item active" href="registeruser.php">Register User</a></li>
            <li><a class="dropdown-item" href="change_password.php">Password</a></li>
          </ul>
        </div>
        <a href="logout.php" class="nav-link text-danger fw-bold"><i class="fas fa-sign-out-alt me-1"></i></a>
      </div>
    </div>
  </nav>
  <!-- Navbar End -->

  <div class="dashboard">
    <div class="card business">
      <h3>Owners</h3>
      <p id="totalOwners"><?php echo $totalOwners; ?></p>
    </div>
    <div class="card job">
      <h3>Security</h3>
      <p id="totalSecurity"><?php echo $totalSecurity; ?></p>
    </div>
  </div>

  <!--  SECURITY TABLE START  -->
  <div class="table">
    <div class="table-header">
      <p>All Security</p>
      <div>
        <button type="button" class="block-btn" data-bs-toggle="modal" data-bs-target="#registersecurity">Add
          Security</button>
      </div>
      <div class="modal fade" id="registersecurity" tabindex="-1" aria-labelledby="registerSecurityLabel"
        aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="registerSecurityLabel">Register Security</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <form method="POST" action="users.php" onsubmit="return validatePasswords();">
                <div class="row">

                  <div class="col-md-6 mb-3">
                    <label for="first_name">First Name</label>
                    <input type="text" id="first_name" name="sname" class="form-control" placeholder="Enter First Name"
                      required>
                  </div>

                  <div class="col-md-6 mb-3">
                    <label for="mobile_number">Mobile Number</label>
                    <input type="tel" id="mobile_number" name="smobile" class="form-control"
                      placeholder="Enter Mobile Number" pattern="[0-9]{10}"
                      title="Please enter a valid 10-digit mobile number" required>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="semail" class="form-control" placeholder="Enter Email Address"
                      required>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="address">Address</label>
                    <input type="text" id="address" name="saddress" class="form-control" placeholder="Enter Address"
                      required>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="spassword" class="form-control"
                      placeholder="Enter Password" required>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="cpassword" class="form-control"
                      placeholder="Confirm Password" required>
                  </div>
                  <div class="col-md-12">
                    <button type="submit" name="security_submit" class="register-btn">Register</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- SECURITY TABLE SELECTION START-->
    <div class="table-selection">
      <?php
      $rowsPerPageSecurity = 5;
      $page_security = isset($_GET['page_security']) ? (int) $_GET['page_security'] : 1;
      $startFromSecurity = ($page_security - 1) * $rowsPerPageSecurity;

      $sqlTotalSecurity = "SELECT COUNT(*) as total FROM vms_security_master";
      $resultTotalSecurity = $conn->query($sqlTotalSecurity);
      $rowTotalSecurity = $resultTotalSecurity->fetch_assoc();
      $totalRowsSecurity = $rowTotalSecurity['total'];
      $totalPagesSecurity = ceil($totalRowsSecurity / $rowsPerPageSecurity);

      $sqlSecurity = "SELECT smid,sname, smobile, semail, saddress, sregistered FROM vms_security_master ORDER BY sregistered DESC LIMIT $startFromSecurity, $rowsPerPageSecurity";
      $resultSecurity = $conn->query($sqlSecurity);
      ?>
      <table>
        <thead>
          <tr>
            <th>Sr. No.</th>
            <th> Name</th>
            <th>Mobile Number</th>
            <th>Email</th>
            <th>Address</th>
            <th>Registration Date</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="visitor-table">
          <?php
          if ($resultSecurity->num_rows > 0) {
            $sr_no_security = $startFromSecurity + 1;
            while ($row = $resultSecurity->fetch_assoc()) {
              echo "<tr>
                            <td>" . $sr_no_security . "</td>
                            <td>" . htmlspecialchars($row["sname"]) . "</td>
                            <td>" . htmlspecialchars($row["smobile"]) . "</td>
                            <td>" . htmlspecialchars($row["semail"]) . "</td>
                            <td>" . htmlspecialchars($row["saddress"]) . "</td>
                            <td>" . date("d-M-Y H:i A", strtotime($row["sregistered"])) . "</td>
                            <td>
                            <button class='edit-btn'><i class='fa-solid fa-pen'></i></button>
                            <button class='delete-btn' data-id='" . $row["smid"] . "' data-role='security'><i class='fa-solid fa-trash'></i></button>

                          </td>
                        </tr>";
              $sr_no_security++;
            }
          } else {
            echo "<tr><td colspan='9'>No security personnel found</td></tr>";
          }
          ?>
        </tbody>
      </table>
      <div class="pagination">
        <div><a href="?page_security=1"><i class="fa-solid fa-angles-left"></i></a></div>
        <div><a href="?page_security=<?php echo ($page_security > 1) ? $page_security - 1 : 1; ?>"><i
              class="fa-solid fa-chevron-left"></i></a></div>
        <?php
        for ($i = 1; $i <= $totalPagesSecurity; $i++) {
          $activeClass = ($i == $page_security) ? 'active' : '';
          echo "<div><a href='?page_security=$i' class='$activeClass'>$i</a></div>";
        }
        ?>
        <div><a
            href="?page_security=<?php echo ($page_security < $totalPagesSecurity) ? $page_security + 1 : $totalPagesSecurity; ?>"><i
              class="fa-solid fa-chevron-right"></i></a></div>
        <div><a href="?page_security=<?php echo $totalPagesSecurity; ?>"><i class="fa-solid fa-angles-right"></i></a>
        </div>
      </div>
    </div>
  </div>
  <!-- SECURITY TABLE SELECTION END-->

  <!--  OWNER TABLE START  -->
  <div class="table">
    <div class="table-header">
      <p>All Owner</p>
      <div>
        <button type="button" class="add-btn" data-bs-toggle="modal" data-bs-target="#registeruser">Add Owner</button>
      </div>
      <div class="modal fade" id="registeruser" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="registerModalLabel">Register Owner</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <form method="POST" action="users.php" onsubmit="return validateOwnerPasswords();">
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label for="full_name">Full Name</label>
                    <input type="text" id="full_name" name="oname" class="form-control" placeholder="Enter Full Name"
                      required>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="mobile_number_owner">Mobile Number</label>
                    <input type="tel" id="mobile_number_owner" name="omobile" class="form-control"
                      placeholder="Enter Mobile Number" pattern="[0-9]{10}"
                      title="Please enter a valid 10-digit mobile number" required>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="email_owner">Email</label>
                    <input type="email" id="email_owner" name="oemail" class="form-control" placeholder="Owner Email"
                      required>
                  </div>
                  <!-- <div class="col-md-6 mb-3">
                    <label for="building">Building Number</label>
                    <input type="text" id="building" name="obuilding" class="form-control"
                      placeholder="Enter Building Number" required>
                  </div> -->
                  <div class="col-md-6 mb-3">
                    <label for="building">Building</label>
                    <select id="building" name="obuilding" class="form-control" required>
                      <option value="">Select Building</option>
                      <option value="A">Building A</option>
                      <option value="B">Building B</option>
                    </select>
                  </div>

                  <div class="col-md-6 mb-3">
                    <label for="room">Room Number</label>
                    <input type="text" id="room" name="oroom" class="form-control" placeholder="Enter Room Number"
                      required>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="password_owner">Password</label>
                    <input type="password" id="password_owner" name="opassword" class="form-control"
                      placeholder="Enter Password" required>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="confirm_password_owner">Confirm Password</label>
                    <input type="password" id="confirm_password_owner" name="cpassword" class="form-control"
                      placeholder="Confirm Password" required>
                  </div>
                  <div class="col-md-12">
                    <button type="submit" name="owner_submit" class="register-btn">Register</button>
                  </div>
                </div>
              </form>
              <script>
                function validateOwnerPasswords() {
                  var opassword = document.getElementById("password_owner").value;
                  var cpassword = document.getElementById("confirm_password_owner").value;
                  if (opassword !== cpassword) {
                    alert("Passwords do not match!");
                    return false;
                  }
                  return true;
                }
              </script>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- OWNER TABLE SELECTION -->
    <div class="table-selection">
      <?php
      $rowsPerPageOwner = 5;
      $page_owner = isset($_GET['page_owner']) ? (int) $_GET['page_owner'] : 1;
      $startFromOwner = ($page_owner - 1) * $rowsPerPageOwner;

      $sqlTotalOwner = "SELECT 
      (SELECT COUNT(*) FROM vms_ownera_master) + 
      (SELECT COUNT(*) FROM vms_ownerb_master) 
      AS total";
      $resultTotalOwner = $conn->query($sqlTotalOwner);
      if (!$resultTotalOwner) {
        die("Error: " . $conn->error);
      }
      $rowTotalOwner = $resultTotalOwner->fetch_assoc();
      $totalRowsOwner = $rowTotalOwner['total'];
      $totalPagesOwner = ceil($totalRowsOwner / $rowsPerPageOwner);


      $totalOwnersSql = "SELECT COUNT(*) as total FROM ( SELECT omid FROM vms_ownera_master UNION ALL SELECT omid FROM vms_ownerb_master ) AS combined";
      $totalOwnersResult = $conn->query($totalOwnersSql);
      $totalOwnersRow = $totalOwnersResult->fetch_assoc();
      $totalOwners = $totalOwnersRow['total'];


      $sqlOwners = "SELECT omid, oname, oemail, omobile, obuilding, oroom, oregistered 
              FROM vms_ownera_master 
              UNION ALL 
              SELECT omid, oname, oemail, omobile, obuilding, oroom, oregistered 
              FROM vms_ownerb_master 
              ORDER BY oregistered DESC 
              LIMIT $startFromOwner, $rowsPerPageOwner";

      $resultOwner = $conn->query($sqlOwners);
      if (!$resultOwner) {
        die("Error: " . $conn->error);
      }
      ?>
      <table>
        <thead>
          <tr>
            <th>Sr. No.</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Mobile Number</th>
            <th>Building</th>
            <th>Room</th>
            <th>Registration Date</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="visitor-table">
          <?php
          if ($resultOwner->num_rows > 0) {
            $sr_no_owner = $startFromOwner + 1;
            while ($row = $resultOwner->fetch_assoc()) {
              echo "<tr omid='row_{$row['omid']}'>
                <td>" . $sr_no_owner . "</td>
                <td>" . htmlspecialchars($row["oname"]) . "</td>
                <td>" . htmlspecialchars($row["oemail"]) . "</td>
                <td>" . htmlspecialchars($row["omobile"]) . "</td>
                <td>" . htmlspecialchars($row["obuilding"]) . "</td>
                <td>" . htmlspecialchars($row["oroom"]) . "</td>
                <td>" . date("d-M-Y H:i A", strtotime($row["oregistered"])) . "</td>
                <td>
                    <button class='edit-btn'>Edit</button>
                    <button class='delete-btn' data-id='{$row['omid']}' data-role='owner'><i class='fa-solid fa-trash'></i></button>

                </td>
            </tr>";
              $sr_no_owner++;
            }
          } else {
            echo "<tr><td colspan='9'>No owners registered</td></tr>";
          }
          ?>
        </tbody>
      </table>
      <div class="pagination">
        <div><a href="?page_owner=1"><i class="fa-solid fa-angles-left"></i></a></div>
        <div><a href="?page_owner=<?php echo ($page_owner > 1) ? $page_owner - 1 : 1; ?>"><i
              class="fa-solid fa-chevron-left"></i></a></div>
        <?php
        for ($i = 1; $i <= $totalPagesOwner; $i++) {
          $activeClass = ($i == $page_owner) ? 'active' : '';
          echo "<div><a href='?page_owner=$i' class='$activeClass'>$i</a></div>";
        }
        ?>
        <div><a
            href="?page_owner=<?php echo ($page_owner < $totalPagesOwner) ? $page_owner + 1 : $totalPagesOwner; ?>"><i
              class="fa-solid fa-chevron-right"></i></a></div>
        <div><a href="?page_owner=<?php echo $totalPagesOwner; ?>"><i class="fa-solid fa-angles-right"></i></a></div>
      </div>
    </div>
  </div>
  <!--  OWNER TABLE END  -->

  <!-- Footer Start -->
  <?php @include("includes/footer.php"); ?>
  <!-- Footer End -->

  <?php @include("includes/javalib.php"); ?>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    document.addEventListener("DOMContentLoaded", function() {
      document.getElementById("totalOwners").innerText = "<?php echo $totalOwners; ?>";
      document.getElementById("totalSecurity").innerText = "<?php echo $totalSecurity; ?>";
    });
    

   
  </script>
</body>

</html>