<!-- 
1.purpose: this file is to display errors into the form when errors happen.
2. authors: Group2
-->
<?php
function displayErrors(array $error_msg){
	
	echo "<div>\n";
	echo "<h3> This form contains the following errors</h3>\n";
	echo "<ul class='warning'>\n";
	foreach ($error_msg as $err){
		echo "<li>".$err."</li>\n";
	}
	echo "</ul>\n";
	echo "</div>\n";
}

?>
