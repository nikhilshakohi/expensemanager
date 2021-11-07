<?php

$host = "localhost";
$username = "root";
$password = ""; /*Blank for XAMPP Application*/
$databaseName = "expenseManager"; /*The one we have created*/

$conn = mysqli_connect($host, $username, $password, $databaseName) or die("Unable to connect to Database");
/*$conn - This parameter can be used for calling the database while using mySQL*/


//The one in the 000webhostapp
/*

$host = "localhost";
$username = "id17875233_root";
$password = "k]BJ24\D#u318u<A"; 
$databaseName = "id17875233_expensemanager"; 
$conn = mysqli_connect($host, $username, $password, $databaseName) or die("Unable to connect to Database");
/*$conn - This parameter can be used for calling the database while using mySQL*/


?>