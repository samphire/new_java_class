<?php
include("sessionheader.inc");
?>
    <style type="text/css">
        #again {
            position: absolute;
            left: 28%;
            top: 30%;
            font-family: 'ubunturegular', Arial, sans-serif;
            font-size: 3em;
            text-align: center;
            background-color: #333333;
            color: yellowgreen;
            border-radius: 5px;
            width: 40%;
            padding: 2%;
        }

        #btnAgain {
            margin: 1rem;
            width: 240px;
            height: 105px;
            text-align: right;
            font-size: 0.7em;
        }

        input {
            padding: 3%;
            font-size: 0.5em;
            border-radius: 8px;
        }

        #home {
            position: fixed;
            left: 0px;
            top: 0px;
            height: 100%;
            cursor: pointer;
        }

        @media screen and (max-width: 760px) {
            #again {
                left: 3%;
                width: 90%;
            }

            body {
                padding-left: 0%;
                margin-left: 0%;
            }
        }

    </style>

    <script type="text/javascript">
        function goHome() {
            window.location.href = "home.php";
        }

        imgArr = ['meme.png', 'fem.png', 'rog.png', 'fees.png', 'jimmy.png', 'root.png', 'angry kitty.png', 'kitty.png', 'anger.png', 'monkey.png', 'soldier.png', 'guy.png', 'moon.png', 'bird.png', 'symbol.png', 'anger'];


        window.onload = function () {
            indx = Math.floor(Math.random() * imgArr.length);
            var randomImage = "url('img/" + imgArr[indx] + "')";
            var el = document.getElementById("btnAgain");
            el.style.background = "azure " + randomImage + " no-repeat";
            el.value = "Again";
        }
    </script>

<?php
print "\n</head>";
print "<div id='home' onclick='goHome()'><img src='home.png'></div>";
//Select Wrongly Answered Questions Function
function callWrong($con)
{
    $sql = "SELECT * FROM tbl_qns" . $_SESSION['qnstable'] . "
        INNER JOIN tbl_response" . $_SESSION['qnstable'] . "
        ON tbl_qns" . $_SESSION['qnstable'] . ".fld_qnum = tbl_response" . $_SESSION['qnstable'] . ".fld_question_id
        WHERE tbl_response" . $_SESSION['qnstable'] . ".fld_response <> BINARY tbl_qns" . $_SESSION['qnstable'] . ".fld_answer
        AND tbl_response" . $_SESSION['qnstable'] . ".fld_student_id='" . $_SESSION['studid'] . "'
        ORDER BY tbl_qns" . $_SESSION['qnstable'] . ".fld_qnum";

//    echo "<br>$sql<br>";


    $query = mysqli_query($con, $sql);

    if (!$query) {
        die("<br><br>oops! " . mysqli_error($con));
    }
    return $query;
}

function calcScore($query)
{
    $percent_score = round(($_SESSION['numq'] - mysqli_num_rows($query)) / $_SESSION['numq'] * 100);
    return $percent_score;
}

function trimbo($stringo)
{
    //Trims single quotes, then whitespace, then reinserts single quotes
    $tempo = trim($stringo, chr(39));
    $tempo = trim($tempo);
    $tempo = chr(39) . $tempo . chr(39);
    return $tempo;
}

//format responses
for ($x = 1; $x <= $_SESSION['numq']; $x++) {
    $response[$x] = $_POST["response$x"];
}

