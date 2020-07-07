<?php

/*1.purpose: this file is to list up the path data for selecting one path from all paths stored in the database, and
and it verifies the edited path data and updates the database. 
If validation succeeds, the database record for the edited path data is to be updated and a success message sent to the browser, 
otherwise error messages are to be sent to the browser.
2. authors: Group2*/


//session_start(); //<----Using AJAX:adding session_start();
header("Content-Type: application/json");
require_once("../includes/db_connection.php");  
$DataList = array();

if(isset($_POST['list_select']) && !empty($_POST['list_select'])){
	

	 $pt_id=$_POST['list_select']; 
	getPathData($pt_id);
}

if(isset($_POST['action']) && isset($_POST['ptId'])){
    

    /*
       ============================================================
       Start of Path Info Update
       ============================================================
    */
    if(!empty($_POST['ptId']) && $_POST['action']=="update"){
        
        
        $errMsg=0;
        $ptFrequency=trim($_POST['ptFrequency']);
        $ptDescription=trim($_POST['ptDescription']);
        $ptNote=trim($_POST['ptNote']);
        $ptid=$_POST['ptId'];

        if(!isset($_POST['ptFrequency']) || (empty($ptFrequency) && !is_numeric($ptFrequency))){
           $DataList['msg'][]= "Frequency field is empty";
            $errMsg=1;
        }
        else{
            if(!is_numeric($ptFrequency)){
                $DataList['msg'][]="The Frequency should be numeric";
                $errMsg=1;
            }
            else if($ptFrequency<1.0 || $ptFrequency>100.0){
                $DataList['msg'][]="The value of the Frequency should be between 1.0 and 100.0 GHz";
                $errMsg=1;
            }
        }
        
        if(!isset($_POST['ptDescription']) || (empty($ptDescription) && !is_numeric($ptDescription))){
            $DataList['msg'][]= "Description field is empty";
            $errMsg=1;
        }
        else{
            if(strlen($ptDescription)> 255){
                $DataList['msg'][]="The length of the Description should be shorter than 255 characters";
                $errMsg=1;
            }	
        }
        
        if(!isset($_POST['ptNote']) || (empty($ptNote) && !is_numeric($ptNote))){

        }
        else{
            if(strlen($ptNote)> 65534){
                $DataList['msg'][]="The length of the Note should be shorter than 65534 characters";
                $errMsg=1;
            }	
        }

        if($errMsg == 0){
            
            /*
                ============================================================
                Database PDO function for updating database field start
                ============================================================
            */
            $qry = "UPDATE path_info SET pt_frequency=?, pt_description=?, pt_note=? WHERE pt_id=?"; 
            $data = [$ptFrequency,$ptDescription,$ptNote,$_POST['ptId']];

            $db_conn =connectDB();
            $stmt = $db_conn->prepare($qry);
            if (!$stmt){
                echo "<p>Error in view prepare: ".$db_conn->errorCode()."</p>\n<p>Message ".implode($db_conn->errorInfo())."</p>\n";
                exit(1);
            }
            
            $status = $stmt->execute($data);
            if ($status){
                $DataList['status'] = "OK";
                $DataList['msg'] ="Field updated successfully";
                $newData = json_encode($DataList);
                echo $newData;             
            }
            else{
                echo "<p>Error in view execute: ".$db_conn->errorCode()."</p>\n<p>Message ".implode($db_conn->errorInfo())."</p>\n";
                exit(1);
            }
            
            /*
                ============================================================
                Database PDO function for updating database field end
                ============================================================
            */
            
        }else {
            $DataList['status'] = "ERROR";
           
            $newData = json_encode($DataList);
            echo $newData;
        }
        
         
        
    }else {
        $DataList['status'] = "ERROR";
        $DataList['msg'][] = "Invalid action ";
        $newData = json_encode($DataList);
        echo $newData;
    }
    /*
       ============================================================
       End of Path Info Update
       ============================================================
    */

    
}else if(isset($_POST['action']) && isset($_POST['mdId'])){
    /*
       ============================================================
       Start of Mid Info Update
       ============================================================
    */
    if(!empty($_POST['mdId']) && $_POST['action']=="update"){
        
        
        $errMsg=0;
        $mdGroundHeight=trim($_POST['mdGroundHeight']);
        $mdTerrainType=trim($_POST['mdTerrainType']);
        $mdObstructionHeight=trim($_POST['mdObstructionHeight']);
        $mdObstructionType=trim($_POST['mdObstructionType']);

        if(!isset($_POST['mdGroundHeight']) || (empty($mdGroundHeight) && !is_numeric($mdGroundHeight))){
            $DataList['msg'][]= "Ground Height field is empty";
            $errMsg=1;
        }
        else{
            if(!is_numeric($mdGroundHeight)){
                $DataList['msg'][]="The Ground Height should be numeric";
                $errMsg=1;
            }
        }
        
        if(!isset($_POST['mdTerrainType']) || (empty($mdTerrainType) && !is_numeric($mdTerrainType))){
            $DataList['msg'][]= "Terrain Type field is empty";
            $errMsg=1;
        }
        else{
            $temp=strtolower($mdTerrainType);
            $values=["grassland","rough grassland","smooth rock","bare rock","bare earth","paved surface","lake","ocean"];
            if(strlen($mdTerrainType)> 50){
                $DataList['msg'][]="The length of the Terrain Type should be shorter than 50 characters";
                $errMsg=1;
            }

            if(!in_array($temp,$values)){
                $DataList['msg'][]="The value of the Terrain Type is invalid";
                $errMsg=1;
            }
        }
        
        if(!isset($_POST['mdObstructionHeight']) || (empty($mdObstructionHeight) && !is_numeric($mdObstructionHeight))){
            $DataList['msg'][]= "Obstruction Height field is empty";
            $errMsg=1;
        }
        else{
            if(!is_numeric($mdObstructionHeight)){
                $DataList['msg'][]="The value of the Obstruction Height should be numeric";
                $errMsg=1;
            }
            else if($mdObstructionHeight<0){
                $DataList['msg'][]="The value of the Obstruction Height is invalid";
                $errMsg=1;
            }
        }
        
        if(!isset($_POST['mdObstructionType']) || (empty($mdObstructionType) && !is_numeric($mdObstructionType))){
            $DataList['msg'][]= "Obstruction Type field is empty";
            $errMsg=1;
        }
        else{
            $temp=strtolower($mdObstructionType);
            $values=["none","trees","brush","buildings","webbed towers","solid towers","power cables"];
            if(strlen($mdObstructionType)> 50){
                $DataList['msg'][]="The length of the Obstruction Type should be shorter than 50 characters";
                $errMsg=1;
            }
            if(!in_array($temp,$values)){
                $DataList['msg'][]="The value of the Obstruction Type is invalid";
                $errMsg=1;
            }
            if($temp=="none" && ($mdObstructionHeight!=0 && is_numeric($mdObstructionHeight))){
                $DataList['msg'][]="The value of the Obstruction Height is invalid. If there is no obstruction, the value should be 0";
                $errMsg=1;
            }
            if($temp!="none" && ($mdObstructionHeight==0 && is_numeric($mdObstructionHeight))){
                $DataList['msg'][]="The value of the Obstruction Height is invalid. If there is the obstruction, the value should not be 0";
                $errMsg=1;
            }
        }
        

        if($errMsg == 0){
            
            /*
                ============================================================
                Database PDO function for updating database field start
                ============================================================
            */
            
            $data=[$mdGroundHeight,$mdTerrainType,$mdObstructionHeight,$mdObstructionType,$_POST['mdId']];
            $qry = "UPDATE path_mid SET md_ground_height=?, md_terrain_type=?, md_obstruction_height=?, md_obstruction_type=? WHERE md_id=?";   
            
            $db_conn =connectDB();
            $stmt = $db_conn->prepare($qry);
            if (!$stmt){
                echo "<p>Error in view prepare: ".$db_conn->errorCode()."</p>\n<p>Message ".implode($db_conn->errorInfo())."</p>\n";
                exit(1);
            }
            
            $status = $stmt->execute($data);
            if ($status){
               
                
                $DataList['status'] = "OK";
                $DataList['msg'] ="Field updated successfully";
                $newData = json_encode($DataList);
                echo $newData;             
            }
            else{
                echo "<p>Error in view execute: ".$db_conn->errorCode()."</p>\n<p>Message ".implode($db_conn->errorInfo())."</p>\n";
                exit(1);
            }
                
            
            /*
                ============================================================
                Database PDO function for updating database field end
                ============================================================
            */
           
        }else {
            $DataList['status'] = "ERROR";
           
            $newData = json_encode($DataList);
            echo $newData;
        }
        
         
        
    }else {
        $DataList['status'] = "ERROR";
        $DataList['msg'][] = "Invalid action ";
        $newData = json_encode($DataList);
        echo $newData;
    }
    /*
       ============================================================
       End of Mid Info Update
       ============================================================
    */
}else if(isset($_POST['action']) && isset($_POST['edId'])){
    
     /*
       ============================================================
       Start of End Info Update
       ============================================================
    */
    if(!empty($_POST['edId']) && $_POST['action']=="update"){
        
        
        $errMsg=0;
        
        $edGroundHeight=trim($_POST['edGroundHeight']);
        $edAntennaHeight=trim($_POST['edAntennaHeight']);
        $edAntennaCableType=trim($_POST['edAntennaCableType']);
        $edAntennaCableLength=trim($_POST['edAntennaCableLength']);

        if(!isset($_POST['edGroundHeight']) || (empty($edGroundHeight) && !is_numeric($edGroundHeight))){
           $DataList['msg'][]= "Ground Height field is empty";
            $errMsg=1;
        }
        else{
            if(!is_numeric($edGroundHeight)){
                $DataList['msg'][]="The value of the Ground Height should be numeric";
                $errMsg=1;
            }
        }
        
        if(!isset($_POST['edAntennaHeight']) || (empty($edAntennaHeight) && !is_numeric($edAntennaHeight))){
            $DataList['msg'][]= "Antenna Height field is empty";
            $errMsg=1;
        }
        else{
            if(!is_numeric($edAntennaHeight)){
                $DataList['msg'][]="The value of the Antenna Height should be numeric";
                $errMsg=1;
            }
        }
        
        if(!isset($_POST['edAntennaCableType']) || (empty($edAntennaCableType) && !is_numeric($edAntennaCableType))){
            $DataList['msg'][]= "Antenna Cable Type field is empty";
            $errMsg=1;
        }
        else{
            $values=["LDF4-50A","LDF5-50A","LDF-6-50","LDF7-50A","LDF12-50"];
            if(!in_array($edAntennaCableType,$values)){
                $DataList['msg'][]="The value of the Antenna Cable Type is invalid";
                $errMsg=1;
            }	
        }
        
        if(!isset($_POST['edAntennaCableLength']) || (empty($edAntennaCableLength) && !is_numeric($edAntennaCableLength))){
            $DataList['msg'][]= "Antenna Cable Length field is empty";
            $errMsg=1;
        }
        else{
            if(!is_numeric($edAntennaCableLength)){
                $DataList['msg'][]="The value of the Antenna Cable Length should be numeric";
                $errMsg=1;
            }
        }
     
     
        if($errMsg ==0){
            
            /*
                ============================================================
                Database PDO function for updating database field start
                ============================================================
            */

            $qry = "UPDATE path_end SET ed_ground_height=?, ed_antenna_height=?, ed_antenna_type=?, ed_antenna_length=? WHERE ed_id=?";   
            $data=[$edGroundHeight,$edAntennaHeight,$edAntennaCableType,$edAntennaCableLength,$_POST['edId']];
            
            $db_conn =connectDB();
            $stmt = $db_conn->prepare($qry);
            if (!$stmt){
                echo "<p>Error in view prepare: ".$db_conn->errorCode()."</p>\n<p>Message ".implode($db_conn->errorInfo())."</p>\n";
                exit(1);
            }
            
            $status = $stmt->execute($data);
            if ($status){
                $DataList['status'] = "OK";
                $DataList['msg'] ="Field updated successfully";
                $newData = json_encode($DataList);
                echo $newData;             
                
                
            }
            else{
                echo "<p>Error in view execute: ".$db_conn->errorCode()."</p>\n<p>Message ".implode($db_conn->errorInfo())."</p>\n";
                exit(1);
            }
            /*
                ============================================================
                Database PDO function for updating database field end
                ============================================================
            */
           
        }else {
            $DataList['status'] = "ERROR";
           
            $newData = json_encode($DataList);
            echo $newData;
        }
        
         
        
    }else {
        $DataList['status'] = "ERROR";
        $DataList['msg'][] = "Invalid action ";
        $newData = json_encode($DataList);
        echo $newData;
    }
    /*
       ============================================================
       End of End Info Update
       ============================================================
    */ 
}

