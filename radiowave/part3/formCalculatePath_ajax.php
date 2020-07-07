<?php

/*
1.purpose: this file is to list up the path data for selecting one path from all paths stored in the database
and to perform the calculation for the selected path data. 
2. authors: Group2
*/

//session_start(); //<----Using AJAX:adding session_start();
header("Content-Type: application/json");
require_once("../includes/db_connection.php");  
$DataList = array();

if(isset($_POST['list_select']) && !empty($_POST['list_select']) && $_POST['factors']!=""){
	
     $pt_id=$_POST['list_select']; 
     $factors=$_POST['factors'];
	getPathData($pt_id,$factors);
}


function getPathData($pt_id,$factors,$msg=""){
    
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

    if($DataList['status']=="OK"){

        $Fghz=$DataList['pathInfo'][0]["pt_frequency"];
        $D=$DataList['endInfo'][1]["ed_distance"];
        $PA=92.4+(20*log10($Fghz))+(20*log10($D)); //$D=1.026;
        $DataList['calculate']["pathAttenuation"]=$PA;
        $DataList['curvature']=$factors;
        $factor=0;

        if($factors=="4/3"){
            $factor=17;
        }
        else if($factors=="1"){
            $factor=12.75;
        }
        else if($factors=="2/3"){
            $factor=8.5;
        }
        else if($factors=="infinity"){
            $factor=0;
        }

        $startAntenna = $DataList['endInfo'][0]["ed_ground_height"] + $DataList['endInfo'][0]["ed_antenna_height"];
        $endAntenna = $DataList['endInfo'][1]["ed_ground_height"] + $DataList['endInfo'][1]["ed_antenna_height"];
    //    $divAntenna = round(($endAntenna - $startAntenna) / (count($DataList['midInfo'])),1);
        $divAntenna = ($endAntenna - $startAntenna) / (count($DataList['midInfo'])+1);
        $DataList['calculate']["antenna"][]=$startAntenna;
        for($a=0; $a < count($DataList['midInfo']);$a++ ){
            $DataList['calculate']["antenna"][]=$DataList['calculate']["antenna"][$a] + $divAntenna;
        }

        $DataList['calculate']["antenna"][]=$endAntenna;
        $DataList['calculate']["apparentGroundHeight"][]=$DataList['endInfo'][0]["ed_ground_height"];
        $DataList['calculate']["totalApparentHeight"][]=$DataList['endInfo'][0]["ed_ground_height"];
        $DataList['label'][]=0;
        
        for($i=0;$i<count($DataList['midInfo']);$i++){
            $d1=$DataList['midInfo'][$i]["md_distance"];
            $d2=$D-$d1;
            $F1=17.3*sqrt($d1*$d2/($Fghz*$D));
            $DataList['calculate']["firstFreznelZone"][]=$F1;        
            if($factor==0){
                $h=0;
            }
            else{
                $h=$d1*$d2/$factor;
            }
            $DataList['calculate']["curvatureHeight"][]=$h;
            $apparentGroundHeight=$DataList['midInfo'][$i]["md_ground_height"]+$DataList['midInfo'][$i]["md_obstruction_height"]+$h;
            $DataList['calculate']["apparentGroundHeight"][]=$apparentGroundHeight;
            $totalApparentHeight=$apparentGroundHeight+$F1;
            $DataList['calculate']["totalApparentHeight"][]=$totalApparentHeight;
            $DataList['label'][]=$d1;
        }
        
        $DataList['calculate']["apparentGroundHeight"][]=$DataList['endInfo'][1]["ed_ground_height"];
        $DataList['calculate']["totalApparentHeight"][]=$DataList['endInfo'][1]["ed_ground_height"];
        $DataList['label'][]=$DataList['endInfo'][1]["ed_distance"];
        
    }

    /*===================End of EndPath Data====================*/ 
        $DataList['msg'] = $msg;
        $newData = json_encode($DataList);
        echo $newData;
}


?>

