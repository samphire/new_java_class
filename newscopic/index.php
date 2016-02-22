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
    body {
        background-color: white;
        font-size: x-large;
        font-family: 'ubunturegular', Arial, sans-serif;
    }

    #setCourse, #setPass, #enterPass, #setStudent {
        top: 30%;
        left: 0%;
        position: absolute;
        width: 90%;
        text-align: center;
        padding: 5%;
    }
    #setCourse{
        top: 10%;
    }

    #setStudent{
        /*line-height: 500px;*/
    }

    .inner {
        width: 70%;
        margin: 0 auto;
    }

    input {
        padding: 5px;
        margin: 10px;
    }

    .on {
        display: block;
    }

    .off {
        display: none;
    }

    option, select {
        font-size: x-large;
        margin-top: 1em;
        color: greenyellow;
        background-color: #333333;
        padding: 0.2em;
    }

    .selectCourse {
        border-radius: 5px;
        background-color: #333333;
        padding: 2%;
        width: 96%;
        margin: 0 auto;
        margin-top: 30px;
        color: greenyellow;
        cursor: pointer;
    }


</style>
<script language="JavaScript">
    var courseid, coursedesc, studentid, password;
    var ajax;
    if (typeof Storage !== "undefined") {
        localStorage.removeItem("user");
        if (localStorage.getItem("user")) {
            courseid = localStorage.getItem("course");
            coursedesc = localStorage.getItem("coursedesc");
            studentid = localStorage.getItem("user");
            password = localStorage.getItem("password");
            headForHome();
        }
    }

    function ajaxCall(method, url, sync) {
        var bob = null;
        ajax = new XMLHttpRequest();
        ajax.open(method, url, sync);
        ajax.onreadystatechange = function () {
            if (ajax.readyState == 4) {
                bob = ajax.responseText;
            }
        };
        ajax.send();
        return bob;
    }

    function headForHome() {
        //set session variables in ajax
        var resp = ajaxCall("GET", "login.php?courseid=" + courseid + "&coursedesc=" + coursedesc + "&studentid=" + studentid, false);
//        alert(resp);
        window.location = "home.php";
    }
