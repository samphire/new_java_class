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
$link1 = mysqli_connect("localhost", "root", "canal", "panscopic") or die('Error connecting to mysql');
mysqli_select_db("panscopic", $link1);
mysqli_query("SET NAMES utf8", $link1);
mysqli_query("SET CHARACTER SET utf8", $link1);


$sql = "SELECT Count(tbl_students.fld_student_id) AS numStuds FROM tbl_students INNER JOIN tbl_stud_class ON tbl_students.fld_student_id = tbl_stud_class.fld_student_id WHERE tbl_stud_class.fld_class_id='" . $_GET['classid'] . "'";

//echo $sql . "<br>";

$query = mysqli_query($link1, $sql) or die("error getting students" . mysqli_error($link1));

list($numStuds) = mysqli_fetch_row($query);

//$sql = "SELECT tbl_stud_testscore.fld_test_id, tbl_tests.fld_desc, Count(tbl_students.fld_student_id) AS fld_student_idOfCount
//FROM (tbl_students INNER JOIN tbl_stud_testscore ON tbl_students.fld_student_id = tbl_stud_testscore.fld_student_id)
//RIGHT JOIN tbl_tests ON tbl_stud_testscore.fld_test_id = tbl_tests.fld_test_id
//WHERE tbl_students.fld_class1=" . $_GET['classid'] . " AND tbl_stud_testscore.fld_score=100
//GROUP BY tbl_stud_testscore.fld_test_id
//ORDER BY tbl_stud_testscore.fld_test_id";


$sql = "SELECT tbl_tests.fld_test_id, tbl_tests.fld_desc, bob.numstuds
FROM tbl_tests LEFT JOIN
(SELECT tbl_stud_testscore.fld_test_id AS testid, count(trev.fld_student_id) AS numstuds
FROM (SELECT tbl_students.fld_student_id, tbl_stud_class.fld_class_id FROM tbl_students INNER JOIN tbl_stud_class
WHERE tbl_stud_class.fld_class_id=" . $_GET['classid'] . " AND tbl_students.fld_student_id=tbl_stud_class.fld_student_id) AS trev INNER JOIN tbl_stud_testscore
ON trev.fld_student_id = tbl_stud_testscore.fld_student_id
WHERE tbl_stud_testscore.fld_score=100
GROUP BY tbl_stud_testscore.fld_test_id) AS bob
ON tbl_tests.fld_test_id = bob.testid
ORDER BY tbl_tests.fld_test_id";

//echo $sql;

$query = mysqli_query($link1, $sql) or die("error getting tests");
//echo mysqli_num_rows($query);
$jsonStrings = array();
while (list($testid, $testDesc, $count) = mysqli_fetch_row($query)) {
//    echo "data is: " . $testid . $testDesc . $count;
    $arr = array('test' => $testDesc, 'count' => $count, 'numstuds' => $numStuds);
    array_push($jsonStrings, $arr);
}
//echo("<br><br>" . $jsonStrings.$count . "<br><br>");
echo json_encode($jsonStrings);


