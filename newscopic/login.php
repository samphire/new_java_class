<?php
error_reporting(1);
if (!isset($_SESSION)) {
    session_start();
}
global $conn;
$conn = mysqli_connect("localhost", "root", "canal", "panscopic") or die('Error connecting to mysql');
mysqli_query($conn, "SET NAMES utf8");
mysqli_query($conn, "SET CHARACTER SET utf8");

$course = $_GET['courseid'];
$coursedesc = $_GET['coursedesc'];
$student = $_GET['studentid'];

$sql = "SELECT fld_name FROM tbl_students WHERE fld_student_id=" . $student;
$query = mysqli_query($conn, $sql) OR die("something wrong happened");
list($name) = mysqli_fetch_row($query);

$_SESSION['studid'] = $student;
$_SESSION['studname'] = $name;
$_SESSION['courseid'] = $course;
$_SESSION['coursedesc'] = $coursedesc;

//echo $_SESSION['studid'] . $_SESSION['studname'] = $name . $_SESSION['courseid'] = $course . $_SESSION['coursedesc'] = $coursedesc;