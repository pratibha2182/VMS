<?php
include 'includes/db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = intval($_POST['id']);

    // Update the status of the user to 'accepted'
    $sql = "UPDATE vms_register_master SET rstatus = 'accepted' WHERE rmid = $id";

    if ($conn->query($sql) === TRUE) {
        echo "success";
    } else {
        echo "Error updating record: " . $conn->error;
    }

    $conn->close();
} else {
    echo "Invalid request";
}
?>