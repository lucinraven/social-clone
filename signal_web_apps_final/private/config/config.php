<?php
ob_start(); //Turns on output buffering 
session_start();

$timezone = date_default_timezone_set("Europe/London");

$con = mysqli_connect("localhost", "root", "", "signal_db"); //Connection variable

if($con->connect_error) 
{
	die("Connection failed: ".$con->connect_error);
}

?>