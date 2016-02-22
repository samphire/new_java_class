<?php          
include ("sessionheader.inc");
print "\n</head>";
//Select Wrongly Answered Questions Function
function callWrong(){
    
        $sql="SELECT * FROM tbl_qns".$_SESSION['qnstable']."  
        INNER JOIN tbl_response".$_SESSION['qnstable']." 
        ON tbl_qns".$_SESSION['qnstable'].".fld_qnum = tbl_response".$_SESSION['qnstable'].".fld_question_id 
        WHERE tbl_response".$_SESSION['qnstable'].".fld_response <> BINARY tbl_qns".$_SESSION['qnstable'].".fld_answer 
        AND tbl_response".$_SESSION['qnstable'].".fld_student_id=".$_SESSION['studentid']." 
        ORDER BY tbl_qns".$_SESSION['qnstable'].".fld_qnum";
        $query = mysql_query($sql,$GLOBALS['link1']);
        if(!$query){die("oops! " . mysql_error());}
        return $query;
}
        
function calcScore($query){
        $percent_score=round(($_SESSION['numq']-mysql_num_rows($query))/$_SESSION['numq']*100);
        return $percent_score;   
}

function trimbo($stringo){
    //Trims single quotes, then whitespace, then reinserts single quotes lol
    $tempo = trim($stringo, chr(39));
    $tempo = trim($tempo);
    $tempo = chr(39) . $tempo . chr(39);
    return $tempo;
}
          
//format responses
for ($x = 1; $x <= $_SESSION['numq']; $x++){
//$response[$x]=chr(39).mysql_real_escape_string($_POST["response$x"]).chr(39);
$response[$x]=$_POST["response$x"];
}

if ($_SESSION['oneshot']==-1){
$sql="SELECT * FROM tbl_stud_testscore
      WHERE fld_student_id='".$_SESSION['studentid']."'
      AND fld_test_id=".$_SESSION['testid'];
$query = mysql_query($sql,$link1);
$dataexist = mysql_numrows($query);
if ($dataexist>0){
print "<h2>You cannot take this test again!</h2>";
exit();
}
}

if ($_SESSION['retain']==-1){

    //check to see if data exists. If exists, update, else insert  
    $sql = "SELECT * FROM tbl_response".$_SESSION['qnstable']." WHERE fld_student_id=".$_SESSION['studentid'];
    $query = mysql_query($sql,$link1) or die("Query is failing early  " . mysql_error());
    $data = mysql_fetch_array($query);

    if($data){
        //SELECT wrongly answered questions (pre-update!)
        $query = callWrong();
        //UPDATE response table
        $sql = "Update tbl_response".$_SESSION['qnstable']." 
        SET tbl_response".$_SESSION['qnstable'].".fld_response = CASE tbl_response".$_SESSION['qnstable'].".fld_question_id ";
        while (list($qnumber,,$txt1,,,,,,,$answer) = mysql_fetch_row($query)){
            $sql .= " WHEN $qnumber THEN " . trimbo($response[$qnumber]); 
        }
        $sql .= " ELSE tbl_response".$_SESSION['qnstable'].".fld_response END
        WHERE fld_student_id=".$_SESSION['studentid'];
        mysql_query($sql,$link1) or die("Error message is:  " . mysql_error());
        
        //SELECT wrongly answered questions
        $query = callWrong();
        //CALCULATE score
        $percent_score=calcScore($query);
        //UPDATE scores table
        $sql="UPDATE tbl_stud_testscore SET fld_score = $percent_score WHERE fld_student_id='".$_SESSION['studentid']."' AND fld_test_id=".$_SESSION['testid'];
        mysql_query($sql,$link1) or die(mysql_error());
    }

    else {
        // INSERT response table     This should not work because of shuffle... something fishy going on that makes it work, but is it reliable?
        $sql= "INSERT INTO tbl_response".$_SESSION['qnstable']." (fld_student_id,fld_question_id,fld_response) VALUES";
        for ($x=1;$x<=$_SESSION['numq'];$x++){
            $sql.="('".$_SESSION['studentid']."',$x," . trimbo($response[$x]) . "),";
        }    
        $sql = rtrim($sql,",");
        mysql_query($sql,$link1) or die(mysql_error()); 
          
        //SELECT wrongly answered questions
        $query = callWrong();
        //CALCULATE score
        $percent_score = calcScore($query);
        //INSERT scores table
        $sql="INSERT INTO tbl_stud_testscore(fld_student_id, fld_test_id, fld_score) VALUES('".$_SESSION['studentid']."', ".$_SESSION['testid'].",$percent_score)";
        mysql_query($sql,$link1) or die(mysql_error());
    }
}

