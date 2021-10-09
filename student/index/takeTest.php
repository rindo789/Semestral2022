<?php
session_start();
include "../include/studentFunctions.inc.php";
?>

<html>
<head></head>
<body>
<form action='../include/takeTest.inc.php' method='POST' id="test_form">

<?php
loadTestStudent($_SESSION["testIdToEdit"]);
?>

<button type="submit" name="answer">submit</button>
</form>

</body>
</html>
