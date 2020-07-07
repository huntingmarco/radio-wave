<!-- 
1.purpose: This form performs the reset operation using the database and validates the data from the file 
and check for errors, and then this form returns the result to display.
2. authors: Group2
-->
<?php 
//session_start(); 

require_once("../includes/db_connection.php");

$err_msgs='"errors';
$posts = array();
$posts['errorMsg'] = array();
$resultData="";
if (!empty($_POST['list_select']) && isset($_POST['list_select'])){
	$pt_id=$_POST['list_select'][0];
	$pathFile="./uploads/PathData_id_".$pt_id.".csv";

	validateResetData($pt_id,$pathFile);
}
else{
	$err_msgs.='"'."Please, choose a path data first";
	$posts['errorMsg']=$err_msgs;
	echo $err_msgs;
}

function validateResetData($id, $csv){
	$err_msgs='"errors';
	$row = 1;
	$data = array();
	$error=false;
	if (file_exists($csv) &&(($handle = fopen($csv, "r")) !== FALSE)) {
		$db_conn = connectDB();

		if (!$db_conn){
			$err_msgs.='"'."Reset failed - Error connecting to the database";
			$error=true;

		} else {
		
			try{
				$db_conn->setAttribute(PDO::ATTR_AUTOCOMMIT,0);
				$db_conn->beginTransaction();

				$posts["pathId"]  = $id;
				
				$resultData='"pathInfo"'.$id;
				$error=false;
				$quory="DELETE FROM path_end where ed_pt_id = ?";
				$stmt = $db_conn->prepare($quory);
				
				if (!$stmt){						
					$err_msgs.='"'. "Reset failed - Error getting ready to insert data into the database";							
					$error=true;
				}  
				else{
					
					$result = $stmt->execute([$id]);

					if(!$result){				
						$err_msgs.='"'. "Reset failed - Error deleting data into the database";
						$error=true;
					}
					else{
						
						$quory="DELETE FROM path_mid where md_pt_id = ?";
						$stmt = $db_conn->prepare($quory);
						
						if (!$stmt){						
							$err_msgs.='"'. "Reset failed - Error getting ready to delete data in the database";							
							$error=true;
						}  
						else{
						
							$result = $stmt->execute([$id]);

							if(!$result){				
								$err_msgs.='"'. "Reset failed - Error deleting data in the database";
								$error=true;
							}
							else{
								
								while (($data = fgetcsv($handle, 0,",",'"')) !== FALSE) {
									$num = count($data);
									$quory="";
									$lineData= array();
									$err_msgs = '"errors';
									$error=false;

									for ($c=0; $c < $num; $c++) {
										
										$fieldValue=trim($data[$c]);
													
										if($row==1 && $c<4){
											
											if(isset($fieldValue) && (!empty($fieldValue) || is_numeric($fieldValue) || $c ==3 ) ){
												if($c==0){
													if(strlen($fieldValue)> 100){
														$err_msgs.='"'."The length of the field #".($c+1)." in line #$row should be shorter than 100 characters";
														$error=true;
													}
													if(gettype($fieldValue)!="string"){
														$err_msgs.='"'."The data type of the field #".($c+1)." in line #$row is not string";
														$error=true;
													}
													$resultData.='"'.$fieldValue;
													$posts["pathName"]  = $fieldValue;
												}
												else if($c==1){
													if(!is_numeric($fieldValue)){
														$err_msgs.='"'."The data type of the field #".($c+1)." in line #$row should be numeric";
														$error=true;
													}
													else if($fieldValue<1.0 || $fieldValue>100.0){
														$err_msgs.='"'."The value of the field #".($c+1)." in line #$row should be between 1.0 and 100.0 GHz";
														$error=true;
													}
													$resultData.='"'.$fieldValue;
													$posts["frequency"]  = $fieldValue;
													
												}
												else if($c==2){
													if(strlen($fieldValue)> 255){
														$err_msgs.='"'."The length of the field #".($c+1)." in line #$row is longer than 255 characters";
														$error=true;
													}
													$resultData.='"'.$fieldValue;
													$posts["description"]  = $fieldValue;
												
												}
												else if($c==3){
													if(!empty($fieldValue)){
														if(strlen($fieldValue)> 65534){
															$err_msgs.='"'."The length of the field #".($c+1)." in line #$row should be shorter than 255 characters";
															$error=true;
														}								
													}
													$resultData.='"'.$fieldValue;
													$posts["note"] = $fieldValue;
												
												}
											}
											else{
												$error=true;
												$err_msgs.='"'."The field #".($c+1)." in line #$row is nonexistent or empty";
											}
											
											$lineData[] = $fieldValue;
											$quory="update path_info set pt_name=?, pt_frequency=?, pt_description=?, pt_note=? where pt_id = ?";
											
										}
										else if(($row==2 || $row==3) && $c<5 ) {
											if(isset($fieldValue) && (!empty($fieldValue)|| is_numeric($fieldValue)) ){
												if($c==0){
													if($row==2 && ($fieldValue!=0 || !is_numeric($fieldValue))){
														$err_msgs.='"'."The value of the field #".($c+1)." in line #$row should be 0";
														$error=true;
													}
													if($row==3 && (!is_numeric($fieldValue))){
														$err_msgs.='"'."The value of the field #".($c+1)." in line #$row should be numeric";
														$error=true;
													}
												}
												else if($c==1 || $c==2){
													
													if(!is_numeric($fieldValue)){
														$err_msgs.='"'."The value of the field #".($c+1)." in line #$row should be numeric";
														$error=true;
													}
												}
												else if($c==3){
													$values=["LDF4-50A","LDF5-50A","LDF-6-50","LDF7-50A","LDF12-50"];
													if(!in_array($fieldValue,$values)){
														$err_msgs.='"'."The value of the field #".($c+1)." in line #$row is invalid";
														$error=true;
													}												
												}
												else if($c==4){
													if(!is_numeric($fieldValue)){
														$err_msgs.='"'."The value of the field #".($c+1)." in line #$row should be numeric";
														$error=true;
													}
												}
											}
											else{
												$err_msgs.='"'."The field #".($c+1)." in line #$row is nonexistent or empty";
												$error=true;
											}
											
											if($c==0){
												$lineData[]=$id;
											}
						
											$lineData[] = $fieldValue;
											$quory="insert into path_end (ed_pt_id, ed_distance, ed_ground_height, ed_antenna_height, ed_antenna_type, ed_antenna_length) values(?, ?, ?, ?, ?, ?)";
										}
										else if($row>3 && $c<5){
											if(isset($fieldValue) && (!empty($fieldValue)|| is_numeric($fieldValue))){
												if($c==0 || $c==1){
													if(!is_numeric($fieldValue)){
														$err_msgs.='"'."The value of the field #".($c+1)." in line #$row should be numeric";
														$error=true;
													}
												}
												else if($c==2){
													$temp=strtolower($fieldValue);
													$values=["grassland","rough grassland","smooth rock","bare rock","bare earth","paved surface","lake","ocean"];
													if(strlen($fieldValue)> 50){
														$err_msgs.='"'."The length of the field #".($c+1)." in line #$row should be shorter than 50 characters";
														$error=true;
													}

													if(!in_array($temp,$values)){
														$err_msgs.='"'."The value of the field #".($c+1)." in line #$row is invalid";
														$error=true;
													}
												}
												else if($c==3){
													if(!is_numeric($fieldValue)){
														$err_msgs.='"'."The value of the field #".($c+1)." in line #$row should be numeric";
														$error=true;
													}
													else if($fieldValue<0){
														$err_msgs.='"'."The value of the field #".($c+1)." in line #$row is invalid";
														$error=true;
													}
												}
												else if($c==4){
													$temp=strtolower($fieldValue);
													$values=["none","trees","brush","buildings","webbed towers","solid towers","power cables"];
													if(strlen($fieldValue)> 50){
														$err_msgs.='"'."The length of the field #".($c+1)." in line #$row should be shorter than 50 characters";
														$error=true;
													}
													if(!in_array($temp,$values)){
														$err_msgs.='"'."The value of the field #".($c+1)." in line #$row is invalid";
														$error=true;
													}
													if($temp=="none" && ($data[3]!=0 && is_numeric($data[3]))){
														$err_msgs.='"'."The value of the field #4 in line #$row is invalid. If there is no obstruction, the value should be 0";
														$error=true;
													}
													if($temp!="none" && ($data[3]==0 && is_numeric($data[3]))){
														$err_msgs.='"'."The value of the field #4 in line #$row is invalid. If there is the obstruction, the value should not be 0";
														$error=true;
													}
												}
											}
											else{
												$err_msgs.='"'."The field #".($c+1)." in line #$row is nonexistent or empty";
												$error=true;
											}	
											
											if($c==0){
												$lineData[]=$id;
											}
											$lineData[] = $fieldValue;
											$quory="insert into path_mid (md_pt_id, md_distance, md_ground_height, md_terrain_type, md_obstruction_height, md_obstruction_type) values(?, ?, ?, ?, ?, ?)";
										}	
									} 
								
									if ($error){
										break;
									}
									else{
										$stmt = $db_conn->prepare($quory);
									
										if (!$stmt){						
											$err_msgs.='"'. "Reset failed - Error getting ready to reset data into the database";							
											$error=true;
											break;
										}  
										
										if($row==1){
											$lineData[]=$id;
										}
										
										$result = $stmt->execute($lineData);
																	
										if(!$result){				
											$err_msgs.='"'. "Reset failed - Error reseting data into the database(Please, check if the path name is unigue)";
											$error=true;
											break;
										} 
									}

									$row++;
								}
								
							}
		
						if($error){
						//	echo "rollback";
							$db_conn->rollback();
							$db_conn = NULL;
						}
						else{
						//	echo "commit";
							$db_conn->commit();
						}
						
					}
				}

			}
			}
			catch(PDOExecption $e) {

			}

			$posts['errorMsg']=$err_msgs;

			if($error){
			$resultData="";
			$resultData=$err_msgs;
			}
			echo $resultData;
			$db_conn = NULL;
			fclose($handle);
			
		}
	}
	else{
		$err_msgs.='"'."The original path file can not be open. Please, check out the file.";
		$resultData=$err_msgs;
		echo $resultData;
	}

}


?>
