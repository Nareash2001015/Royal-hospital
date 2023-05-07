<?php
session_start();
//die( $_SESSION['mailaddress']);
require_once("../conf/config.php");

if (isset($_POST["submit"])) {

    $date = $_POST['date'];
    $department = $_POST['department'];
    $doctor = $_POST['doctor'];
    $time = $_POST['time'];
    $pid = $_POST['patient'];
    $msg = $_POST['msg'];
    $nic = $_SESSION['nic'];

    $docNIC_query = "SELECT nic FROM doctor WHERE doctorID = '$doctor'";
    $result_docNIC = mysqli_query($con, $docNIC_query);
    $docNIC = mysqli_fetch_assoc($result_docNIC)['nic'];

    $recID_query = "SELECT receptionistID FROM receptionist WHERE nic = '$nic'";
    $result_recID = mysqli_query($con, $recID_query);
    $recID = mysqli_fetch_assoc($result_recID)['receptionistID'];

    $query = "INSERT INTO `appointment`(`date`, `time`, `doctorID`, `patientID`, `message`, `status`, `receptionistID`) 
    VALUES ('$date','$time','$doctor','$pid','$msg','Confirmed', '$recID')";
    $result = mysqli_query($con, $query);

    $appIdQuery = "SELECT LAST_INSERT_ID()";
    $appID = mysqli_fetch_assoc(mysqli_query($con, $appIdQuery))['LAST_INSERT_ID()'];

    $query = "INSERT INTO `purchases`(`patientID`, `date`, `quantity`, `paid_status`, `paid_status1`, `item`, `item_flag`, `appointmentID`) 
    VALUES ('$pid' ,'$date',1,'not paid', 'Not paid', 3, 's', NULL), ('$pid', '$date',1, 'not paid', 'Not paid', 4, 's', '$appID')";
    $result = mysqli_query($con, $query);

    $query = "INSERT INTO `notification`( `nic`, `Message`, `Timestamp`) 
              VALUES ('$docNIC','An appointment booked by patient No $pid',CURRENT_TIMESTAMP)";
    $result = mysqli_query($con, $query);

    header("location: " . BASEURL ."/Receptionist/makeAppointment.php");
}else{
    header("location: " . BASEURL ."/Receptionist/makeAppointment.php");
}
