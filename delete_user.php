<?php
include 'includes/db_connection.php'; // Make sure you include your database connection

if (isset($_POST['id']) && isset($_POST['role'])) {
    $id = intval($_POST['id']);
    $role = $_POST['role'];

    if ($role === 'security') {
        $sql = "DELETE FROM vms_security_master WHERE smid = ?";
    } elseif ($role === 'owner') {
        // First, try deleting from vms_ownera_master
        $sqlA = "DELETE FROM vms_ownera_master WHERE omid = ?";
        $stmtA = $conn->prepare($sqlA);
        $stmtA->bind_param("i", $id);
        $stmtA->execute();
        if ($stmtA->affected_rows > 0) {
            echo "success";
            exit;
        }

        // If not found in vms_ownera_master, try vms_ownerb_master
        $sqlB = "DELETE FROM vms_ownerb_master WHERE omid = ?";
        $stmtB = $conn->prepare($sqlB);
        $stmtB->bind_param("i", $id);
        $stmtB->execute();
        if ($stmtB->affected_rows > 0) {
            echo "success";
        } else {
            echo "error";
        }
        exit;
    } else {
        echo "error";
        exit;
    }

    // Execute query
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    if ($stmt->execute() && $stmt->affected_rows > 0) {
        echo "success";
    } else {
        echo "error";
    }
}


 // user accept deletion

if (isset($_POST['rmid'])) {
    $id = $_POST['rmid'];

    $sql = "DELETE FROM vms_register_master WHERE rmid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }

    $stmt->close();
    $conn->close();
}


?>