<!-- to add the data in visitor master table  -->
<?php
session_start();

include 'includes/db_connection.php'; // Database connection




// Set the number of rows per page start
$rowsPerPage = 5;

$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;

$startFrom = ($page - 1) * $rowsPerPage;

$sqlTotal = "SELECT COUNT(*) as total FROM vms_visitor_master";
$resultTotal = $conn->query($sqlTotal);
$rowTotal = $resultTotal->fetch_assoc();
$totalRows = $rowTotal['total'];

$totalPages = ceil($totalRows / $rowsPerPage);
// Set the number of rows per page end

// Fetch the visitor data for the current page start
$sql = "SELECT vmid, vname, vmobile, vemail, vmeet, vreason, vbuilding, vroom, vaddress, vin, vout, vstatus  
FROM vms_visitor_master 
ORDER BY vin DESC LIMIT $startFrom, $rowsPerPage";

$result = $conn->query($sql);
// Fetch the visitor data for the current page start

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $vname = $conn->real_escape_string($_POST['vname']);
  $vemail = $conn->real_escape_string($_POST['vemail']);
  $vmobile = $conn->real_escape_string($_POST['vmobile']);
  $vmeet = $conn->real_escape_string($_POST['vmeet']);
  $vreason = $conn->real_escape_string($_POST['vreason']);
  $vbuilding = $conn->real_escape_string($_POST['vbuilding']);
  $vroom = $conn->real_escape_string($_POST['vroom']);
  $vaddress = $conn->real_escape_string($_POST['vaddress']);
  $vin = date('Y-m-d H:i:s A'); // Auto-set in-time
  $vstatus = 'in'; // Default status

  // Determine the correct owner table based on the building
  if ($vbuilding == 'A') {
    $owner_table = "vms_ownera_master";
} elseif ($vbuilding == 'B') {
    $owner_table = "vms_ownerb_master";
} else {
    die("Invalid building number: " . $vbuilding);
}



  // Fetch owner details based on room number
  $owner_sql = "SELECT omid, oname, oemail FROM $owner_table WHERE oroom = '$vroom' AND obuilding = '$vbuilding' LIMIT 1";
  echo "Debug: " . $owner_sql; // Print the query to check correctness
  $owner_result = $conn->query($owner_sql);

  if ($owner_result->num_rows > 0) {
    $owner = $owner_result->fetch_assoc();
    $omid = $owner['omid'];
    $oname = $owner['oname'];
    $oemail = $owner['oemail'];

    // Insert into visitor master table
    $sql = "INSERT INTO vms_visitor_master (vname, vemail, vmobile, vmeet, vreason, vbuilding, vroom, vaddress, vin, vout, vstatus) 
            VALUES ('$vname', '$vemail', '$vmobile', '$vmeet', '$vreason', '$vbuilding', '$vroom', '$vaddress', '$vin', NULL, '$vstatus')";

    if ($conn->query($sql) === TRUE) {
      // Insert request into owner's dashboard table
      $request_sql = "INSERT INTO vms_requests (visitor_name, visitor_email, visitor_mobile, reason, owner_id, vbuilding, request_date, status) 
                VALUES ('$vname', '$vemail', '$vmobile', '$vreason', '$omid', '$vbuilding', '$vin', 'Pending')";


      if ($conn->query($request_sql) === TRUE) {
        echo "<script>
              alert('Visitor Registered and Request Sent to Owner Successfully!');
              window.location.href = 'security_home.php';
            </script>";
        exit();
      } else {
        echo "Error inserting into vms_requests: " . $conn->error;
      }
    } else {
      echo "Error inserting into vms_visitor_master: " . $conn->error;
    }

  } else {
    echo "<script>alert('Error: No owner found for this room!');</script>";
  }
}




// Count total visitors
$sqlTotalVisitors = "SELECT COUNT(*) as total FROM vms_visitor_master";
$resultTotalVisitors = $conn->query($sqlTotalVisitors);
$rowTotalVisitors = $resultTotalVisitors->fetch_assoc();
$totalVisitors = $rowTotalVisitors['total'];

