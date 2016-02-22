<?php
/**
 * Created by IntelliJ IDEA.
 * User: matthew
 * Date: 2/19/2016
 * Time: 1:45 PM
 */
include("sessionheader.inc");
?>
    <style type="text/css">
        html {
            font-size: 22px;
        }

        body {
            background-color: white;
            font-size: x-large;
            font-family: 'ubunturegular', Arial, sans-serif;

        }

        .questionBlock {
            /*background-color: #8F979A;*/
            background-color: #EAEDDA;
            /*background-color: #F4EFE1;*/

            border: 1px solid black;
            border-radius: 5px;
            padding: 3%;
            margin-bottom: 1em;
        }

        .question {
            font-weight: bold;
        }

        .rubrik {
            display: block;
            color: #333333;
            font-weight: bold;
            font-size: 1.2em;
            margin-bottom: 0.5em;

        }

        input[type=radio] {
            width: 2em;
            height: 2em;
            padding: 10px;
            border-radius: 50px;
            margin-bottom: 25px;
        }

        select {
            font-size: 1em;
            width: 100%;

        }

        input[type=checkbox] {
            width: 2em;
            height: 2em;
            padding: 10px;
            margin-bottom: 25px;
        }

        input[type=submit] {
            font-size: 1.2em;
            border-radius: 0.7em;
            padding: 0.3em;
            color: #333333;
            background-color: #62994E;
        }

    </style>


<?php
print "\n<object id='Player' height='0' width='0' classid='CLSID:6BF52A52-394A-11d3-B153-00C04F79FAA6'></object>";


//get TEST data

if (isset($_GET['testid'])) {
    $_SESSION['testid'] = $_GET['testid'];
}

$sql = "SELECT * from tbl_tests WHERE fld_test_id='" . $_SESSION['testid'] . "'";
$query = mysqli_query($conn, $sql) or die("strange problem");

//list(,$desc,,,,$end,$shuffle,$pwrong,$panswer,$oneshot,$retain,$timer,$qnstable) = mysql_fetch_row($query);
list(, $_SESSION['desc'], $_SESSION['course'], , , , $_SESSION['end'], $_SESSION['shuffle'], $_SESSION['pwrong'],
    $_SESSION['panswer'], $_SESSION['oneshot'], $_SESSION['ppraccy'], $_SESSION['retain'], $_SESSION['timer'],
    $_SESSION['qnstable']) = mysqli_fetch_row($query);

$sql = "SELECT * from tbl_qns" . $_SESSION['qnstable'];
$query = mysqli_query($conn, $sql) or die("some problem");
$_SESSION['numq'] = mysqli_num_rows($query);


//STOPS ENTER KEY FROM SUBMITTING FORM
print "<script type='text/javascript'>
    \nfunction stopRKey(evt) {
    \nvar evt = (evt) ? evt : ((event) ? event : null);
    \nvar node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
    \nif (evt.keyCode == 13) {return false;}
    \n}
    \ndocument.onkeypress = stopRKey;
    \n</script>";
print "\n</head>\n<body>";

//get Questions
if ($_SESSION['retain'] == -1) {
    $sql = "SELECT * FROM tbl_response" . $_SESSION['qnstable'] . " WHERE fld_student_id=" . $_SESSION['studid'];
    $query = mysqli_query($conn, $sql);
    $data = mysqli_fetch_array($query);

    if ($data) {
        $sql = "SELECT * FROM tbl_qns" . $_SESSION['qnstable'] . " INNER JOIN tbl_response" . $_SESSION['qnstable'] . " ON tbl_qns" . $_SESSION['qnstable'] . ".fld_qnum = tbl_response" . $_SESSION['qnstable'] . ".fld_question_id
        WHERE tbl_response" . $_SESSION['qnstable'] . ".fld_response<> BINARY tbl_qns" . $_SESSION['qnstable'] . ".fld_answer AND tbl_response" . $_SESSION['qnstable'] . ".fld_student_id=" . $_SESSION['studid'] . " ORDER BY tbl_qns" . $_SESSION['qnstable'] . ".fld_qnum";
        $query = mysqli_query($conn, $sql);
        $numrows = mysqli_num_rows($query);
        if ($numrows < 1) {
            print "<h1>You have already completed this test</h1>";
            exit;
        }
    } else {
        $sql = "SELECT * from tbl_qns" . $_SESSION['qnstable'];
        $query = mysqli_query($conn, $sql);
    }
}

//QUESTIONS

print "\n\n\n<form enctype='multipart/form-data' name='testqns' action='" . $_SESSION['global_url'] .
    "/result.php' method='post' autocomplete='off'>\n\n";

