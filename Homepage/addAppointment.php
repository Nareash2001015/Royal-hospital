<?php
require_once("../conf/config.php");
session_start();

if (isset($_POST['submit'])) {
    $date = $_POST['date'];
    $department = $_POST['department'];
    $doctor = $_POST['doctor'];
    $time = $_POST['time'];
    $nic = $_POST['nic'];
    $msg = $_POST['msg'];

    $docNIC_query = "SELECT nic FROM doctor WHERE doctorID = '$doctor'";
    $result_docNIC = mysqli_query($con, $docNIC_query);
    $docNIC = mysqli_fetch_assoc($result_docNIC)['nic'];

    $patientCheck = "select * from user where nic = '$nic' and user_role = 'Patient';";
    if(mysqli_num_rows(mysqli_query($con, $patientCheck)) == 0){
        header("location: " . BASEURL ."/Homepage/homepageAppointment.php?warning=You are not registered as a patient.");
        exit();
    }

    $pid = mysqli_fetch_assoc(mysqli_query($con, "select patientID from patient where nic = '$nic'"))['patientID'];

    $appointmentCountPerDayQuery = "select count(*) from appointment where patientID = '$pid' and date='$date';";
    $appointmentCountPerDay = mysqli_fetch_assoc(mysqli_query($con, $appointmentCountPerDayQuery))['count(*)'];


    if($appointmentCountPerDay == 1){
        header("location: " . BASEURL ."/Homepage/homepageAppointment.php?warning=Already you have an appointment this day. Try another date.");
        exit();
    }

    $query = "INSERT INTO `appointment`(`date`, `time`, `doctorID`, `patientID`, `message`, `status`) 
              VALUES ('$date','$time','$doctor','$pid','$msg','Confirmed')";
    $appointmentResult = mysqli_query($con, $query);


    $query = "INSERT INTO `purchases`(`patientID`, `date`, `quantity`, `paid_status`, `paid_status1`, `item`, `item_flag`) 
    VALUES ('$pid', '$date',1, 'not paid', 'Not paid', 3, 's')";
    $result = mysqli_query($con, $query);

    $query = "INSERT INTO `notification`( `nic`, `Message`, `Timestamp`)
              VALUES ('$docNIC','An appointment booked by patient No $pid',CURRENT_TIMESTAMP)";
    $result = mysqli_query($con, $query);

    if($appointmentResult)
        header("location: " . BASEURL ."/Homepage/homepageAppointment.php?result=The appointment is booked successfully.");
}else{
    header("location: " . BASEURL ."/Receptionist/receptionistDash.php");
}