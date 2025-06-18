<?php
include 'includes/db_connection.php'; // Database connection

session_start();


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$owner_id = $_SESSION['user_id'];
$vbuilding = $_SESSION['building'];




// echo "<pre>Owner ID: $owner_id</pre>"; // Debugging
// echo "<pre>Building: " . ($vbuilding ?? 'Not Set') . "</pre>";

$vbuilding = $_SESSION['building']; // Get from session

if (!empty($_POST['vbuilding'])) { // Only update if the form sends a value
    $vbuilding = $_POST['vbuilding'];
}$query = "SELECT * FROM vms_requests WHERE owner_id = $owner_id AND vbuilding = '$vbuilding'";

// $query = "SELECT * FROM vms_requests WHERE owner_id = $owner_id ORDER BY request_date DESC";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query Error: " . mysqli_error($conn)); // Show MySQL error if query fails
}

if (mysqli_num_rows($result) == 0) {
    echo "<pre>No visitor requests found.</pre>"; // Debugging
}
?>








<!DOCTYPE html>
<html lang="en">
<!-- head start  -->

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
</head>
<!-- head end -->
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
            <a href="owner_home.php" class="nav-item nav-link active">Home</a>
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
    <div class="card visitors" onclick="showMessage('Visitors: 14')">
        <h3>Visitors</h3>
        <p></p>
    </div>
    <div class="card business" onclick="showMessage('Business Visits: 4')">
        <h3>Business Visits</h3>
        <p></p>
    </div>
    <div class="card personal" onclick="showMessage('Personal Visits: 8')">
        <h3>Personal Visits</h3>
        <p></p>
    </div>
    <div class="card job" onclick="showMessage('Job Visits: 2')">
        <h3>Job Visits</h3>
        <p></p>
    </div>
</div>
<!-- card end -->


<!-- Visitor table start -->
<div class="table">
    <div class="table-header">
        <p>All Visitors</p>
        <div>
            <input placeholder="Search">
        </div>
    </div>
    <div class="table-selection">
        <table>
            <thead>
                <tr>
                    <th>Number</th>
                    <th>Name</th>
                    <th>Mobile Number</th>
                    <th>Email ID</th>
                    <th>Reason</th>
                    <th>Date of Visit</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (mysqli_num_rows($result) > 0) {
                    $count = 1;
                    while ($row = mysqli_fetch_assoc($result)) { ?>
                        <tr>
                            <td><?php echo $count++; ?></td>
                            <td><?php echo htmlspecialchars($row['visitor_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['visitor_mobile']); ?></td>
                            <td><?php echo htmlspecialchars($row['visitor_email']); ?></td>
                            <td><?php echo htmlspecialchars($row['reason']); ?></td>
                            <td><?php echo htmlspecialchars($row['request_date']); ?></td>
                            <td>
                                <form action="approve_visitor.php" method="POST">
                                    <input type="hidden" name="request_id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" name="approve" class="btn btn-success">Approve</button>
                                    <button type="submit" name="deny" class="btn btn-danger">Deny</button>
                                </form>
                            </td>
                        </tr>
                    <?php }
                } else { ?>
                    <tr>
                        <td colspan="8" style="text-align: center;">No visitor requests found</td>
                    </tr>
                <?php } ?>
            </tbody>


        </table>
    </div>
    <!-- <div class="pagination">
            <div><i class="fa-solid fa-angles-left"></i></div>
            <div><i class="fa-solid fa-chevron-left"></i></div>
            <div>1</div>
            <div>2</div>
            <div><i class="fa-solid fa-chevron-right"></i></div>
            <div><i class="fa-solid fa-angles-right"></i></div>
        </div> -->
</div>
<!-- Visitor table end -->
<!-- admin home end-->

<!-- Footer Start -->
<?php @include("includes/footer.php"); ?>
<!-- Footer End -->


<?php @include("includes/javalib.php"); ?>
</body>

</html>




<!-- <button class='edit-btn'><i class="fa-solid fa-check"></i></button>
<button class='delete-btn'><i class="fa-solid fa-xmark"></i></button> -->