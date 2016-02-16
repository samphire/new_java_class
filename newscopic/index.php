<?php
/**
 * Created by IntelliJ IDEA.
 * User: matthew
 * Date: 2/16/2016
 * Time: 3:21 PM
 */

include("sessionheader.inc");

$sql = "SELECT * FROM tbl_courses";
$query = mysqli_query($conn, $sql);

?>


<style type="text/css">

    #content {
        width: 96%;
        margin: 0 auto;
        background-color: aliceblue;
    }

    #setup {
        display: none;
    }

    .slider {
        position: fixed;
        width: 100%;
        left: 100%;

        -webkit-transition-duration: 0.5s;
        -moz-transition-duration: 0.5s;
        transition-duration: 0.5s;
    }

    .slider.off {
        left: 100%;
    }

    .slider.on {
        left: 0%;
    }

    .slider.done {
        left: -100%;
    }

    #setPass, #enterPass{
        text-align: center;
    }

    input{
        padding: 5px;
        margin: 10px;
    }

    .selectCourse {
        border-radius: 5px;
        background-color: wheat;
        width: 88%;
        padding: 1%;
        margin-top: 5px;
    }
</style>
</head>
<body>


<div id="content">
    <div id="setup">
        <div class="slider on" id="getCourse">

            <h1>Set Your Details Here</h1>

            <h3>You won't have to set them again</h3>

            <?php
            $lists = array();
            while (list($courseid, $coursedesc) = mysqli_fetch_row($query)) {
                print"<div class=\"selectCourse\" onclick=\"setCourse($courseid, this)\">
                $coursedesc
                </div>
                ";
                $sql = "SELECT * FROM tbl_students JOIN tbl_stud_course
                ON tbl_students.fld_student_id = tbl_stud_course.fld_student_id
                WHERE tbl_stud_course.fld_course_id = " . $courseid;
                $bob = array();
                array_push($bob, $sql);
                array_push($bob, $courseid);
                array_push($lists, $bob);
            }
            print "</div><div class=\"slider off\" id=\"setStudent\">";

            for ($i = 0; $i < count($lists); $i++) {
                $sql = $lists[$i][0];
                $query2 = mysqli_query($conn, $sql);
                print "<div id=\"course" . $lists[$i][1] . "\"> Select User <select name=\"studid\" onchange=\"setStudent(this);\">";
                while (list($id, $name, $pass) = mysqli_fetch_row($query2)) {
                    if(empty($pass)){
                        print "<option class=\"noPass\" value=\"$id\">$id, $name</option>";
                    } else{
                        print "<option value=\"$id\">$id, $name</option>";
                    }
                }
                print "</select></div>";
            }
            print "</div>";
            ?>


            <div class="slider off" id="setPass">
                Create a Password:<br>
                <input type="text" id="pass1" autofocus><br>
                <input type="text" id="pass2" onchange="go(this)">
            </div>
            <div class="slider off" id="enterPass">
                Enter Password:<br>
                <input type="password" id="realPass">
            </div>
        </div>
    </div>


    <script language="JavaScript">
        var courseid, studentid, password;

        if (typeof Storage !== "undefined") {
            //Remove next line in production version!
            localStorage.removeItem("user");
            if (!localStorage.getItem("user")) {
                document.getElementById("setup").style.display = "block";
            }
        }

        function setCourse(id, myEl) {
            courseid = id;
            myEl.parentNode.classList.remove("on");
            myEl.parentNode.classList.add("done");
            myEl.parentNode.nextElementSibling.classList.remove("off");
            myEl.parentNode.nextElementSibling.classList.add("on");

            var nodeArr = myEl.parentNode.nextSibling.childNodes;
            for (var i = 0; i < nodeArr.length; i++) {
                if (nodeArr[i].id != "course" + id) {
                    nodeArr[i].remove();
                }
            }
        }

        function setStudent(mySelect) {

            studentid = mySelect.value;
            var selected = mySelect.options[mySelect.selectedIndex];
            mySelect.parentNode.parentNode.classList.remove("on");
            mySelect.parentNode.parentNode.classList.add("done");

            alert("selected" + selected.classList);

            if(selected.classList == "noPass"){
                mySelect.parentNode.parentNode.nextElementSibling.classList.remove("off");
                mySelect.parentNode.parentNode.nextElementSibling.classList.add("on");
            } else{
                mySelect.parentNode.parentNode.nextElementSibling.nextElementSibling.classList.remove("off");
                mySelect.parentNode.parentNode.nextElementSibling.nextElementSibling.classList.add("on");
                document.getElementById("realPass").setAttribute("onchange", "sendPass(this)");
            }
        }

        function go(myPass) {
            alert('go');
            if (myPass.value === myPass.previousElementSibling.previousElementSibling.value) {
                password = myPass.value;
                alert("data: " + courseid + ", " + studentid + ", " + password);
                putPasswordToDb();
            }
        }

        function putPasswordToDb() {
            var ajax = new XMLHttpRequest();
            ajax.onreadystatechange = function () {
                if (ajax.readyState == 4) {
                    setLocalStorage();
                }
            };
            ajax.open("GET", "putPass.php?user=" + studentid + "&course=" + courseid + "&password=" + password, true);
            ajax.send();
        }

        function setLocalStorage() {
            localStorage.setItem("user", studentid);
            localStorage.setItem("course", courseid);
            localStorage.setItem("password", password);
        }

        function sendPass(myInput){
            alert("pass is: " + myInput.value);
            var ajax = new XMLHttpRequest();
            ajax.onreadystatechange = function () {
                if (ajax.readyState == 4) {
                    alert(ajax.responseText);
                    if(ajax.responseText == 'success'){
                        password = myInput.value;
                        setLocalStorage();
                    } else{
                        alert('wrong password!');
                    }
                }
            };
            ajax.open("GET", "verify.php?user=" + studentid + "&password=" + myInput.value, true);
            ajax.send();
        }

    </script>
</body>
</html>