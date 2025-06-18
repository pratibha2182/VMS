<?php

include 'includes/db_connection.php'; // Database connection

// Handle Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize the form data
    $rname = $conn->real_escape_string($_POST['rname']);
    $remail = $conn->real_escape_string($_POST['remail']);
    $rmobile = $conn->real_escape_string($_POST['rmobile']);
    $rgender = $conn->real_escape_string($_POST['rgender']);
    $roccupation = $conn->real_escape_string($_POST['roccupation']);
    $raddress = $conn->real_escape_string($_POST['raddress']);
    $rbuilding = $conn->real_escape_string($_POST['rbuilding']);
    $rroom = $conn->real_escape_string($_POST['rroom']);
    $rtotal = $conn->real_escape_string($_POST['rtotal']);

    // Insert Data into register_master table
    $sql = "INSERT INTO vms_register_master 
        (rname, remail, rmobile, rgender, roccupation, raddress, rbuilding, rroom, rtotal) 
        VALUES ('$rname', '$remail', '$rmobile', '$rgender', '$roccupation', '$raddress',  '$rbuilding', '$rroom',  '$rtotal')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Registration Successful!'); window.location.href='index.php';</script>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
// Close the database connection
$conn->close();
?>


<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
    <title>Owner Regisration Form </title>
    <link rel="stylesheet" href="css/form.css">
</head>
<body class="regbody">
    <div class="regcontainer">
        <header>Registration</header>
        <form action="register.php" method="POST" onsubmit="return validateForm();">
            <div class="form first">
                <div class="details personal">
                    <span class="title">Personal Details</span>
                    <div class="fields">
                        <div class="input-field">
                            <label for="full_name">Full Name</label>
                            <input name="rname" id="full_name" type="text" placeholder="Enter your full name" required pattern="^[A-Za-z\s]{3,50}$" title="Only letters and spaces, min 3 characters.">
                        </div>
                        <div class="input-field">
                            <label for="email">Email</label>
                            <input name="remail" id="email" type="email" placeholder="Enter your email" required>
                        </div>
                        <div class="input-field">
                            <label for="mobile_number">Mobile Number</label>
                            <input name="rmobile" id="mobile_number" type="tel" placeholder="Enter mobile number" pattern="[0-9]{10}" title="Enter a valid 10-digit mobile number" required>
                        </div>
                        <div class="input-field">
                            <label for="gender">Gender</label>
                            <select name="rgender" id="gender" required>
                                <option value="" disabled selected>Select gender</option>
                                <option>Male</option>
                                <option>Female</option>
                                <option>Others</option>
                            </select>
                        </div>
                        <div class="input-field">
                            <label for="occupation">Occupation</label>
                            <input name="roccupation" id="occupation" type="text" placeholder="Enter your occupation" required>
                        </div>
                        <div class="input-field">
                            <label for="address">Address</label>
                            <input name="raddress" id="address" type="text" placeholder="Enter your address" required>
                        </div>
                        <div class="input-field">
                            <label for="building_number">Building Number</label>
                            <input name="rbuilding" id="building_number" type="text" placeholder="Enter Building number" required>
                        </div>
                        <div class="input-field">
                            <label for="room_number">Room Number</label>
                            <input name="rroom" id="room_number" type="text" placeholder="Enter Room number" required>
                        </div>
                        <div class="input-field">
                            <label for="total_person">Total Person</label>
                            <input name="rtotal" id="total_person" type="number" placeholder="Number of persons in house" min="1" required>
                        </div>
                    </div>
                    <button type="submit" class="submit">
                        <span class="btnText">Submit</span>
                    </button>
                </div>
        </form>
    </div>
</body>

</html>