//Put questions into array
$counter = 0;
while (list($a, , $b, $c, $d, $e, $f, $m, $n, $g, $h, $i, $j, $k, $l) = mysqli_fetch_row
($query)) {
    $queshy[$counter]['qnum'] = $a;
    $queshy[$counter]['txt1'] = $b;

    $bob = htmlentities($c, ENT_QUOTES, 'UTF-8');
    $queshy[$counter]['txt2'] = htmlspecialchars_decode($bob, ENT_NOQUOTES);

    $bob = htmlentities($d, ENT_QUOTES, 'UTF-8');
    $queshy[$counter]['txt3'] = htmlspecialchars_decode($bob, ENT_NOQUOTES);

    $bob = htmlentities($e, ENT_QUOTES, 'UTF-8');
    $queshy[$counter]['txt4'] = htmlspecialchars_decode($bob, ENT_NOQUOTES);

    $bob = htmlentities($f, ENT_QUOTES, 'UTF-8');
    $queshy[$counter]['txt5'] = htmlspecialchars_decode($bob, ENT_NOQUOTES);

    $bob = htmlentities($m, ENT_QUOTES, 'UTF-8');
    $queshy[$counter]['txt6'] = htmlspecialchars_decode($bob, ENT_NOQUOTES);

    $bob = htmlentities($n, ENT_QUOTES, 'UTF-8');
    $queshy[$counter]['txt7'] = htmlspecialchars_decode($bob, ENT_NOQUOTES);

    $queshy[$counter]['answer'] = $g;
    $queshy[$counter]['type'] = $h;
    $queshy[$counter]['rubrik'] = $i;
    $queshy[$counter]['image'] = $j;
    $queshy[$counter]['audio'] = $k;
    $queshy[$counter]['video'] = $l;

    $counter++;
}
if ($_SESSION['shuffle'] == -1) {
    shuffle($queshy);
}

