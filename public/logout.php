<?php
    echo "Logout Successfully ";
    session_destroy();   // function that Destroys Session 
    header("Location: login.php");
?>