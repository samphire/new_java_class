<?php
/**
 * Created by IntelliJ IDEA.
 * User: matthew
 * Date: 2/16/2016
 * Time: 10:20 PM
 */
error_reporting(1);
if (!isset($_SESSION)) {
    session_start();
}
global $conn;
$conn = mysqli_connect("localhost", "root", "canal", "panscopic") or die('Error connecting to mysql');
mysqli_query($conn, "SET NAMES utf8");
mysqli_query($conn, "SET CHARACTER SET utf8");
$sql = "SELECT * FROM tbl_students WHERE fld_student_id = '" . $_GET['user'] . "' AND fld_pass = '" . $_GET['password'] . "';";
$query = mysqli_query($conn, $sql);
if (mysqli_num_rows($query) > 0) {
    echo('success');
} else{
    echo('fail');
}


