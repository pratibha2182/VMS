<?php
include 'includes/db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $vmid = $conn->real_escape_string($_POST['vmid']);
    $vname = $conn->real_escape_string($_POST['vname']);
    $vemail = $conn->real_escape_string($_POST['vemail']);
    $vmobile = $conn->real_escape_string($_POST['vmobile']);
    $vmeet = $conn->real_escape_string($_POST['vmeet']);
    $vreason = $conn->real_escape_string($_POST['vreason']);
    $vbuilding = $conn->real_escape_string($_POST['vbuilding']);
    $vroom = $conn->real_escape_string($_POST['vroom']);
    $vaddress = $conn->real_escape_string($_POST['vaddress']);
    $out_remark = $conn->real_escape_string($_POST['out_remark']); // Out remark field

    // Initialize the update query
    $sql = "UPDATE vms_visitor_master 
            SET vname='$vname', vemail='$vemail', vmobile='$vmobile', vmeet='$vmeet',
                vreason='$vreason', vbuilding='$vbuilding', vroom='$vroom', vaddress='$vaddress'";

    // Check if "out" is entered in the out_remark field
    if (strtolower($out_remark) == "out") {
        $vout = date('Y-m-d H:i:s A'); // Store current timestamp
        $sql .= ", vout='$vout', vstatus='out'";
    }

    $sql .= " WHERE vmid='$vmid'";

    if ($conn->query($sql) === TRUE) {
        echo "<script>
                alert('Visitor Updated Successfully!');
                window.location.href = 'security_home.php';
              </script>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>
