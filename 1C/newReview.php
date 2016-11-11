<html>
	<html>
	<head>
		<title>Add Review</title>
	</head>	
	<body>
	<h2 align="center">Add Review</h2>
		<form method="GET">			
			Movie:	<input type="text" name="title" maxlength="50"><br/>
			Reviewer Name:	<input type="text" name="name" maxlength="20"><br/>
			Rating:	<select name="rating">
						<option value="5">5</option>
						<option value="4">4</option>
						<option value="3">3</option>
						<option value="2">2</option>
						<option value="1">1</option>
					</select>
			<br/>
			Comment: <br/>
			<textarea name="comment" cols="60" rows="5"></textarea>
			<br/>
			<input type="submit" value="Submit"/>
		</form>

		<?php
		  $title = $_GET["title"];
		  $name = $_GET["name"];
		  $rating = $_GET["rating"];
		  $comment = $_GET["comment"];

		  if($title && $name && $rating)
		  {
		    $db_connection = mysql_connect("localhost", "cs143", "");
			if(!$db_connection) {
			    $errmsg = mysql_error($db_connection);
			    echo "Connection failed: " . $errmsg . "<br />";
			    exit(1);
			}
		    mysql_select_db("CS143", $db_connection);

			$title = mysql_real_escape_string($title);
			$name = mysql_real_escape_string($name);
			$comment = mysql_real_escape_string($comment);

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
				$insertquery = "INSERT INTO Review VALUES
								('" . $name . "', CURRENT_TIMESTAMP, " . $movieid . ", " . $rating . ", '" . $comment . "')";

			    $insertrs = mysql_query($insertquery, $db_connection);
			    if (!$insertrs) {
			      die('Invalid query: ' . mysql_error());
		    	}

		    	echo "Success: Added review to " . $title .".";
		    }
		    else
		    	echo "Please enter a valid movie title. Use the search page if you are unsure.";

			mysql_close($db_connection);
		  }
		  else
		  	echo "Please fill out form completely (May leave comment empty if you choose)."
		?>

		<hr/>
		<a href="index.php">
			<button>
		   		Back to Home
			</button>
		</a>		
	</body>
</html>
