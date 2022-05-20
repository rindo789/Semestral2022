<!DOCTYPE html>
<?php
include '../../teacher/include/test_func.php';
include '../../login/include/loginFunctions.inc.php';
include '../include/manage_tests.inc.php';
session_start();
loginCheck();
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/harrystyles.css">
    <script src="../js/create_test.js"></script>
    <script src="../../js/create1.js"></script>
    <title>Document</title>
</head>
<body>
    <div id="menu"><a href="index.php">Domov</a></div>
    <div id="inside">
    <form action='../include/manage_tests_check.php' method='POST' id="test_form">

        <div id="teacher_select_modal">
            <p>Vyber ucitela</p>
            <span onclick="closeTeacherSelection();">X</span>
            <table>
                <thead>
                    <tr>
                        <th>ID uzivatela</th>
                        <th>Meno ucitela</th>
                        <th>ID ucitela</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    echoTeachers();
                ?>
                </tbody>
            </table>
        </div>

        <div id="group_select_modal">
            <p>Vyber skupinu</p>
            <span onclick="closeGroupSelection();">X</span>
            <table id="group_table">
                <thead>
                    <tr>
                        <th>ID skupiny</th>
                        <th>Meno skupiny</th>
                    </tr>
                </thead>
            </table>
        </div>

        <label for="test_name">Názov testu</label> <br>
        <input type="text" placeholder="Zadajte názov testu" name="test_name" required> <br>

        <div id="teacher_select">
            <p>Ucitel</p>
            <p id="selected_teacher"></p>
            <button type="button" onclick="showTeacherSelection();">Vyber učiteľa</button>
            <input type="hidden" id="teacher_selected_value" name="selected_teacher">
        </div>

        <div id="group_select">
            <p>Skupina</p>
            <p id="selected_group"></p>
            <button type="button" onclick="showGroupSelection();">Vyber skupinu</button>
            <input type="hidden" id="group_selected_value" name="selected_group">
        </div>
        <br>
            <textarea name='opis' placeholder="opis" form="test_form"></textarea> <br>
            <button type="button" onclick="CreateQuestion('one')">Jeden výber</button>
            <button type="button" onclick="CreateQuestion('multi')">Vyber mnoho</button>
            <button type="button" onclick="CreateQuestion('text')">Napis odpoved</button> <br>
            
            <button id="submitButton" type="submit" name="createTest">submit</button>
    </form>
    </div>  
</body>
</html>