else{
// the following conditional code is irrelevant. it deletes when there is
// no data to delete when a multi test is taken for the first time.
// though notionally irrelevant to one shot tests, if anyone slips thru
// and manages to take a one shot test twice, there will be two entries in
// tbl_stud_score. It is better to remove condition and keep delete queries
// for all tests.
    //if($_SESSION['oneshot']<1){
        $sql="DELETE FROM tbl_response".$_SESSION['qnstable']. 
        " WHERE tbl_response".$_SESSION['qnstable'].".fld_student_id='".$_SESSION['studentid']."'";
        mysql_query($sql,$link1);
        
        $sql="DELETE FROM tbl_stud_testscore
        WHERE fld_student_id='".$_SESSION['studentid']."'
        AND fld_test_id='".$_SESSION['testid']."'";            
        mysql_query($sql,$link1);
              
    //}
       //INSERT response table     This should not work because of shuffle... something fishy going on that makes it work, but is it reliable?
        //for ($x=1;$x<=$_SESSION['numq'];$x++){
        //$sql="insert into tbl_response".$_SESSION['qnstable']." (fld_student_id,fld_question_id,fld_response) VALUES('".$_SESSION['studentid']."',$x,$response[$x])";
        
        
        $sql= "INSERT INTO tbl_response".$_SESSION['qnstable']." (fld_student_id,fld_question_id,fld_response) VALUES";
        for ($x=1;$x<=$_SESSION['numq'];$x++){
            $sql.="('".$_SESSION['studentid']."',$x," . trimbo($response[$x]) . "),";
        }    
        $sql = rtrim($sql,",");
        mysql_query($sql,$link1) or die(mysql_error());        
        
        
        
        /* require_once "Mail/Mail.php";
 
 $from = "The Webmaster <matt@notborder.org>";
 $to = "The Great One <matt@notborder.org>";
 $subject = "from website";
 $body = $sql;
 
 $host = "mail.notborder.org";
 $username = "matt+notborder.org";
 $password = "rahab";
 
 $headers = array ('From' => $from,
   'To' => $to,
   'Subject' => $subject);
 $smtp = Mail::factory('smtp',
   array ('host' => $host,
     'auth' => true,
     'username' => $username,
     'password' => $password));
 
 $mail = $smtp->send($to, $headers, $body);
          */
        
        
        
        
        
        
        
        
        
        
        
        //mail("matt@notborder.org","from website", $sql);
        //file_put_contents('c:\mylog.txt', $sql, FILE_APPEND);
        //mysql_query($sql,$link1);
        //}
        //SELECT wrongly answered questions
        $query = callWrong();
        //CALCULATE score
        $percent_score = calcScore($query);
        //INSERT scores table
        $sql="insert into tbl_stud_testscore(fld_student_id, fld_test_id, fld_score) VALUES('".$_SESSION['studentid']."', '".$_SESSION['testid']."',$percent_score)";
        mysql_query($sql,$link1) or die(mysql_error());
}
//Following line should normally be commented out. It is used for Questionnaires where no results are to be printed.
//print "<h1>Thank you for answering the questionnaire</h1>"; exit;
if($percent_score<100){
print "\n<body onload='timedFade()'>";
print "\n<div id='container'>\n<img src='images/results-logo-gold.jpg' id='imglogo' alt='logo' />";
print "\n<div id='sisImg'>\n<img id='sisyphus' src='".$_SESSION['global_url']."/images/sisyphus.jpg' height='236' width='200' alt='img' />\n</div>";
} else {
print "\n<div id='container'>\n<div>\n<img src='images/results-logo-gold.jpg' id='imglogo' alt='logo' />\n</div>";
print "\n<div id='sisImg'>\n<img id='sisyphus' src='".$_SESSION['global_url']."/images/great.png' height='236' width='200' alt='img' />\n</div>";
}    

print "\n<div id='userdetails'><fieldset style='height:9em;'><legend>User Details</legend>";
print "\n<br />성명&nbsp;&nbsp;&nbsp;<b>" . $_SESSION['name']."</b>";
print "\n<br /><br />학번&nbsp;&nbsp;&nbsp;<b>" . $_SESSION['studentid'] . "</b><br /><br />";
print "\n</fieldset></div>";

