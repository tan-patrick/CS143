<html>
	<html>
	<head>
		<title>Add Director to Movie</title>
	</head>	
	<body>
	<h2 align="center">Add Director to Movie</h2>
		<form method="GET">			
			Movie:	<input type="text" name="title" maxlength="50"><br/>
			Director Name - First: <input type="text" name="first" maxlength="20"> Last: <input type="text" name="last" maxlength="20"><br/>
			<input type="submit" value="Submit"/>
		</form>

		<?php
		  $title = $_GET["title"];
		  $first = $_GET["first"];
		  $last = $_GET["last"];

		  if($title && $first && $last)
		  {
		    $db_connection = mysql_connect("localhost", "cs143", "");
			if(!$db_connection) {
			    $errmsg = mysql_error($db_connection);
			    echo "Connection failed: " . $errmsg . "<br />";
			    exit(1);
			}
		    mysql_select_db("CS143", $db_connection);

			$title = mysql_real_escape_string($title);
			$first = mysql_real_escape_string($first);
			$last = mysql_real_escape_string($last);

		    $movieidquery = "SELECT id
		    				FROM Movie
		    				WHERE title = '" . $title . "'";

			$movieidrs = mysql_query($movieidquery, $db_connection);
		    if (!$movieidrs) {
		      die('Invalid query: ' . mysql_error());
		    }

			while($row = mysql_fetch_row($movieidrs))
		    {
		    	foreach($row as $data)
		    		$movieid = $data;
		    }

		    if($movieid)
		    {
		    	$directoridquery = "SELECT id
		    					FROM Director
		    					WHERE first = '" . $first . "' AND last = '" . $last . "'";

			    $directoridrs = mysql_query($directoridquery, $db_connection);
			    if (!$directoridrs) {
			      die('Invalid query: ' . mysql_error());
			    }

				while($row = mysql_fetch_row($directoridrs))
			    {
			    	foreach($row as $data)
			    		$directorid = $data;
			    }

			    if($directorid)
			    {
					$insertquery = "INSERT INTO MovieDirector VALUES
									(" . $movieid . ", " . $directorid . ")";

				    $insertrs = mysql_query($insertquery, $db_connection);
				    if (!$insertrs) {
				      die('Invalid query: ' . mysql_error());
			    	}

			    	echo "Success: Added " . $first . " " . $last . " to database.";
			    }
			    else
			    	echo " Please Enter a valid director name. Use the search page if you are unsure.";
		    }
		    else
		    	echo "Please enter a valid movie title. Use the search page if you are unsure.";

			mysql_close($db_connection);
		  }
		  else
		  	echo "Please fill out form completely."
		?>

		<hr/>
		<a href="index.php">
			<button>
		   		Back to Home
			</button>
		</a>
	</body>
</html>
