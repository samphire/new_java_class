<?php
/**
 * Created by IntelliJ IDEA.
 * User: matthew
 * Date: 2/16/2016
 * Time: 9:04 PM
 */
include("sessionheader.inc");

$sql = "UPDATE tbl_students SET fld_pass='" . $_GET['password'] . "' WHERE fld_student_id = " . $_GET['user'];
echo ($sql);
echo (mysqli_query($conn, $sql));

echo('success');