$sql="SELECT COUNT(fld_score) FROM tbl_stud_testscore WHERE fld_test_id =".$_SESSION['testid']." AND fld_score > ".$percent_score;
$bob = mysql_query($sql,$link1) or die (mysql_error());
list ($rank) = mysql_fetch_row($bob);
$rank++;
$percent_score= round($percent_score);
print "\n<div id='score'><fieldset style='height:9em;'><legend>Your Score</legend>";
print "\n<br />당신의 점수: <b>$percent_score %</b><br /><br />당신의 계급: <b>$rank </b><br /><br />";
print "\n</fieldset></div>";

$sql="SELECT MIN(fld_score), MAX(fld_score), AVG(fld_score), COUNT(fld_score) FROM tbl_stud_testscore WHERE fld_test_id =".$_SESSION['testid'];
$stats = mysql_query($sql,$link1) or die(mysql_error());
list($min_score, $max_score, $average_score, $count) = mysql_fetch_row($stats);      
$average_score= round($average_score);

print "\n<div id='stats'><fieldset style='height:9em;'><legend>Other Students</legend>";
print "\n<table><tr>
<td style='width:120px'>완성된 시험:</td><td style='width:50px'><b>$count</b></td><td style='width:15px'></td></tr><tr>
<td style='width:120px'>평균 점수:</td><td style='width:50px'><b>$average_score</b></td><td style='width:15px'>%</td></tr><tr>
<td style='width:120px'>최대한 점수:</td><td style='width:50px'><b>$max_score</b></td><td style='width:15px'>%</td></tr><tr>
<td style='width:120px'>최소한 점수:</td><td style='width:50px'><b>$min_score</b></td><td style='width:15px'>%</td></tr></table>";
print "\n</fieldset></div>";
//print "Value of oneshot is: " . $_SESSION['oneshot'] . ". And percent score is: " . $percent_score . ".";
if(($percent_score != 100) && ($_SESSION['oneshot']>-1)){
    $bob = $_SESSION['testid'] . "/" . $_SESSION['testblurb'];    
    print "<div id='again'>
    <form action='".$_SESSION['global_url']."/test.php' method='post'>\n
    <input type='hidden' name='testselect' value='$bob' />\n
    <input type='submit' id='btnAgain' value='Again' />\n
    </form>\n</div>";
}
     
//PRINT INCORRECT ANSWERS TABLE
if ($percent_score<100) {
if ($_SESSION['panswer']<>-1){
        print "<style type='text/css'>
        td {
        width: 455px;
        }
        th {
        width: 455px;
        }</style>"; 
    }

if ($_SESSION['pwrong']==-1){
print "\n<div id='testqns' style='margin-top: 120px;'><fieldset id='test' style='margin-left: 10px; width:895px;word-wrap: break-word'><legend>Incorrect Answers</legend>";   
print"\n<table id='incorrect'><tr><th>Question</th><th>Your Answer</th>";
    if ($_SESSION['panswer']==-1){
        print "<th>Correct Answer</th>"; 
    }
print "</tr>";

while (list($q,,$txt1,,,,,,,$answer,,,,,,,,$response) = mysql_fetch_row($query)){
    print "\n<tr><td><strong>$q</strong>.   $txt1</td><td>$response</td>";
        if ($_SESSION['panswer']==-1){
            print "<td>$answer</td>";
        }
    print "</tr>";
}
print "\n</table>";
print "\n</fieldset></div>";
}
}

else { 
print "\n<div id='squirrel'>\n<img src='".$_SESSION['global_url']."/images/squirrelmassage.gif' alt='gif' />\n</div>";
print "\n<div id='message'>\n<h1>Congratulations, you have scored 100%<br />Time to enjoy a nice squirrel massage.<br />You've earned it!</h1></div>\n";
}

if (!isset($_POST['praccy'])){
    $_POST['praccy'] = 'off';
}
if ($_POST['praccy']=='on'||$_SESSION['studentid']==920) {
//print "Praccy flag was set";
$sql="DELETE FROM tbl_response".$_SESSION['qnstable']. 
     " WHERE tbl_response".$_SESSION['qnstable'].".fld_student_id='".$_SESSION['studentid']."'";
$query = mysql_query($sql,$link1);
$sql="DELETE FROM tbl_stud_testscore
      WHERE fld_student_id='".$_SESSION['studentid']."'
      AND fld_test_id=".$_SESSION['testid'];
$query = mysql_query($sql,$link1);      
} 
print "\n</div>\n</body>\n</html>";
if($_SESSION['studentid'] != 920){
session_destroy();
}
?>