//Print Questions on Page
foreach ($queshy as $val => $wow) {
    $qnumdisplay = $val + 1; //prints from 'question 1' even when retain correct
    $qnumdisplay = $queshy[$val]['qnum']; //prints the actual question number in the test data
    $lenput = strlen($queshy[$val]['answer']) + 5;

    if ($queshy[$val]['rubrik'] <> "") {
        print "\n<span class='rubrik'>" . $queshy[$val]['rubrik'] . "</span>";
    }

    if ($queshy[$val]['image'] <> "") {
        print "\n<table cellpadding=0><tr><td><img src='" . $_SESSION['global_url'] .
            "/images/" . $queshy[$val]['image'] . "' align='left' class='dropshadow' alt='img'/></td></tr><tr><td>";
    }

    if ($queshy[$val]['audio'] <> "") {
        print "\n\n\n<input type='button' name='bob3' value='play' OnClick='Player.url=" . chr(34) . "media/audio/" . $queshy[$val]['audio'] . chr(34) . "; Player.controls.play();'>";
    }

    if ($queshy[$val]['video'] <> "") {
        print "\n\n\n<object id='Player$val' height='240' width='320' classid='CLSID:6BF52A52-394A-11d3-B153-00C04F79FAA6' type='application/x-oleobject' style='float:right;'>\n<PARAM name='uiMode' value='none'>
</object>";
        print "\n\n\n<input type='button' name='bob' value='play' OnClick='Player$val.url=" . chr(34) . "media/video/" . $queshy[$val]['video'] . chr(34) . "; Player$val.controls.play();'>";
    }

    print "<div class='questionBlock'>";

    switch ($queshy[$val]['type']) {
        case "1":

            if (substr($queshy[$val]['txt1'], 0, 8) == "Question") {
                print "\n\n\n<span class='question'>" . substr($queshy[$val]['txt1'], 0, 11) . "</span>&nbsp;&nbsp;&nbsp;&nbsp;" . substr($queshy[$val]['txt1'], 12);
            } else {
                print "\n\n\n<span class='question'>Q " . $qnumdisplay . "</span>&nbsp;&nbsp;&nbsp;&nbsp;" . $queshy[$val]['txt1'];
            }
            print "\n\n<input type='text' size='$lenput' name='response" . $queshy[$val]['qnum'] . "' /> " . $queshy[$val]['txt2'] . "<br><br>";
            print"</div>";
            break;


        case "2":
            if (substr($queshy[$val]['txt1'], 0, 8) == "Question") {
                print "\n\n\n<span class='question'>" . substr($queshy[$val]['txt1'], 0, 11) . "</span>&nbsp;&nbsp;&nbsp;&nbsp;" . substr($queshy[$val]['txt1'], 12);
            } else {
                print "\n\n\n<span class='question'>Q " . $qnumdisplay . "</span>&nbsp;&nbsp;&nbsp;&nbsp;" . $queshy[$val]['txt1'];
            }
            $brad = "<br><br>\n<select name='response" .
                $queshy[$val]['qnum'] . "'>\n<option></option>\n<option>" . $queshy[$val]['txt2'] .
                "</option>\n<option>" . $queshy[$val]['txt3'] . "</option>";

            if ($queshy[$val]['txt4'] <> "") {
                $brad .= "\n <option>" . $queshy[$val]['txt4'] . " </option >";
            }
            if ($queshy[$val]['txt5'] <> "") {
                $brad .= "\n <option>" . $queshy[$val]['txt5'] . " </option >";
            }
            if ($queshy[$val]['txt6'] <> "") {
                $brad .= "\n <option>" . $queshy[$val]['txt6'] . " </option >";
            }
            if ($queshy[$val]['txt7'] <> "") {
                $brad .= "\n <option>" . $queshy[$val]['txt7'] . " </option >";
            }
            $brad .= "\n</select> \n<br><br></div>";
            print $brad;
            break;

        case "3":
            if (substr($queshy[$val]['txt1'], 0, 8) == "Question") {
                print "\n<span class='question'>" . substr($queshy[$val]['txt1'], 0, 11) . "</span>&nbsp;&nbsp;&nbsp;&nbsp;" . substr($queshy[$val]['txt1'], 12);
            } else {
                print "\n\n\n<span class='question'>\nQ" . $qnumdisplay . "</span>&nbsp;&nbsp;&nbsp;&nbsp;" . $queshy[$val]['txt1'];
            }

            $izzy = "\n<br><br>\n<input type='radio' name='response" . $queshy[$val]['qnum'] . "' id='" . $queshy[$val]['qnum'] . "a' value=" . chr(34) . $queshy[$val]['txt2'] . chr(34) . " />&nbsp;&nbsp;<label for='" . $queshy[$val]['qnum'] . "a'>" . $queshy[$val]['txt2'] . "</label>";
            $izzy .= "\n<br>\n<input type='radio' name='response" . $queshy[$val]['qnum'] . "' id='" . $queshy[$val]['qnum'] . "b' value=" . chr(34) . $queshy[$val]['txt3'] . chr(34) . " />&nbsp;&nbsp;<label for='" . $queshy[$val]['qnum'] . "b'>" . $queshy[$val]['txt3'] . "</label>";

            if ($queshy[$val]['txt4'] <> "") {
                $izzy .= "<br>\n<input type='radio' name='response" . $queshy[$val]['qnum'] . "' id='" . $queshy[$val]['qnum'] . "c' value=" . chr(34) . $queshy[$val]['txt4'] . chr(34) . " />&nbsp;&nbsp;<label for='" . $queshy[$val]['qnum'] . "c'>" . $queshy[$val]['txt4'] . "</label>";
            }
            if ($queshy[$val]['txt5'] <> "") {
                $izzy .= "<br>\n<input type='radio' name='response" . $queshy[$val]['qnum'] . "' id='" . $queshy[$val]['qnum'] . "d' value=" . chr(34) . $queshy[$val]['txt5'] . chr(34) . " />&nbsp;&nbsp;<label for='" . $queshy[$val]['qnum'] . "d'>" . $queshy[$val]['txt5'] . "</label>";
            }
            if ($queshy[$val]['txt6'] <> "") {
                $izzy .= "<br>\n<input type='radio' name='response" . $queshy[$val]['qnum'] . "' id='" . $queshy[$val]['qnum'] . "e' value=" . chr(34) . $queshy[$val]['txt6'] . chr(34) . " />&nbsp;&nbsp;<label for='" . $queshy[$val]['qnum'] . "e'>" . $queshy[$val]['txt6'] . "</label>";
            }
            if ($queshy[$val]['txt7'] <> "") {
                $izzy .= "<br>\n<input type='radio' name='response" . $queshy[$val]['qnum'] . "' id='" . $queshy[$val]['qnum'] . "f' value=" . chr(34) . $queshy[$val]['txt7'] . chr(34) . " />&nbsp;&nbsp;<label for='" . $queshy[$val]['qnum'] . "f'>" . $queshy[$val]['txt7'] . "</label>";
            }

            print $izzy;
            print"</div>";
            break;


        /*case "4":
            if (substr($queshy[$val]['txt1'],0,8)=="Question"){
            print "\n\n\n <span class='question' > ". substr($queshy[$val]['txt1'],0,11)."</span >&nbsp;&nbsp;&nbsp;&nbsp;".substr($queshy[$val]['txt1'],12);
            }
            else{
            print "\n\n\n <span class='question' > Question " . $qnumdisplay . " </span >&nbsp;&nbsp;&nbsp;&nbsp;" . $queshy[$val]['txt1'];
            }
            print "\n <br><br > \n<input type = 'hidden' name = 'MAX_FILE_SIZE' value = '8000000' /><input type = 'file' name = 'file' ><br ><br > ";
            break;*/

        case
        "5":

            if (substr($queshy[$val]['txt1'], 0, 8) == "Question") {
                print "\n<span class='question'>" . substr($queshy[$val]['txt1'], 0, 11) . "</span>&nbsp;&nbsp;&nbsp;&nbsp;" . substr($queshy[$val]['txt1'], 12);
            } else {
                print "\n\n\n<span class='question'>\nQ" . $qnumdisplay . " </span>&nbsp;&nbsp;&nbsp;&nbsp;" . $queshy[$val]['txt1'];
            }
            $izzy = "\n<br><br>\n<input type='checkbox' id='" . $queshy[$val]['qnum'] . "a' onclick='bob" . $queshy[$val]['qnum'] . "()' />&nbsp;&nbsp;<label for='" . $queshy[$val]['qnum'] . "a'> " . $queshy[$val]['txt2'] . "</label><br>\n<input type='checkbox' id='" . $queshy[$val]['qnum'] . "b' onclick='bob" . $queshy[$val]['qnum'] . "()' />&nbsp;&nbsp;<label for='" . $queshy[$val]['qnum'] . "b'>" . $queshy[$val]['txt3'] . "</label>";
            $jav = "\n<script type='text/javascript'>\nfunction bob" . $queshy[$val]['qnum'] . "(){\nvar cat = document.getElementById('" . $queshy[$val]['qnum'] . "a').checked + ',' + document.getElementById('" . $queshy[$val]['qnum'] . "b').checked + ','";
            $jav_mid = " + 'false,false,false,false,';";

            if ($queshy[$val]['txt4'] <> "") {
                $izzy .= "<br>\n<input type='checkbox' id='" . $queshy[$val]['qnum'] . "c' onclick='bob" . $queshy[$val]['qnum'] . "()' />&nbsp;&nbsp;<label for='" . $queshy[$val]['qnum'] . "c'>" . $queshy[$val]['txt4'] . "</label>";
                $jav .= " + document.getElementById('" . $queshy[$val]['qnum'] . "c').checked + ','";
                $jav_mid = " + 'false,false,false,';";
            }

            if ($queshy[$val]['txt5'] <> "") {
                $izzy .= "<br>\n<input type='checkbox' id='" . $queshy[$val]['qnum'] . "d' onclick='bob" . $queshy[$val]['qnum'] . "()' />&nbsp;&nbsp;<label for='" . $queshy[$val]['qnum'] . "d'>" . $queshy[$val]['txt5'] . "</label>";
                $jav .= " + document.getElementById('" . $queshy[$val]['qnum'] . "d').checked + ','";
                $jav_mid = " + 'false,false,';";
            }
            if ($queshy[$val]['txt6'] <> "") {
                $izzy .= "<br>\n<input type='checkbox' id='" . $queshy[$val]['qnum'] . "e' onclick='bob" . $queshy[$val]['qnum'] . "()' />&nbsp;&nbsp;<label for='" . $queshy[$val]['qnum'] . "e'>" . $queshy[$val]['txt6'] . "</label>";
                $jav .= " + document.getElementById('" . $queshy[$val]['qnum'] . "e').checked + ','";
                $jav_mid = " + 'false,';";
            }
            if ($queshy[$val]['txt7'] <> "") {
                $izzy .= "<br>\n<input type='checkbox' id='" . $queshy[$val]['qnum'] . "f' onclick='bob" . $queshy[$val]['qnum'] . "()' />&nbsp;&nbsp;<label for='" . $queshy[$val]['qnum'] . "f'>" . $queshy[$val]['txt7'] . "</label>";
                $jav .= " + document.getElementById('" . $queshy[$val]['qnum'] . "f').checked + ','";
                $jav_mid = "";
            }

            $izzy .= "\n<input type='hidden' name='response" . $queshy[$val]['qnum'] . "' id='dick" . $queshy[$val]['qnum'] . "'>";
            $jav .= $jav_mid;
            $jav .= "\ndocument.getElementById('dick" . $queshy[$val]['qnum'] . "').value = cat;\n}\n </script > ";


//            echo "<h1>$izzy</h1>";

            error_log( $izzy, 3, "bob.txt");

            print $izzy;
            print $jav;
            print"</div>";
            break;


    }
    if ($queshy[$val]['image'] <> "") {
        print "\n </td ></tr ></table > ";
    }
}
print "\n\n <center><input type = 'submit' name = 'submit' id = 'sendbutton' value = \"Send\" /></center>";
if ($_SESSION['oneshot'] == -1 && $_SESSION['ppraccy'] == -1) {
    print"\n<hr /><strong>연습만?</strong>  <input type='checkbox' name='praccy' align='left'>";
}
print "\n</form>\n\n";
print "\n</body>\n</html>";


