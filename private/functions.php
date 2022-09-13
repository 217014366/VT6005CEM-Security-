<?php

function generateSalt($length)
{
    $rand_str = "";
    $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
    
    // start of 1b
    for($i = 0; $i < $length; $i++) 
    {
        $rand_str = $rand_str . $chars[rand(0, strlen($chars) - 1)];
    } 
    // end of 1b
    
    return $rand_str;
}


function test_input(&$data)
{

  $data = trim($data);

  $data = stripslashes($data);

  $data = htmlspecialchars($data);

  return $data;
}