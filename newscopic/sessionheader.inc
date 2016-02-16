<?php
error_reporting(1);
if (!isset($_SESSION)) {
    session_start();
}
date_default_timezone_set('Asia/Seoul');
global $conn;
$conn = mysqli_connect("localhost", "root", "canal", "panscopic") or die('Error connecting to mysql');
//mysql_select_db("panscopic", $link1);
mysqli_query($conn, "SET NAMES utf8");
mysqli_query($conn, "SET CHARACTER SET utf8");
session_cache_limiter('private_no_expire, must-revalidate');
$_SESSION['global_url'] = "http://52.192.60.185/scopic";
print "<!DOCTYPE html>";
print "\n<head>
\n<title>NSU Java Quiz Site</title>
\n<meta http-equiv=\"Content-Type\" content=\"text/html;charset=utf-8\" />
\n<meta name=\"viewport\" content=\"width=device-width, initial-scale=1, maximum-scale=1\">
\n<link rel=\"SHORTCUT ICON\" href='" .$_SESSION['global_url'] . "/images/scopic.ico' />";