function getPathData($pt_id,$msg=""){
    
    $db_conn =connectDB();
    

	/*===================Start of InfoPath Data====================*/ 
    
	$qry = "select pt_id, pt_name,pt_frequency,pt_description,pt_note from path_info    
			WHERE pt_id=?";
    $stmt = $db_conn->prepare($qry);
	if (!$stmt){
		echo "<p>Error in view prepare: ".$db_conn->errorCode()."</p>\n<p>Message ".implode($db_conn->errorInfo())."</p>\n";
		exit(1);
	}
	
	$status = $stmt->execute($pt_id);
	if ($status){
		if ($stmt->rowCount() > 0){
            
		    $DataList['status'] = "OK";
			
			while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
				$DataList['pathInfo'][] = $row;	
			}
			
        }
		
	}
    
    /*===================End of InfoPath Data====================*/ 
    
    /*===================Start of Mid Data====================*/
    
    $qry2 = "select md_id, md_distance,md_ground_height,md_terrain_type,md_obstruction_height,md_obstruction_type from path_mid    
			WHERE md_pt_id=?";
    $stmt2 = $db_conn->prepare($qry2);
	if (!$stmt2){
		echo "<p>Error in view prepare: ".$db_conn->errorCode()."</p>\n<p>Message ".implode($db_conn->errorInfo())."</p>\n";
		exit(1);
	}
	
	$status2 = $stmt2->execute($pt_id);
	if ($status2){
		if ($stmt2->rowCount() > 0){
			
			while($row2=$stmt2->fetch(PDO::FETCH_ASSOC)){
				$DataList['midInfo'][] = $row2;	
			}
			
        }
		
	}
    
    /*===================End of MidPath Data====================*/ 
    
    /*===================Start of EndPath Data====================*/ 
    
    
    $qry3 = "select ed_id, ed_distance,ed_ground_height,ed_antenna_height,ed_antenna_type,ed_antenna_length from path_end    
			WHERE ed_pt_id=?";
    $stmt3 = $db_conn->prepare($qry3);
	if (!$stmt3){
		echo "<p>Error in view prepare: ".$db_conn->errorCode()."</p>\n<p>Message ".implode($db_conn->errorInfo())."</p>\n";
		exit(1);
	}
	
	$status3 = $stmt3->execute($pt_id);
	if ($status3){
		if ($stmt3->rowCount() > 0){
			
			while($row3=$stmt3->fetch(PDO::FETCH_ASSOC)){
				$DataList['endInfo'][] = $row3;	
			}
			
        }
		
	}
    /*===================End of EndPath Data====================*/ 
        $DataList['msg'] = $msg;
        $newData = json_encode($DataList);
        echo $newData;
}


?>

