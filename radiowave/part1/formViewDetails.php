<!-- 
1.purpose: This form performs to get the detail information about selected path using the database  
and returns the result tables to display.
2. authors: Group2
-->

<?php
//session_start(); //<----Using AJAX:adding session_start();
require_once("../includes/db_connection.php");  

if(isset($_POST['list_select']) && !empty($_POST['list_select'])){
	
		formViewDetails();
}

function formViewDetails(){
	view_path_info();
	view_path_mid();
	view_path_end();
}
function view_path_info(){
	$db_conn =connectDB();

	//Using AJAX:replace  $_POST['list_select']; with array($_GET['list_select']);
	$pt_id=$_POST['list_select']; 

	
	$qry = "select pt_name,pt_frequency,pt_description,pt_note from path_info    
			WHERE pt_id=?";
    $stmt = $db_conn->prepare($qry);
	if (!$stmt){
		echo "<p>Error in view prepare: ".$db_conn->errorCode()."</p>\n<p>Message ".implode($db_conn->errorInfo())."</p>\n";
		exit(1);
	}
	
	$status = $stmt->execute($pt_id);
	if ($status){
		if ($stmt->rowCount() > 0){

			?>
	<h2>Fanshawe College Info-5094 LAMP2 - Microwave Radio Path</h3>

	<h3>Path Information Data </h3>

	<table  border="1">
	  <?php


		$output = "<table style=\"table-layout:fixed; width:500px\"border=\"1\">\n";
		$output .= "<tr style=\"width=10px; text-align:center; vertical-align:middle; word-wrap:break-word\"><th>Path Name</th><th>Operating Frequency</th><th>Description</th><th>Note</th></tr>\n";
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
			$output .= "<tr><td>" .$row['pt_name']. "</td><td>"
				.$row['pt_frequency']."</td><td>"
				.$row['pt_description']."</td><td>"
				.$row['pt_note']."</td></tr>\n";
		}
		$output .= "</table>\n";
		echo $output;
		} else {
			echo "No posts available\n";
		}
	}

}

function view_path_mid(){
	$db_conn =connectDB();

	//Using AJAX:replace  $_POST['list_select']; with array($_GET['list_select']);
	$pt_id=$_POST['list_select'];
	
	$qry = "select md_distance,md_ground_height,md_terrain_type,md_obstruction_height,md_obstruction_type from path_mid    
			WHERE md_pt_id=?";
    $stmt = $db_conn->prepare($qry);
	if (!$stmt){
		echo "<p>Error in view prepare: ".$db_conn->errorCode()."</p>\n<p>Message ".implode($db_conn->errorInfo())."</p>\n";
		exit(1);
	}
	
	$status = $stmt->execute($pt_id);
	if ($status){
		if ($stmt->rowCount() > 0){
			
			?>
	<h3>Mid Point Information Data </h3>

	<table  border="1">
	  <?php

		$output = "<table style=\"table-layout:fixed; width:500px\"border=\"1\">\n";
		$output .= "<tr style=\"width=10px; text-align:center; vertical-align:middle; word-wrap:break-word\"><th>Distance Start To Mid Point</th><th>Ground Height</th><th>Terrain Type</th><th>Obstruction Height</th><th>Obstruction Type</th></tr>\n";
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
			$output .= "<tr><td>" .$row['md_distance']. "</td><td>"
				.$row['md_ground_height']."</td><td>"
				.$row['md_terrain_type']."</td><td>"
				.$row['md_obstruction_height']."</td><td>"
				.$row['md_obstruction_type']."</td></tr>\n";
		}
		$output .= "</table>\n";
		echo $output;
		} else {
			echo "No posts available\n";
		}
	}
}



function view_path_end(){
	$db_conn =connectDB();

	//Using AJAX:replace  $_POST['list_select']; with array($_GET['list_select']);
	$pt_id=$_POST['list_select']; 

	
	$qry = "select ed_distance,ed_ground_height,ed_antenna_height,ed_antenna_type,ed_antenna_length from path_end    
			WHERE ed_pt_id=?";
    $stmt = $db_conn->prepare($qry);
	if (!$stmt){
		echo "<p>Error in view prepare: ".$db_conn->errorCode()."</p>\n<p>Message ".implode($db_conn->errorInfo())."</p>\n";
		exit(1);
	}
	
	$status = $stmt->execute($pt_id);
	if ($status){
		if ($stmt->rowCount() > 0){
			
			?>
	<h3>End Point Information Data </h3>
	<form method="POST">
	<table  border="1">
	  <?php

		$output = "<table style=\"table-layout:fixed; width:500px\"border=\"1\">\n";
		$output .= "<tr style=\"width=10px; text-align:center; vertical-align:middle; word-wrap:break-word\"><th>Distance Start To End Point</th><th>Ground Height</th><th>Antenna Height</th><th>Antenna Cable Type</th><th>Antenna Cable Length</th></tr>\n";
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
			$output .= "<tr><td>" .$row['ed_distance']. "</td><td>"
				.$row['ed_ground_height']."</td><td>"
				.$row['ed_antenna_height']."</td><td>"
				.$row['ed_antenna_type']."</td><td>"
				.$row['ed_antenna_length']."</td></tr>\n";
		}
		$output .= "</table>\n";
		echo $output;
		} else {
			echo "No posts available\n";
		}
	}
	?>

	<table>
	<tr>
		<td><input type='button' value='Return to List' onclick='topFunction()'></td>
	</tr>
	</table>
	</form>	
<?php
}

// transfer to formfileupload.php
function validateViewDetails(){
	$err_msgs = array();
	if (!isset($_POST['list_select'])){
		$err_msgs[] = "One of the lists must be selected";
	} 
	return $err_msgs;
}
?>

