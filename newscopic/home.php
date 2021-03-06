<?php
/**
 * Created by IntelliJ IDEA.
 * User: matthew
 * Date: 2/18/2016
 * Time: 9:45 AM
 */

include("sessionheader.inc");

function convert_datetime($str)
{
    list($date, $time) = explode(' ', $str);
    list($year, $month, $day) = explode('-', $date);
    list($hour, $minute, $second) = explode(':', $time);
    return mktime($hour, $minute, $second, $month, $day, $year);

}

?>
    <style>
        body {
            background-color: white;
            font-size: x-large;
            font-family: 'ubunturegular', Arial, sans-serif;
        }

        #stars {
            width: 90%;
            margin: 0 auto;
            border: 1px solid black;
            border-radius: 5px;
            background-color: azure;
            padding: 2%;
            text-align: center;
        }

        .stardiv {
            margin: auto;
            float: left;
            width: 20%;
        }

        .header {
            text-align: center;
        }

        .testChoice {
            border-radius: 5px;
            background-color: #333333;
            padding: 2%;
            width: 90%;
            margin: 0 auto;
            margin-top: 30px;
            color: greenyellow;
            cursor: pointer;
        }

        .done {
            background-color: sandybrown;
            color: black;
        }

        .perc {
            float: right;
            padding: 5px;
        }

        #reset {
            position: absolute;
        }
    </style>

    <script type="text/javascript">

        function doTest(testid) {
            window.location = "test.php?testid=" + testid;
        }

    </script>

<?php
print   "\n</head>\n<body>";
//print "<img id=\"reset\" src=\"../img/reset.png\" onclick='reset();'>";
print "\n<div class='header'><h2>" . $_SESSION['coursedesc'] . "</h2></div>";

//Get List of Classes the student belongs to
$sql = "SELECT * FROM tbl_stud_class JOIN tbl_classes
ON tbl_stud_class.fld_class_id=tbl_classes.fld_class_id
WHERE tbl_stud_class.fld_student_id='" . $_SESSION['studid'] . "'";
//echo "<br>$sql<br>";
$query = mysqli_query($conn, $sql);


while (list($stud, $class) = mysqli_fetch_row($query)) {
//Get actual scores for this student
    $sql = "SELECT bob.fld_test_id, bob.fld_desc, bob.fld_retain, bob.fld_startdate, bob.fld_enddate, susan.fld_score
FROM (SELECT tbl_tests.fld_test_id, tbl_tests.fld_desc, tbl_tests.fld_retain,tbl_class_tests.fld_startdate, tbl_class_tests.fld_enddate
FROM (tbl_classes INNER JOIN tbl_class_tests ON tbl_classes.fld_class_id = tbl_class_tests.fld_classid)
INNER JOIN tbl_tests ON tbl_class_tests.fld_test_id = tbl_tests.fld_test_id
WHERE tbl_class_tests.fld_classid=" . $class . ") AS bob
LEFT JOIN (SELECT fld_test_id, fld_score FROM tbl_stud_testscore WHERE fld_student_id='" . $_SESSION['studid'] . "') AS susan
ON bob.fld_test_id = susan.fld_test_id
ORDER BY bob.fld_test_id";
//    echo "<br>$sql<br>";
    $query2 = mysqli_query($conn, $sql) or die('something wrong');
    $numStars = mysqli_num_rows($query2);
    $width = floor(100 / $numStars);
    $starSize = round(14 / $numStars, 1);
    if ($starSize > 4) $starSize = 4;
    print "\n<div id=\"stars\">";

    $tests = array();


    while (list ($testid, $testdesc, $testRetain, $start, $end, $score) = mysqli_fetch_row($query2)) {
        $test = array();
        array_push($test, $testid);
        array_push($test, $testdesc);
        array_push($test, $score);
        array_push($test, convert_datetime($start));
        array_push($test, convert_datetime($end));
        array_push($test, $testRetain);
        array_push($tests, $test);

        if ($score == 100) {
            print "\n<div class=\"stardiv\" style=\"width: $width%;font-size: " . $starSize . "em;\">&#x2605;<div style=\"font-size: 1rem;\">$testdesc</div></div>";
        } else {
            print"\n<div class=\"stardiv\" style=\"width: $width%;font-size: " . $starSize . "em;\">&#x2606;<div style=\"font-size: 1rem;\">$testdesc</div></div>";
        }
    }
    print "\n<div style=\"clear: both\"></div>\n</div>";

    foreach ($tests as $val) {
//        echo $val[3]."<br>";
//        echo time();
        if ($val[3] > time() OR $val[4] < time()) {
            continue;
        }

        if ((($val[2] < 100) && $val[5] == -1) || $val[2]===NULL) {
            if ($val[2] > 0) {
                echo "<div class='testChoice' onclick='doTest($val[0]);'> $val[1] <div class='perc'>$val[2]%</div></div>";
            } else {
                echo "<div class='testChoice' onclick='doTest($val[0]);'> $val[1] <div class='perc'></div></div>";
            }
        } else {
            echo "<div class='testChoice done' onclick='doTest($val[0]);'> $val[1]&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;COMPLETE&nbsp;&nbsp;$val[2]%</div>";
        }
    }


    print "\n<hr>";

}


print "</body></html>";

?>