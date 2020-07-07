<!-- 
1.purpose: the user is to upload path data file and import its contents into the database.
The form allows a user to select a CSV file containing the data about a single microwave path to upload to the web server and then import the contents of the file into the database.
2. authors: Group2
-->
<?php
session_start(); 

require_once("../includes/db_connection.php");
require_once("../includes/displayStatus.php");
require_once("../includes/displayErrors.php");   

if ((isset($_POST['info_b_submit'])&&$_POST['info_b_submit']=="Submit") && $_SERVER['REQUEST_METHOD'] == "POST") {

	$err_msgs = validateFormData();

	if (count($err_msgs) >0){
		displayErrors($err_msgs);
		displayForm();
	} else {
		$status = processUploads();
		if($status!="OK"){
			$err_msgs = displayStatus($status);
			displayErrors($err_msgs);
		}
	}
} else {
	displayForm();
}


function displayForm(){
?>
<html>
	<head>
		<meta charset="utf-8">
		<title>Path Data File Upload</title>
		<link href="../css/style.css" type="text/css" rel="stylesheet"/>
		<script src="../js/jquery-3.2.1.js" type="text/javascript"></script>
	</head>
	<body>
	<h2>Path Data File Upload</h2>
	<form method="POST" enctype="multipart/form-data">
		<div>
			<div class="box">
			<span style="font-weight:bold">[Note] Please, choose a CSV file containing the data about a single microwave path to upload to the web server.</span><br><br><br>
			<label for="fileUpload">1. Path data file: </label>
			<input type="hidden" name="MAX_FILE_SIZE" value="2000000"/>
			<input type="file" id="fileUpload" name="uploads" />
			</div>
		</div>
		<br />
		<input type="submit" name="info_b_submit"  value="Submit"/>
	</form>
	<br />
	<a href="../index.php">Return to menu</a>
	</body>
</html>
<?php
}

function validateFormData():array {
	$err_msgs = array();
	$allowed_exts = array("csv");
	$allowed_types = array("text/csv");

	if (isset($_FILES['uploads']) && !empty($_FILES['uploads']['name'])){
		$up =$_FILES['uploads'];
		if ($up['error'] == 0){
			if ($up['size'] == 0){
				$err_msgs[] = "An empty file was uploaded";
			}
			$ext = strtolower(pathinfo($up['name'], PATHINFO_EXTENSION));
			if (!in_array($ext, $allowed_exts)){
				$err_msgs[] = "File extension does not indicate that the uploaded file is of an allowed file type";
			}
			if (!in_array($up['type'], $allowed_types)){
				$err_msgs[] = "The uploaded file's MIME type is not allowed";
			}
			if (!file_exists($up['tmp_name'])){
				$err_msgs[] = "No files exists on the server for this upload";
			}
		
		} else {
			$err_msgs[] = "An error occured during file upload";
		}
	} else {
		$err_msgs[] = "No file was uploaded";
	}

	return $err_msgs;
}

