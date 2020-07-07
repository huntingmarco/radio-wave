<!-- 
1.purpose: this file is to get the status messages of uploading file.
2. authors: Group2
-->
<?php
function displayStatus($status):array{
	$err_msgs=array();
	if ($status == "FailMove"){
		$err_msgs[]="File upload failed - failed to move file to permanent storage";
	} 
	else if ($status == "NoDirectory"){ 
		$err_msgs[]="File upload failed - The permanent storage directory does not exist or is not accessible";
	} 
	else if ($status == "PrepareFail"){
		$err_msgs[]="upload failed - Error getting ready to insert data into the database";
	}
	else if ($status == "ExecuteFail"){
		$err_msgs[]="File upload failed - Error insertng data into the database(Please, check if the path name is unigue)";
	}
	else if ($status == "DBConnectionFail"){
		$err_msgs[]="File upload failed - Error connecting to the database";
	}
	
	return $err_msgs;
}

?>
