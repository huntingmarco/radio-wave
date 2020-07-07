<?php

/*1.purpose: this file is to open a PDO connection to the microwave_radio_path database using the microwave_radio_path 
account, and that proper error handling is included.
2. authors: Group2*/

	define("DBHOST", "localhost");
	define("DBDB",   "microwave_radio_path");
	define("DBUSER", "lamp2user");
	define("DBPW", "!Lamp12!");

	function connectDB(){
		$dsn = "mysql:host=".DBHOST.";dbname=".DBDB.";charset=utf8";
		try{
			$db_conn = new PDO($dsn, DBUSER, DBPW);
			$db_conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			return $db_conn;
		} catch (PDOException $e){
			echo "<p>Error opening database <br/>\n".$e->getMessage()."</p>\n";
			exit(1);
		}
	}

?>
