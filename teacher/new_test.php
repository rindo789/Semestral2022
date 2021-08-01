<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="teach_style.css">
    <title>Document</title>
</head>
<body>
        
        <script src="create_question.js.js"></script>
    <?php
        include 'teacher_functions.php';
        $testName = $_POST['test_name'];
        $testID = checkID(); 
        newTest($testName,$testID);
        if(isset($_POST['submit'])){
            saveTest($testName,$testID);
        }
    ?>
    
    <form action='new_test.php' method='POST' id="test_form">
        <textarea name='opis' placeholder="opis" form="test_form"></textarea> <br>
            <button type="button" onclick="CreateQuestion('one')">Jeden výber</button>
            <button type="button" onclick="CreateQuestion('multi')">Vyber mnoho</button>
            <button type="button" onclick="CreateQuestion('text')">Napis odpoved</button> <br>

            <!--<button type="button" id="counter" value="0" style="display: none;"></button>
            <button type="button" id="real_counter" value="0" style="display: none;"></button> -->
            <button type="submit" name="submit">submit</button>
    </form>
        
</body>
</html>