// Count today's visitors
$sqlTotalOwners = "SELECT COUNT(*) as total FROM vms_visitor_master WHERE DATE(vin) = CURDATE()";
$resultTotalOwners = $conn->query($sqlTotalOwners);
$rowTotalOwners = $resultTotalOwners->fetch_assoc();
$totalOwners = $rowTotalOwners['total'];

// Count last 7 days' visitors
$sqlTotalSecurity = "SELECT COUNT(*) as total FROM vms_visitor_master WHERE DATE(vin) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
$resultTotalSecurity = $conn->query($sqlTotalSecurity);
$rowTotalSecurity = $resultTotalSecurity->fetch_assoc();
$totalSecurity = $rowTotalSecurity['total'];

$conn->close();
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
  <link
    href="https://fonts.googleapis.com/css2?family=Barlow:wght@500;600;700&family=Open+Sans:wght@400;600&display=swap"
    rel="stylesheet">

  <!-- Icon Font Stylesheet -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
    integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />

  <!-- Libraries Stylesheet -->
  <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

  <!-- Customized Bootstrap Stylesheet -->
  <link href="css/bootstrap.min.css" rel="stylesheet">

  <!-- Template Stylesheet -->
  <link href="css/style.css" rel="stylesheet">
  <link href="css/form.css" rel="stylesheet">
</head>
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
      <a href="security_home.php" class="nav-item nav-link active ">Home</a>
      <a href="profile.php" class="nav-item nav-link ">Profile</a>
      <a href="password.php" class="nav-item nav-link">Password</a>
      <a href="logout.php" class="nav-link text-danger fw-bold"><i class="fas fa-sign-out-alt me-1"></i></a>
    </div>
  </div>
</nav>
<!-- Navbar End -->

<!-- admin home start-->
<!-- card start -->
<div class="dashboard">
  <div class="card visitors" onclick="showMessage('Visitors: ' + document.getElementById('totalVisitors').innerText)">
    <h3>Total Visitors</h3>
    <p id="totalVisitors">Loading...</p>
  </div>
  <div class="card business" onclick="showMessage('Owners: ' + document.getElementById('totalOwners').innerText)">
    <h3>Today</h3>
    <p id="totalOwners">Loading...</p>
  </div>
  <div class="card job" onclick="showMessage('Security: ' + document.getElementById('totalSecurity').innerText)">
    <h3>Week</h3>
    <p id="totalSecurity">Loading...</p>
  </div>
</div>

<!-- card end -->

