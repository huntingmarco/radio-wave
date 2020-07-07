<!-- 
1.purpose: this file is to list up the path data for selecting one path from all paths stored in the database, and
it uses AJAX to send the selected path to the server and to receive the path data to display.
2. authors: Group2
-->
<?php 
session_start(); 

require_once("../includes/db_connection.php");
require_once("../includes/displayStatus.php");
require_once("../includes/displayErrors.php");  

function displayData(){
	$db_conn = connectDB();
	$qry = "select * from path_info order by pt_id;";
	//$rs = $db_conn->query($qry);
	$stmt = $db_conn->prepare($qry);
	if (!$stmt){
	//if ($rs->num_rows > 0){
		echo "<p>Error in view prepare: ".$db_conn->errorCode()."</p>\n<p>Message ".implode($db_conn->errorInfo())."</p>\n";
		exit(1);
	}
	$status = $stmt->execute();
	if ($stmt->rowCount() > 0){
		$output = "<table border=\"1\">\n";
		$output .= "<tr><th>Select path</th><th>Name</th><th>Frequency</th><th>Description</th><th>Note</th></tr>\n";
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
		//while ($row = $rs->fetch_assoc()){
			
			$output .= "<tr><td><input type='radio' id='list_select'  name='list_select[]' value='" .$row['pt_id']. "'></td><td>"
				.$row['pt_name']."</td><td>"
				.$row['pt_frequency']."</td><td>"
				.$row['pt_description']."</td><td>"
				.$row['pt_note']."</td>"
				."</tr>\n";
		}
		$output .= "</table>\n";
		echo $output;
	} else {
		echo "No posts available\n";
	}
}


?>
<html>
<head>
	<meta charset="utf-8">
	<title>Path Data Details</title>
	<link href="../css/style.css" type="text/css" rel="stylesheet"/>
	<script src="../js/jquery-3.2.1.js"></script>
	<script src="./js/formDisplayPath_ajax.js"></script> 	
	
</head>
<body>
	<h2> Information List</h2>
	<span style="font-weight:bold">[Note] Please, choose a path data to display and then click the 'View Details' button</span><br><br><br>

	<form method="POST" id="DetailsForm" onsubmit="return validateViewDetails()">
	<?php 
		displayData();
		$db_conn= NULL;
	?>
	<br>
	<p>
	<input type="submit" id="viewDetails" name="view" value="View Details"/>
	</p>
	</form>
		

	<a href="../index.php">Return to menu</a><br><br>
	<p id="detailResult"></p>
</body>
</html>
