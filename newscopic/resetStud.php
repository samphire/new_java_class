<?php
error_reporting(1);
if (!isset($_SESSION)) {
session_start();
}
global $conn;
$conn = mysqli_connect("localhost", "root", "canal", "panscopic") or die('Error connecting to mysql');
mysqli_query($conn, "SET NAMES utf8");
mysqli_query($conn, "SET CHARACTER SET utf8");
$sql = "DELETE FROM tbl_stud_testscore WHERE fld_student_id = '". $_GET['stud'] . "' AND fld_test_id = " . $_GET['test'];
$query = mysqli_query($conn, $sql);

$sql = "SELECT * FROM tbl_tests WHERE fld_test_id='" . $_GET['test'] . "'";
$query = mysqli_query($conn, $sql);
list(,,,,,,,,,,,,,,$qtable) = mysqli_fetch_row($query);
$sql = "DELETE FROM tbl_response" . $qtable . " WHERE fld_student_id = '". $_GET['stud'] . "'";
$query = mysqli_query($conn, $sql);
//$retval = mysqli_affected_rows($conn);
//if ($retval > 0) {
//echo('success');
//} elseif ($retval < 0){
//echo('fail');
//}