<!-- Visitor Table Start -->
<div class="table">
  <div class="table-header">
    <p>All Visitors</p>
    <div>
      <button type="button" class="add-btn" data-bs-toggle="modal" data-bs-target="#registervisitor">Add
        Visitor</button>
    </div>
    <!-- Visitor Registration Modal -->
    <div class="modal fade" id="registervisitor" tabindex="-1" aria-labelledby="registerVisitorLabel"
      aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="registerVisitorLabel">Register Visitor</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <!-- The form action points to the same page (e.g., security_home.php) -->
            <form method="POST" action="security_home.php">
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="visitor_name">Full Name</label>
                  <input type="text" id="visitor_name" name="vname" class="form-control" placeholder="Enter Full Name"
                    required>
                </div>
                <div class="col-md-6 mb-3">
                  <label for="visitor_email">Visitor email</label>
                  <input type="email" id="visitor_email" name="vemail" class="form-control" placeholder="Enter email"
                    required>
                </div>
                <div class="col-md-6 mb-3">
                  <label for="visitor_mobile_number">Mobile Number</label>
                  <input type="tel" id="visitor_mobile_number" name="vmobile" class="form-control"
                    placeholder="Enter Mobile Number" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label for="whom_to_meet">Whom To Meet</label>
                  <input type="text" id="whom_to_meet" name="vmeet" class="form-control" placeholder="Person to Meet"
                    required>
                </div>
                <div class="col-md-6 mb-3">
                  <label for="reason_to_meet">Reason To Meet</label>
                  <input type="text" id="reason_to_meet" name="vreason" class="form-control" placeholder="Enter Reason"
                    required>
                </div>
                <div class="col-md-6 mb-3">
                  <label for="building_number">Building Number</label>
                  <input type="text" id="building_number" name="vbuilding" class="form-control"
                    placeholder="Enter Building No." required>
                </div>
                <div class="col-md-6 mb-3">
                  <label for="room_number">Room Number</label>
                  <input type="text" id="room_number" name="vroom" class="form-control" placeholder="Enter Room No."
                    required>
                </div>
                <div class="col-md-6 mb-3">
                  <label for="visitor_address">Visitor Address</label>
                  <input type="text" id="visitor_address" name="vaddress" class="form-control"
                    placeholder="Enter Address" required>
                </div>

                <div class="col-md-12">
                  <input type="hidden" name="vin" value="<?php echo date('Y-m-d H:i:s A'); ?>">
                  <button type="submit" class="register-btn">Register</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Visitor Table Selection -->
  <div class="table-selection">
    <table class="table">
      <thead>
        <tr>
          <th>Sr No.</th>
          <th>Name</th>
          <th>Mobile No.</th>
          <th>Email ID</th>
          <th>Date of Visit</th>
          <th>Date of Exit</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody id="visitor-table">
        <?php
        $serialNumber = $startFrom + 1; // Start serial number based on current page
        if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$serialNumber}</td>
                    <td>" . htmlspecialchars($row['vname']) . "</td>
                    <td>" . htmlspecialchars($row['vmobile']) . "</td>
                    <td>" . htmlspecialchars($row['vemail']) . "</td>
                    <td>" . date('d-m-Y h:i:s A', strtotime($row['vin'])) . "</td>
                    <td>" . (!empty($row['vout']) ? date('d-m-Y h:i:s A', strtotime($row['vout'])) : '-') . "</td>

                    <td>
                       <button class='edit-btn' 
                          data-vmid='{$row['vmid']}' 
                          data-vname='{$row['vname']}' 
                          data-vemail='{$row['vemail']}' 
                          data-vmobile='{$row['vmobile']}' 
                          data-vmeet='{$row['vmeet']}'
                          data-vreason='{$row['vreason']}'
                          data-vbuilding='{$row['vbuilding']}'
                          data-vroom='{$row['vroom']}'
                          data-vaddress='{$row['vaddress']}'
                          data-vin='{$row['vin']}'
                          data-vout='{$row['vout']}'
                          data-bs-toggle='modal' data-bs-target='#editVisitorModal'>
                          <i class='fa-regular fa-pen-to-square'></i>
                       </button>
                    </td>
                  </tr>";
            $serialNumber++;
          }
        } else {
          echo "<tr><td colspan='6' class='text-center'>No visitors found</td></tr>";
        }
        ?>
      </tbody>
    </table>
  </div>

  <!--  Edit Visitor Modal-->
  <div class="modal fade" id="editVisitorModal" tabindex="-1" aria-labelledby="editVisitorLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content" style="background: rgba(255, 255, 255, 0.9);">
        <div class="modal-header">
          <h5 class="modal-title" id="editVisitorLabel">Edit Visitor</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <!-- The form action points to the same page (e.g., security_home.php) -->
          <form method="POST" action="update_visitor.php">
            <div class="row">
              <input type="hidden" id="edit_vmid" name="vmid">
              <div class="col-md-6 mb-3">
                <label for="edit_vname" class="form-label">Full Name</label>
                <input type="text" id="edit_vname" name="vname" class="form-control" required>
              </div>
              <div class="col-md-6 mb-3">
                <label for="edit_email" class="form-label">Email</label>
                <input type="email" id="edit_vemail" name="vemail" class="form-control" required>
              </div>
              <div class="col-md-6 mb-3">
                <label for="edit_mobile_number" class="form-label">Mobile Number</label>
                <input type="tel" id="edit_vmobile" name="vmobile" class="form-control" required>
              </div>
              <div class="col-md-6 mb-3">
                <label for="edit_whom_to_meet">Whom to Meet</label>
                <input type="text" id="edit_vmeet" name="vmeet" class="form-control" required>
              </div>
              <div class="col-md-6 mb-3">
                <label for="edit_reason_to_meet">Reason to Meet</label>
                <input type="text" id="edit_vreason" name="vreason" class="form-control" required>
              </div>
              <div class="col-md-6 mb-3">
                <label for="edit_building_number">Building Number</label>
                <input type="text" id="edit_vbuilding" name="vbuilding" class="form-control" required>
              </div>
              <div class="col-md-6 mb-3">
                <label for="edit_room_number">Room Number</label>
                <input type="text" id="edit_vroom" name="vroom" class="form-control" required>
              </div>
              <div class="col-md-6 mb-3">
                <label for="edit_visitor_address">Visitor Address</label>
                <input type="text" id="edit_vaddress" name="vaddress" class="form-control" required>
              </div>
              <div class="col-md-6 mb-3">
                <label for="edit_registered_at">In Time</label>
                <input type="text" id="edit_vin" name="vin" class="form-control" required>
              </div>


              <div class="col-md-6 mb-3">
                <label for="edit_out_remark">Out Remark</label>
                <input type="text" id="edit_out_remark" name="out_remark" class="form-control"
                  placeholder="Type 'out' to check out">
              </div>



              <div class="col-md-12">
                <button type="submit" class="btn btn-primary">Save Changes</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>



