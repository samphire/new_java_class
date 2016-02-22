<?php
/**
 * Created by IntelliJ IDEA.
 * User: matthew
 * Date: 2/16/2016
 * Time: 9:04 PM
 */
error_reporting(1);
if (!isset($_SESSION)) {
    session_start();
}
global $conn;
$conn = mysqli_connect("localhost", "root", "canal", "panscopic") or die('Error connecting to mysql');
mysqli_query($conn, "SET NAMES utf8");
mysqli_query($conn, "SET CHARACTER SET utf8");
$sql = "UPDATE tbl_students SET fld_pass='" . $_GET['password'] . "' WHERE fld_student_id = " . $_GET['user'];
$query = mysqli_query($conn, $sql);
$retval = mysqli_affected_rows($conn);
if ($retval > 0) {
    echo('success');
} elseif ($retval < 0){
    echo('fail');
}


