<html>
	<html>
	<head>
		<title>Search Database</title>
	</head>	
	<body>
		<h2 align="center">Search Database</h2>

		<?php
		  $search = $_GET["search"];

		  if($search)
		  {
		    $db_connection = mysql_connect("localhost", "cs143", "");
			if(!$db_connection) {
			    $errmsg = mysql_error($db_connection);
			    echo "Connection failed: " . $errmsg . "<br />";
			    exit(1);
			}
		    mysql_select_db("CS143", $db_connection);

			$search = mysql_real_escape_string($search);

			$words = explode(" ", $search);

			echo "<h2> Actors: </h2>";

			$firstarr = array();
			$lastarr = array();
			$dobarr = array();
			$idarr = array();

			$j = 0;

			for($i = 0; $i < count($words); $i++)
			{
				$firstquery = "SELECT first, last, dob, id
							   FROM Actor
							   WHERE first LIKE '%" . $words[$i] . "%'";

				$firstrs = mysql_query($firstquery, $db_connection);
			    if (!$firstrs) {
			      die('Invalid query: ' . mysql_error());
			    }

			    while($row = mysql_fetch_row($firstrs))
			    {
			    	if(!in_array($row[3], $idarr))
			    	{
				    	$firstarr[$j] = $row[0];
				    	$lastarr[$j] = $row[1];
				    	$dobarr[$j] = $row[2];
				    	$idarr[$j] = $row[3];
				    	$j++;
			    	}

			    }
			}

			for($i = 0; $i < count($words); $i++)
			{
				$lastquery = "SELECT first, last, dob, id
							   FROM Actor
							   WHERE last LIKE '%" . $words[$i] . "%'";

				$lastrs = mysql_query($lastquery, $db_connection);
			    if (!$lastrs) {
			      die('Invalid query: ' . mysql_error());
			    }

			    while($row = mysql_fetch_row($lastrs))
			    {
			    	if(!in_array($row[3], $idarr))
			    	{
				    	$firstarr[$j] = $row[0];
				    	$lastarr[$j] = $row[1];
				    	$dobarr[$j] = $row[2];
				    	$idarr[$j] = $row[3];
				    	$j++;
			    	}
			    }
			}

			for($i = 0; $i < count($firstarr); $i++)
				echo "<a href = './browseActor.php?aid=" . $idarr[$i] . "'> " . $firstarr[$i] . " " . $lastarr[$i] . " (" . $dobarr[$i] . ")</a><br/>";

			echo "<h2> Movies: </h2>";

			$titlearr = array();
			$yeararr = array();
			$midarr = array();

			$j = 0;

			for($i = 0; $i < count($words); $i++)
			{
				$titlequery = "SELECT title, year, id
							   FROM Movie
							   WHERE title LIKE '%" . $words[$i] . "%'";

				$titlers = mysql_query($titlequery, $db_connection);
			    if (!$titlers) {
			      die('Invalid query: ' . mysql_error());
			    }

			    while($row = mysql_fetch_row($titlers))
			    {
			    	if(!in_array($row[2], $midarr))
			    	{
				    	$titlearr[$j] = $row[0];
				    	$yeararr[$j] = $row[1];
				    	$midarr[$j] = $row[2];
				    	$j++;
			    	}
			    }
			}

			for($i = 0; $i < count($titlearr); $i++)
				echo "<a href = './browseMovie.php?mid=" . $midarr[$i] . "'> " . $titlearr[$i] . " (" . $yeararr[$i] . ")</a><br/>";

			mysql_close($db_connection);
		  }
		  else
		  	echo "Please enter a search key below.";
		?>

		<hr/>
		<p align="center">
		  <form method="GET" align="center">
		    Search for another actor or movie: <input type="text" name="search">
		    <input type="submit" value="Search">
		  </form>
		</p>

		<a href="index.php">
			<button>
		   		Back to Home
			</button>
		</a>	
	</body>
</html>