</div>


<!-- Visitor Pagination Links -->
<div class="pagination">
  <div><a href="?page=1"><i class="fa-solid fa-angles-left"></i></a></div>
  <div><a href="?page=<?php echo ($page > 1) ? $page - 1 : 1; ?>"><i class="fa-solid fa-chevron-left"></i></a></div>
  <?php
  for ($i = 1; $i <= $totalPages; $i++) {
    $activeClass = ($i == $page) ? 'active' : '';
    echo "<div><a href='?page=$i' class='$activeClass'>$i</a></div>";
  }
  ?>
  <div><a href="?page=<?php echo ($page < $totalPages) ? $page + 1 : $totalPages; ?>"><i
        class="fa-solid fa-chevron-right"></i></a></div>
  <div><a href="?page=<?php echo $totalPages; ?>"><i class="fa-solid fa-angles-right"></i></a></div>
</div>
</div>
<!-- Visitor Table End -->

<!-- admin home end-->

<!-- Footer Start -->
<?php @include("includes/footer.php"); ?>
<!-- Footer End -->


<?php @include("includes/javalib.php"); ?>
<script>
  document.getElementById('totalVisitors').innerText = '<?php echo $totalVisitors; ?>';
  document.getElementById('totalOwners').innerText = '<?php echo $totalOwners; ?>';
  document.getElementById('totalSecurity').innerText = '<?php echo $totalSecurity; ?>';

  function showMessage(message) {
    alert(message);
  }

  document.addEventListener("DOMContentLoaded", function () {
    const editButtons = document.querySelectorAll(".edit-btn");

    editButtons.forEach(button => {
      button.addEventListener("click", function () {
        document.getElementById("edit_vmid").value = this.getAttribute("data-vmid");
        document.getElementById("edit_vname").value = this.getAttribute("data-vname");
        document.getElementById("edit_vemail").value = this.getAttribute("data-vemail");
        document.getElementById("edit_vmobile").value = this.getAttribute("data-vmobile");
        document.getElementById("edit_vmeet").value = this.getAttribute("data-vmeet");
        document.getElementById("edit_vreason").value = this.getAttribute("data-vreason");
        document.getElementById("edit_vbuilding").value = this.getAttribute("data-vbuilding");
        document.getElementById("edit_vroom").value = this.getAttribute("data-vroom");
        document.getElementById("edit_vaddress").value = this.getAttribute("data-vaddress");
        document.getElementById("edit_vin").value = this.getAttribute("data-vin");
        document.getElementById("edit_vout").value = this.getAttribute("data-vout");

      });
    });
  });





  document.getElementById("editVisitorForm").addEventListener("submit", function (event) {
    var outRemark = document.getElementById("edit_vout").value.trim();
    if (outRemark !== "") {
      var now = new Date();
      var formattedDateTime = now.getFullYear() + "-" +
        String(now.getMonth() + 1).padStart(2, '0') + "-" +
        String(now.getDate()).padStart(2, '0') + "T" +
        String(now.getHours()).padStart(2, '0') + ":" +
        String(now.getMinutes()).padStart(2, '0') + ":" + // REMOVED SECONDS
        String(now.getSeconds()).padStart(2, '0');


      document.getElementById("edit_vout").value = formattedDateTime;
    }
  });

</script>

</body>

</html>