</script>
</head>
<body>
<div id="setCourse" class="on">
    <div class="inner">
        <h2>Set Your Details Here</h2>

        <h3>You won't have to set them again</h3>
        <?php
        $lists = array();
        while (list($courseid, $coursedesc) = mysqli_fetch_row($query)) {
            print"\n<div class=\"selectCourse\" onclick=\"setCourse($courseid, '$coursedesc', this)\">
                \n$coursedesc
                \n</div><!--end selectCourse-->";
            $sql = "SELECT * FROM tbl_students JOIN tbl_stud_course
                ON tbl_students.fld_student_id = tbl_stud_course.fld_student_id
                WHERE tbl_stud_course.fld_course_id = " . $courseid;
            $bob = array();
            array_push($bob, $sql);
            array_push($bob, $courseid);
            array_push($lists, $bob);
        }
        print "\n</div><!--end inner-->\n</div><!--end setCourse-->\n<div id=\"setStudent\" class=\"off\">\n<div class=\"inner\">";

        for ($i = 0; $i < count($lists); $i++) {
            $sql = $lists[$i][0];
            $query2 = mysqli_query($conn, $sql);
            print "\n<div id=\"course" . $lists[$i][1] . "\"> \nSelect User<br> \n<select onchange=\"setStudent(this);\">";
            print "\n<option value=\"\" disabled selected>select student</option>";
            while (list($id, $name, $pass) = mysqli_fetch_row($query2)) {
                if (empty($pass)) {
                    print "\n<option class=\"noPass\" value=\"$id\">$id, $name</option>";
                } else {
                    print "\n<option value=\"$id\">$id, $name</option>";
                }
            }
            print "\n</select>\n</div><!--end course" . $lists[$i][1] . "-->";
        }
        print "\n</div><!--end inner-->";
        print "\n</div><!--end setStudent-->";
        ?>

        <div id="setPass" class="off">
            <div class="inner">
                Create a Password:<br>
                <input type="text" id="pass1" autofocus><br>
                <input type="text" id="pass2" onchange="go(this)">
            </div>
            <!--end inner-->
        </div>
        <!--end setPass-->
        <div id="enterPass" class="off">
            <div class="inner">
                Enter Password:<br>
                <input type="password" id="realPass">
            </div>
            <!--end inner-->
        </div>
        <!--end enterPass-->


        <script language="JavaScript">

            function setCourse(id, desc, myEl) {
                courseid = id;
                coursedesc = desc;
                myEl.parentNode.parentNode.classList.remove("on");
                myEl.parentNode.parentNode.classList.add("off");
                myEl.parentNode.parentNode.nextElementSibling.classList.remove("off");
                myEl.parentNode.parentNode.nextElementSibling.classList.add("on");

                var startEl = myEl.parentNode.parentNode.nextElementSibling.firstElementChild;
//                alert("startEl is " + startEl.tagName + ", " + startEl.classList + ", " + startEl.id);
                var nodeArr = startEl.childNodes;
//                alert("there are " + nodeArr.length + " child nodes\nNow will recurse the children");
//                recurseDomChildren(startEl, true);

                for (var i = 0; i < nodeArr.length; i++) {
                    var el = nodeArr[i];
//                    alert(i+1 + "of " + nodeArr.length + ". " + el.nodeName + ", " + el.nodeType + ", " + el.nodeValue + ", " + el.id);
                    if (nodeArr[i].tagName == "DIV" && nodeArr[i].id != "course" + id) {
//                        alert("removing " + nodeArr[i].id);
                        nodeArr[i].remove();
                    }
                }
            }
            function setStudent(mySelect) {
                studentid = mySelect.value;
                var selected = mySelect.options[mySelect.selectedIndex];
                var setStudentNode = mySelect.parentNode.parentNode.parentNode;
                setStudentNode.classList.remove("on");
                setStudentNode.classList.add("off");
                var setPassNode;
                if (selected.classList == "noPass") {
                    setPassNode = setStudentNode.nextElementSibling;
                    setPassNode.classList.remove("off");
                    setPassNode.classList.add("on");
                } else {
                    setPassNode = setStudentNode.nextElementSibling.nextElementSibling;
                    setPassNode.classList.remove("off");
                    setPassNode.classList.add("on");
                    document.getElementById("realPass").setAttribute("onchange", "sendPass(this)");
                }
            }
            function go(myPass) {
                if (myPass.value === myPass.previousElementSibling.previousElementSibling.value) {
                    password = myPass.value;
                    putPasswordToDb();
                } else {
                    alert("Passwords do not match. Try again!");
                }
            }
            function putPasswordToDb() {
                var url = "putPass.php?user=" + studentid + "&course=" + courseid + "&password=" + password;
                var responseText = ajaxCall("GET", url, false);
                if (responseText == 'success') {
                    setLocalStorage();
                    headForHome();
                } else {
                    alert('something wrong! Contact 010 4357 2757');
                }


            }
            function setLocalStorage() {
                localStorage.setItem("user", studentid);
                localStorage.setItem("course", courseid);
                localStorage.setItem("coursedesc", coursedesc);
                localStorage.setItem("password", password);
            }
            function sendPass(myInput) {
                var url = "verify.php?user=" + studentid + "&password=" + myInput.value;
                var responseText = ajaxCall("GET", url, false);
                if (responseText == 'success') {
                    password = myInput.value;
                    setLocalStorage();
                    headForHome();
                } else {
                    alert('wrong password');
                }
            }


            function recurseDomChildren(start, output) {
                var nodes;
                console.log("START IS: " + start.tagName + ", " + start.id);
                if (start.childNodes) {
                    nodes = start.childNodes;
                    loopNodeChildren(nodes, output);
                }
            }

            function loopNodeChildren(nodes, output) {
                var node;
                for (var i = 0; i < nodes.length; i++) {
                    node = nodes[i];
                    if (output) {
                        outputNode(node);
                    }
                    if (node.childNodes) {
                        recurseDomChildren(node, output);
                    }
                }
            }

            function outputNode(node) {
                var whitespace = /^\s+$/g;
                if (node.nodeType === 1) {
                    console.log("element: " + node.tagName + ", " + node.id + ", " + node.classList);
                } else if (node.nodeType === 3) {
                    //clear whitespace text nodes
                    node.data = node.data.replace(whitespace, "");
                    if (node.data) {
                        console.log("text: " + node.data);
                    }
                }
            }


        </script>
</body>
</html>