if ($_SESSION['retain'] == -1) {

//    echo "retain<br>";

    //check to see if data exists. If exists, update, else insert  
    $sql = "SELECT * FROM tbl_response" . $_SESSION['qnstable'] . " WHERE fld_student_id=" . $_SESSION['studid'];
    $query = mysqli_query($conn, $sql) or die("Query is failing early  " . mysqli_error($conn));
    $data = mysqli_fetch_array($query);

    if ($data) {

//        echo "data<br>";
        //If not callWrong, the update query below will replace all values in tbl_reponse!!!
        $query = callWrong($conn);
        //UPDATE response table
        $sql = "Update tbl_response" . $_SESSION['qnstable'] . "
        SET tbl_response" . $_SESSION['qnstable'] . ".fld_response = CASE tbl_response" . $_SESSION['qnstable'] . ".fld_question_id ";
        while (list($qnumber, , $txt1, , , , , , , $answer) = mysqli_fetch_row($query)) {
            $sql .= " WHEN $qnumber THEN " . trimbo($response[$qnumber]);
        }
        $sql .= " ELSE tbl_response" . $_SESSION['qnstable'] . ".fld_response END
        WHERE fld_student_id=" . $_SESSION['studid'];

//        echo $sql."<br>";

        mysqli_query($conn, $sql) or die("Error message is:  " . mysqli_error($conn));

        //SELECT wrongly answered questions
        $query = callWrong($conn);
        //CALCULATE score
        $percent_score = calcScore($query);
        //UPDATE scores table
        $sql = "UPDATE tbl_stud_testscore SET fld_score = $percent_score WHERE fld_student_id='" . $_SESSION['studid'] . "' AND fld_test_id=" . $_SESSION['testid'];
        mysqli_query($conn, $sql) or die(mysqli_error($conn));
    } else {

//        echo "no data<br>";
        // INSERT response table     This should not work because of shuffle... something fishy going on that makes it work, but is it reliable?
        $sql = "INSERT INTO tbl_response" . $_SESSION['qnstable'] . " (fld_student_id,fld_question_id,fld_response) VALUES";
        for ($x = 1; $x <= $_SESSION['numq']; $x++) {
            $sql .= "('" . $_SESSION['studid'] . "',$x," . trimbo($response[$x]) . "),";
        }
        $sql = rtrim($sql, ",");
        mysqli_query($conn, $sql) or die(mysqli_error($conn));

        //SELECT wrongly answered questions
        $query = callWrong($conn);
        //CALCULATE score
        $percent_score = calcScore($query);
        //INSERT scores table
        $sql = "INSERT INTO tbl_stud_testscore(fld_student_id, fld_test_id, fld_score) VALUES('" . $_SESSION['studid'] . "', " . $_SESSION['testid'] . ",$percent_score)";
        mysqli_query($conn, $sql) or die(mysqli_error($conn));
    }
} else {

//    echo "deleting responses";
    $sql = "DELETE FROM tbl_response" . $_SESSION['qnstable'] .
        " WHERE tbl_response" . $_SESSION['qnstable'] . ".fld_student_id='" . $_SESSION['studid'] . "'";
    mysqli_query($conn, $sql);

//    echo "deleting scores";
    $sql = "DELETE FROM tbl_stud_testscore
        WHERE fld_student_id='" . $_SESSION['studid'] . "'
        AND fld_test_id='" . $_SESSION['testid'] . "'";
    mysqli_query($conn, $sql);

    $sql = "INSERT INTO tbl_response" . $_SESSION['qnstable'] . " (fld_student_id,fld_question_id,fld_response) VALUES";
    for ($x = 1; $x <= $_SESSION['numq']; $x++) {
        $sql .= "('" . $_SESSION['studid'] . "',$x," . trimbo($response[$x]) . "),";
    }
    $sql = rtrim($sql, ",");
    mysqli_query($conn, $sql) or die("damn!" . mysqli_error($conn));

    //SELECT wrongly answered questions
    $query = callWrong($conn);
    //CALCULATE score
    $percent_score = calcScore($query);
    //INSERT scores table
    $sql = "insert into tbl_stud_testscore(fld_student_id, fld_test_id, fld_score) VALUES('" . $_SESSION['studid'] . "', '" . $_SESSION['testid'] . "',$percent_score)";
    mysqli_query($conn, $sql) or die("woah!" . mysqli_error($conn));
}

$percent_score = round($percent_score);

if ($percent_score != 100) {
    $bob = $_SESSION['testid'] . "/" . $_SESSION['testblurb'];

    print "<div id='again'>\n$percent_score%
    <form action='" . $_SESSION['global_url'] . "/test.php' method='get'>\n
    <input type='submit' id='btnAgain' value='' />\n
    </form>\n</div>";
} else {
    print "<div id='again'>만점</div>";
}

print "\n</div>\n</body>\n</html>";

