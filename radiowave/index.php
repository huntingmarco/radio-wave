<!-- 
1.purpose: this file is an index php source to list the web applications.
the users can choose one of web applications they need to work.
2. authors: group2
-->
<html>
<head>
	<title>Microwave Communication System</title>
	<link href="./css/style.css" type="text/css" rel="stylesheet"/>
	<script src="./js/jquery-3.2.1.js" type="text/javascript"></script>

</head>
<body>
	<h2>Microwave Radio Path Applications</h2>
	<fieldSet>
	<legend>Part 1</legend>
	<ul>
		<li><a href="./part1/formFileUpload.php">Web application #1 - Web application to upload the microwave radio path file </a></li></br>
		<li><a href="./part1/formDisplayPath.php">Web application #2 - Web application to list up path file and display the selected path data</a></li></br>
		<li><a href="./part1/formDisplayResetList.php">Web application #3 - Web application to reset the database content for a path to the originally uploaded data</a></li>
	</ul>
	</fieldSet>

    <fieldSet>
	<legend>Part 2</legend>
	<ul>
		<li><a href="./part2/formEditPath.php">Web application #1 - Web application to edit data for selected microwave path</a></li>
		
	</ul>
	</fieldSet>

	<fieldSet>
	<legend>Part 3</legend>
	<ul>
		<li><a href="./part3/formCalculatePath.php">Web application #1 - Web application to calculate data for selected microwave path</a></li>
		
	</ul>
	</fieldSet>
</body>
</html>
