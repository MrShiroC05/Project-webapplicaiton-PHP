<?php 
    $hostname = "localhost";
    $username = "pma" ;
    $password = "123456" ;
    $dbname = "mydb" ;

	$conn =  new mysqli($hostname, $username, $password, $dbname) or trigger_error(mysqli_error($conn),E_USER_ERROR);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
?>