function validateFieldData($csv){
	$err_msgs = array();
	$row = 1;
	$data = array();
	$pathName="";
	if (($handle = fopen($csv, "r")) !== FALSE) {
		$db_conn = connectDB();
		$last_id=0;
		if (!$db_conn){
			$status = "DBConnectionFail";
			$err_msgs = displayStatus($status);
			displayErrors($err_msgs);
		} else {
		
			try{
				$db_conn->setAttribute(PDO::ATTR_AUTOCOMMIT,0);
				$db_conn->beginTransaction();
				$error=false;
				
				while (($data = fgetcsv($handle, 0,",")) !== FALSE) {
					$num = count($data);
					$quory="";
					$lineData= array();
					$err_msgs = array();
					$error=false;

					for ($c=0; $c < $num; $c++) {
						
						$fieldValue=trim($data[$c]);
									
						if($row==1 && $c<4){
							
							if(isset($fieldValue) && (!empty($fieldValue) || is_numeric($fieldValue) || $c ==3 ) ){
								if($c==0){
									if(strlen($fieldValue)> 100){
										$err_msgs[]="The length of the field #".($c+1)." in line #$row should be shorter than 100 characters";
									}
									if(gettype($fieldValue)!="string"){
										$err_msgs[]="The data type of the field #".($c+1)." in line #$row is not string";
									}
									
									$pathName=$fieldValue;
								}
								else if($c==1){
									if(!is_numeric($fieldValue)){
										$err_msgs[]="The data type of the field #".($c+1)." in line #$row should be numeric";
									}
									else if($fieldValue<1.0 || $fieldValue>100.0){
										$err_msgs[]="The value of the field #".($c+1)." in line #$row should be between 1.0 and 100.0 GHz";
									}
								}
								else if($c==2){
									if(strlen($fieldValue)> 255){
										$err_msgs[]="The length of the field #".($c+1)." in line #$row is longer than 255 characters";
									}						
								}
								else if($c==3){
									if(!empty($fieldValue)){
										if(strlen($fieldValue)> 65534){
											$err_msgs[]="The length of the field #".($c+1)." in line #$row should be shorter than 255 characters";
										}								
									}											
								}
							}
							else{
								$err_msgs[]="The field #".($c+1)." in line #$row is nonexistent or empty";
							}
							
							$lineData[] = $fieldValue;
							$quory="insert into path_info (pt_name, pt_frequency, pt_description, pt_note) values(?, ?, ?, ?)";
							
						}
						else if(($row==2 || $row==3) && $c<5 ) {
							
							if(isset($fieldValue) && (!empty($fieldValue)|| is_numeric($fieldValue)) ){
								if($c==0){
	
									if($row==2 && ($fieldValue!=0 || !is_numeric($fieldValue))){
										$err_msgs[]="The value of the field #".($c+1)." in line #$row should be 0";
									}
									if($row==3 && (!is_numeric($fieldValue))){
										$err_msgs[]="The value of the field #".($c+1)." in line #$row should be numeric";
									}
								}
								else if($c==1 || $c==2){
									
									if(!is_numeric($fieldValue)){
										$err_msgs[]="The value of the field #".($c+1)." in line #$row should be numeric";
									}
								}
								else if($c==3){
									$values=["LDF4-50A","LDF5-50A","LDF-6-50","LDF7-50A","LDF12-50"];
									if(!in_array($fieldValue,$values)){
										$err_msgs[]="The value of the field #".($c+1)." in line #$row is invalid";
									}												
								}
								else if($c==4){
									if(!is_numeric($fieldValue)){
										$err_msgs[]="The value of the field #".($c+1)." in line #$row should be numeric";
									}
								}
							}
							else{
								$err_msgs[]="The field #".($c+1)." in line #$row is nonexistent or empty";
							}
							
							if($c==0){
								$lineData[]=$last_id;
							}
		
							$lineData[] = $fieldValue;
							$quory="insert into path_end (ed_pt_id, ed_distance, ed_ground_height, ed_antenna_height, ed_antenna_type, ed_antenna_length) values(?, ?, ?, ?, ?, ?)";
						}
						else if($row>3 && $c<5){
							if(isset($fieldValue) && (!empty($fieldValue)|| is_numeric($fieldValue))){
								if($c==0 || $c==1){
									if(!is_numeric($fieldValue)){
										$err_msgs[]="The value of the field #".($c+1)." in line #$row should be numeric";
									}
								}
								else if($c==2){
									$temp=strtolower($fieldValue);
									$values=["grassland","rough grassland","smooth rock","bare rock","bare earth","paved surface","lake","ocean"];
									if(strlen($fieldValue)> 50){
										$err_msgs[]="The length of the field #".($c+1)." in line #$row should be shorter than 50 characters";
									}

									if(!in_array($temp,$values)){
										$err_msgs[]="The value of the field #".($c+1)." in line #$row is invalid";
									}
								}
								else if($c==3){
									if(!is_numeric($fieldValue)){
										$err_msgs[]="The value of the field #".($c+1)." in line #$row should be numeric";
									}
									else if($fieldValue<0){
										$err_msgs[]="The value of the field #".($c+1)." in line #$row is invalid";
									}
								}
								else if($c==4){
									$temp=strtolower($fieldValue);
									$values=["none","trees","brush","buildings","webbed towers","solid towers","power cables"];
									if(strlen($fieldValue)> 50){
										$err_msgs[]="The length of the field #".($c+1)." in line #$row should be shorter than 50 characters";
									}
									if(!in_array($temp,$values)){
										$err_msgs[]="The value of the field #".($c+1)." in line #$row is invalid";
									}
									if($temp=="none" && ($data[3]!=0 && is_numeric($data[3]))){
										$err_msgs[]="The value of the field #4 in line #$row is invalid. If there is no obstruction, the value should be 0";
									}
									if($temp!="none" && ($data[3]==0 && is_numeric($data[3]))){
										$err_msgs[]="The value of the field #4 in line #$row is invalid. If there is the obstruction, the value should not be 0";
									}
								}
							}
							else{
								$err_msgs[]="The field #".($c+1)." in line #$row is nonexistent or empty";
							}	
							
							if($c==0){
								$lineData[]=$last_id;
							}
							
							$lineData[] = $fieldValue;
							$quory="insert into path_mid (md_pt_id, md_distance, md_ground_height, md_terrain_type, md_obstruction_height, md_obstruction_type) values(?, ?, ?, ?, ?, ?)";
						}	
					} 
				
					if (count($err_msgs) >0){
						displayErrors($err_msgs);
						displayForm();
						$error=true;
						break;
					}
					else{
						$stmt = $db_conn->prepare($quory);
					
						if (!$stmt){						
							$status = "PrepareFail";							
							$err_msgs = displayStatus($status);
							displayErrors($err_msgs);
							displayForm();
							$error=true;
							break;
						}  
								
						$result = $stmt->execute($lineData);
						
						if($row==1){
							$last_id = $db_conn->lastInsertId();
						}
						
						if(!$result){				
							$status = "ExecuteFail";
							$err_msgs = displayStatus($status);
							displayErrors($err_msgs);
							displayForm();
							$error=true;
							break;
						} 
					}

					$row++;
				}
				
				if($error){
				//	echo "rollback";
					$db_conn->rollback();
					$db_conn = NULL;
					unlink($csv);
				}
				else{
				//	echo "commit";
					$db_conn->commit();
					displayForm();

				}

			}
			catch(PDOExecption $e) {

			}
		}
			
		$db_conn = NULL;
		fclose($handle);
		if(!$error){
			rename($csv,"./uploads/PathData_id_".$last_id.".csv");
			echo '<script>alert("The file has been verified and uploaded successfully!");</script>';
		}

	}

}

function processUploads(): string{
	$status = "OK";

	if(!is_dir('./uploads') || !is_writable("./uploads")){ 
		$status = "NoDirectory";
	} else {
		$ext = strtolower(pathinfo($_FILES['uploads']['name'], PATHINFO_EXTENSION));
		$fn = $_FILES['uploads']['name'];
		$newName = "./uploads/pathData_$fn"."_".rand(10000, 99999).".". $ext;
		$success = move_uploaded_file($_FILES['uploads']['tmp_name'], $newName);
		if (!$success){
			$status = "FailMove";
		} else {
			validateFieldData($newName);
		}
	}
	
	return $status;
}

?>
