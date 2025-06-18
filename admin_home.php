<?php
session_start();

include 'includes/db_connection.php'; // Database connection

// Set the number of records per page
$records_per_page = 10;  // Adjust this number as needed

// Get the current page number from the URL, if available, otherwise default to 1
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;

// Calculate the offset for the query
$offset = ($page - 1) * $records_per_page;

// SQL query to fetch the records for the current page
$sql = "SELECT * FROM vms_register_master ORDER BY rmid DESC LIMIT $offset, $records_per_page";
$result = $conn->query($sql);

// SQL query to get the total number of records in the table
$total_records_sql = "SELECT COUNT(*) FROM vms_register_master";
$total_records_result = $conn->query($total_records_sql);
$total_records_row = $total_records_result->fetch_row();
$total_records = $total_records_row[0];

// Calculate the total number of pages
$totalPages = ceil($total_records / $records_per_page);

// SQL query to fetch data from vms_vms_register_master table
$sql = "SELECT * FROM vms_register_master WHERE rstatus = 'pending' ORDER BY rmid DESC LIMIT $offset, $records_per_page";

$result = $conn->query($sql);

// card number showing
// Query to get total number of visitors
$totalVisitorsSql = "SELECT COUNT(*) as total FROM vms_visitor_master";
$totalVisitorsResult = $conn->query($totalVisitorsSql);
$totalVisitorsRow = $totalVisitorsResult->fetch_assoc();
$totalVisitors = $totalVisitorsRow['total'];

// Query to get total number of owners
$totalOwnersSql = "SELECT COUNT(*) as total FROM vms_ownera_master";
$totalOwnersResult = $conn->query($totalOwnersSql);
$totalOwnersRow = $totalOwnersResult->fetch_assoc();
$totalOwners = $totalOwnersRow['total'];

// Query to get total number of security personnel
$totalSecuritySql = "SELECT COUNT(*) as total FROM vms_security_master";
$totalSecurityResult = $conn->query($totalSecuritySql);
$totalSecurityRow = $totalSecurityResult->fetch_assoc();
$totalSecurity = $totalSecurityRow['total'];

//accept_table
$sqlAccepted = "SELECT * FROM vms_register_master WHERE rstatus = 'accepted' ORDER BY rmid DESC";
$resultAccepted = $conn->query($sqlAccepted);

// Close the database connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<!-- Head Start -->
<?php @include("includes/head.php"); ?>
<!-- Head End -->

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
            <a href="admin_home.php" class="nav-item nav-link active">Home</a>
            <a href="profile.php" class="nav-item nav-link ">Profile</a>
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle " id="navbarDropdown" role="button"
                    data-bs-toggle="dropdown" aria-expanded="false">Management</a>
                <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <li><a class="dropdown-item " href="users.php">Register User</a></li>
                    <li><a class="dropdown-item " href="change_password.php" class="nav-item nav-link">Password</a></li>
                </ul>
            </div>
            <a href="logout.php" class="nav-link text-danger fw-bold"><i class="fas fa-sign-out-alt me-1"></i></a>
        </div>
    </div>
</nav>
<!-- Navbar End -->

<!-- admin home start-->


