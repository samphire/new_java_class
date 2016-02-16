<?php
/**
 * Created by IntelliJ IDEA.
 * User: matthew
 * Date: 2/12/2016
 * Time: 10:54 PM
 */

error_reporting(1);

date_default_timezone_set('Asia/Seoul');
global $link1;
$link1 = mysql_connect("localhost", "root", "canal") or die('Error connecting to mysql');
mysql_select_db("panscopic", $link1);
mysql_query("SET NAMES utf8", $link1);
mysql_query("SET CHARACTER SET utf8", $link1);




$sql = "SELECT Count(tbl_students.fld_student_id) AS numStuds
	  FROM tbl_students
	  WHERE tbl_students.fld_class1=" . $_GET['classid'];

$query = mysql_query($sql, $link1) or die("some error");

list($numStuds) = mysql_fetch_row($query);

//$sql = "SELECT tbl_stud_testscore.fld_test_id, tbl_tests.fld_desc, Count(tbl_students.fld_student_id) AS fld_student_idOfCount
//FROM (tbl_students INNER JOIN tbl_stud_testscore ON tbl_students.fld_student_id = tbl_stud_testscore.fld_student_id)
//RIGHT JOIN tbl_tests ON tbl_stud_testscore.fld_test_id = tbl_tests.fld_test_id
//WHERE tbl_students.fld_class1=" . $_GET['classid'] . " AND tbl_stud_testscore.fld_score=100
//GROUP BY tbl_stud_testscore.fld_test_id
//ORDER BY tbl_stud_testscore.fld_test_id";


$sql = "SELECT tbl_tests.fld_test_id, tbl_tests.fld_desc, bob.numstuds
FROM tbl_tests LEFT JOIN
(SELECT tbl_stud_testscore.fld_test_id AS testid, count(tbl_students.fld_student_id) AS numstuds FROM
tbl_students INNER JOIN tbl_stud_testscore
ON tbl_students.fld_student_id = tbl_stud_testscore.fld_student_id
WHERE tbl_students.fld_class1=" . $_GET['classid'] . " AND tbl_stud_testscore.fld_score=100
GROUP BY tbl_stud_testscore.fld_test_id) AS bob
ON tbl_tests.fld_test_id = bob.testid
ORDER BY tbl_tests.fld_test_id";


$query = mysql_query($sql, $link1) or die("some error");

$jsonStrings = array();
while (list($testid, $testDesc, $count) = mysql_fetch_row($query)) {
    $arr = array('test' => $testDesc, 'count' => $count, 'numstuds' => $numStuds);
    array_push($jsonStrings, $arr);
}

echo json_encode($jsonStrings);


