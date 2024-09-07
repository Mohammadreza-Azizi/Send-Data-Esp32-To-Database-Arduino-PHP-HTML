<?php

$hostname = "fdb1029.awardspace.net"; 
$username = "4272950_temp"; 
$password = "Mm@9901969183"; 
$database = "4272950_temp"; 

$conn = mysqli_connect($hostname, $username, $password, $database);

if (!$conn) { 
	die("Connection failed: " . mysqli_connect_error()); 
} 


//Read the database
if (isset($_POST['check_LED_status'])) {
	$led_id = $_POST['check_LED_status'];	
	$sql = "SELECT * FROM Heater_Status WHERE id = '$led_id';";
	$result   = mysqli_query($conn, $sql);
	$row  = mysqli_fetch_assoc($result);
	if($row['Status'] == 0){
		echo "0";
	}
	else{
		echo "1";
	}	
}


 if (isset ($_POST["temperature"])) {

 
	$t = $_POST["temperature"];

	$sql = "INSERT INTO Temperature (Temperature) VALUE (".$t.")"; 

	if (mysqli_query($conn, $sql)) { 
		echo "\New record created successfully"; 
	} else { 
		echo "Error: " . $sql . "<br>" . mysqli_error($conn); 
	}
 }
	
?>

