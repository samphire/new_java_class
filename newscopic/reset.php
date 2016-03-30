<?php
include("sessionheader.inc");

$sql = "SELECT * FROM tbl_tests";
$testQry = mysqli_query($conn, $sql);
$sql = "SELECT * FROM tbl_students";
$studQry = mysqli_query($conn, $sql);
?>

<script type="text/javascript">
    function resetStud(){

        var ajax = new XMLHttpRequest();
        var url = "resetStud.php?stud=" + document.getElementById("stud").value + "&test=" + document.getElementById("test").value;
        alert(url);
        ajax.open("GET", url);
        ajax.send();

    }
</script>



<?php
print "\n</head><body>";

print "Student: ";
print "<select id='stud'>";
while(list($studid, $studname) = mysqli_fetch_row($studQry)){
    print"<option value='$studid'>$studid, $studname</option>";
}
print "</select><br>Test: <select id='test'>";
while(list($testid, $testdesc) = mysqli_fetch_row($testQry)){
    print"<option value='$testid'>$testid, $testdesc</option>";
}
print "</select><br><button onclick='resetStud();' value='hey'>hey</button>";

?>

