<?php
	$user = 'u68790'; 
	$pass = ''; 
	$db = new PDO('mysql:host=localhost;dbname=u68790', $user, $pass,
	[PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]); 
?>