<!-- requested owner start -->
<div class="table">
    <div class="table-header">
        <p>User Requests</p>
    </div>
    <div class="table-selection">
        <table>
            <thead>
                <tr>
                    <th>Sr No.</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>Address</th>
                    <th>Gender</th>
                    <th>Building </th>
                    <th>Room </th>
                    <th>Person</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="visitor-table">
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr rmid='row_" . $row['rmid'] . "'>
                               <td>" . $row["rmid"] . "</td>
                               <td>" . $row["rname"] . "</td>
                               <td>" . $row["remail"] . "</td>
                               <td>" . $row["rmobile"] . "</td>
                               <td>" . $row["raddress"] . "</td>
                               <td>" . $row["rgender"] . "</td>
                               <td>" . $row["rbuilding"] . "</td>
                               <td>" . $row["rroom"] . "</td>
                               <td>" . $row["rtotal"] . "</td>
                               <td>
                                 <button class='btn btn-success accept-btn' data-id='" . $row["rmid"] . "'><i class='fa-solid fa-check'></i></button>
                                 <button class='btn btn-danger reject-btn' data-id='" . $row["rmid"] . "'><i class='fa-solid fa-xmark'></i></button>
                               </td>
                            </tr>";
                    }
                } else {
                    echo "<tr><td colspan='10'>No records found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    <div class="pagination">
        <!-- First Page Arrow -->
        <div><a href="?page=1"><i class="fa-solid fa-angles-left"></i></a></div>

        <!-- Previous Page Arrow -->
        <div><a href="?page=<?php echo ($page > 1) ? $page - 1 : 1; ?>"><i class="fa-solid fa-chevron-left"></i></a>
        </div>

        <!-- Page Number Links -->
        <?php
        for ($i = 1; $i <= $totalPages; $i++) {
            // Check if the current page number is the one being displayed
            $activeClass = ($i == $page) ? 'active' : ''; // Add 'active' class to current page
            echo "<div><a href='?page=$i' class='$activeClass'>$i</a></div>";
        }
        ?>

        <!-- Next Page Arrow -->
        <div><a href="?page=<?php echo ($page < $totalPages) ? $page + 1 : $totalPages; ?>"><i
                    class="fa-solid fa-chevron-right"></i></a></div>

        <!-- Last Page Arrow -->
        <div><a href="?page=<?php echo $totalPages; ?>"><i class="fa-solid fa-angles-right"></i></a></div>
    </div>
</div>
<!-- requested owner end -->

<!-- Accepted owner start -->
<div class="table">
    <div class="table-header">
        <p>Accepted Users</p>
    </div>
    <div class="table-selection">
        <table>
            <thead>
                <tr>
                    <th>Sr No.</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>Address</th>
                    <th>Gender</th>
                    <th>Building </th>
                    <th>Room </th>
                    <th>Person</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php
if ($resultAccepted->num_rows > 0) {
    while ($row = $resultAccepted->fetch_assoc()) {
        echo "<tr id='row_".$row["rmid"]."'>
               <td>" . $row["rmid"] . "</td>
               <td>" . $row["rname"] . "</td>
               <td>" . $row["remail"] . "</td>
               <td>" . $row["rmobile"] . "</td>
               <td>" . $row["raddress"] . "</td>
               <td>" . $row["rgender"] . "</td>
               <td>" . $row["rbuilding"] . "</td>
               <td>" . $row["rroom"] . "</td>
               <td>" . $row["rtotal"] . "</td>
               <td>
               <button class='delete' data-id='".$row["rmid"]."'><i class='fa-solid fa-trash'></i></button>
               </td>
           </tr>";
    }
} else {
    echo "<tr><td colspan='10'>No accepted users</td></tr>";
}
?>
            </tbody>
        </table>
    </div>
    <div class="pagination">
        <!-- First Page Arrow -->
        <div><a href="?page=1"><i class="fa-solid fa-angles-left"></i></a></div>

        <!-- Previous Page Arrow -->
        <div><a href="?page=<?php echo ($page > 1) ? $page - 1 : 1; ?>"><i class="fa-solid fa-chevron-left"></i></a>
        </div>

        <!-- Page Number Links -->
        <?php
        for ($i = 1; $i <= $totalPages; $i++) {
            // Check if the current page number is the one being displayed
            $activeClass = ($i == $page) ? 'active' : ''; // Add 'active' class to current page
            echo "<div><a href='?page=$i' class='$activeClass'>$i</a></div>";
        }
        ?>

        <!-- Next Page Arrow -->
        <div><a href="?page=<?php echo ($page < $totalPages) ? $page + 1 : $totalPages; ?>"><i
                    class="fa-solid fa-chevron-right"></i></a></div>

        <!-- Last Page Arrow -->
        <div><a href="?page=<?php echo $totalPages; ?>"><i class="fa-solid fa-angles-right"></i></a></div>
    </div>
</div>
<!-- Accepted owner end -->
<!-- admin home end-->

<!-- Footer Start -->
<?php @include("includes/footer.php"); ?>
<!-- Footer End -->


<?php @include("includes/javalib.php"); ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        $(".accept-btn, .reject-btn").click(function () {
            var button = $(this);
            var id = button.data("id");
            var action = button.hasClass("accept-btn") ? "accept" : "reject";

            $.ajax({
                url: "handle_request.php",
                type: "POST",
                data: { id: id, action: action },
                success: function (response) {
                    if (response.trim() === "success") {
                        $("#row_" + id).remove();
                        alert("Request " + action + "ed successfully.");
                        if (action === "accept") {
                            location.reload(); // Reload to update accepted users table
                        }
                    } else {
                        alert("Error processing request.");
                    }
                }
            });
        });
    });
</script>
//accept user delete function
<script>
    $(document).ready(function() {
  $(".delete").click(function() {
      var id = $(this).data("id");
      var row = $("#row_" + id);

      if (confirm("Are you sure you want to delete this record?")) {
          $.ajax({
              url: "delete_user.php",
              type: "POST",
              data: { rmid: id },
              success: function(response) {
                  if (response == "success") {
                      row.fadeOut(500, function() {
                          $(this).remove();
                      });
                  } else {
                      alert("Failed to delete record!");
                  }
              }
          });
      }
  });
});
</script>

</body>

</html>