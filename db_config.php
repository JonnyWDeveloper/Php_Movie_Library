<?php
/*Running MySQL default setting: user root with default password.
An empty password was not accepted by the Server version: 8.0.18 - MySQL Community Server - GPL.
The database tool used in development was phpMyAdmin Version: 4.9.1.
The webbserver used Apache/2.4.41 (Win64) OpenSSL/1.1.1c PHP/7.3.11.
PHP version: 7.3.11.
PHP extensions: mysqli, curl & mbstring.*/

//Helpfull error reporting while developing.
error_reporting(E_ALL);
ini_set('display_errors', 1);

//The administrator account, with a password, has been created earlier in the MySQL Server.
$host = "localhost";
$username = "administrator";
$password = "pass";  
$database = "movies";

//Try to connect to the local MySQL database movies using the mysqli object. 
 $mysqli = new mysqli($host, $username, $password, $database);

 //On failed connection display error message.
 if ($mysqli->connect_error){
    die("Error message: Could not connect: ".$mysqli->connect_error);
 }
   
?>