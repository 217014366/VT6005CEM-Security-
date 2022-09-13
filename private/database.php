<?php

define('DB_NAME', 'login_db');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_HOST', 'localhost');

if (!$connection = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME))
{
    die("Connection failed");   
}