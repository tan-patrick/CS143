<html>
	<html>
	<head>
		<title>Add Actor to Movie</title>
	</head>	
	<body>
	<h2 align="center">Add Actor to Movie</h2>
		<form method="GET">			
			Movie:	<input type="text" name="title" maxlength="100"><br/>
			Actor Name - First: <input type="text" name="first" maxlength="20"> Last: <input type="text" name="last" maxlength="20"><br/>
			Role:	<input type="text" name="role" maxlength="50"><br/>
			<input type="submit" value="Submit"/>
		</form>

		<?php
		  $title = $_GET["title"];
		  $first = $_GET["first"];
		  $last = $_GET["last"];
		  $role = $_GET["role"];

		  if($title && $first && $last && $role)
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
			$role = mysql_real_escape_string($role);

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
		    	$actoridquery = "SELECT id
		    					FROM Actor
		    					WHERE first = '" . $first . "' AND last = '" . $last . "'";

			    $actoridrs = mysql_query($actoridquery, $db_connection);
			    if (!$actoridrs) {
			      die('Invalid query: ' . mysql_error());
			    }

				while($row = mysql_fetch_row($actoridrs))
			    {
			    	foreach($row as $data)
			    		$actorid = $data;
			    }

			    if($actorid)
			    {
					$insertquery = "INSERT INTO MovieActor VALUES
									(" . $movieid . ", " . $actorid . ", '" . $role . "')";

				    $insertrs = mysql_query($insertquery, $db_connection);
				    if (!$insertrs) {
				      die('Invalid query: ' . mysql_error());
			    	}

			    	echo "Success: Added " . $first . " " . $last . " to database.";
			    }
			    else
			    	echo " Please Enter a valid actor name. Use the search page if you are unsure.";
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
