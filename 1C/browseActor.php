<html>
	<html>
	<head>
		<title>Browse Actor</title>
	</head>	
	<body>
		<h2 align="center">Browse Actor Information</h2>

		<?php
		  $aid = $_GET["aid"];
		  $regexp = "/^[0-9]+$/";
	      if(@preg_match($regexp, $aid))
		  {
		    $db_connection = mysql_connect("localhost", "cs143", "");
			if(!$db_connection) {
			    $errmsg = mysql_error($db_connection);
			    echo "Connection failed: " . $errmsg . "<br />";
			    exit(1);
			}
		    mysql_select_db("CS143", $db_connection);

		    $firstquery = 	"SELECT first
		    				FROM Actor
		    				WHERE id = " . $aid;
		    $lastquery =		"SELECT last
		    				FROM Actor
		    				WHERE id = " . $aid;
		    $sexquery = 	"SELECT sex
		    				FROM Actor
		    				WHERE id = " . $aid;
		    $dobquery = 	"SELECT dob
		    				FROM Actor
		    				WHERE id = " . $aid;
		    $dodquery = 	"SELECT dod
		    				FROM Actor
		    				WHERE id = " . $aid;
	    	$actorquery = 	"SELECT role, title, mid
							FROM Movie, MovieActor, Actor
							WHERE Actor.id = " . $aid . " AND Movie.id = MovieActor.mid AND MovieActor.aid = " . $aid;

		    $firstrs = mysql_query($firstquery, $db_connection);
		    if (!$firstrs) {
		      die('Invalid query: ' . mysql_error());
		    }

		    $lastrs = mysql_query($lastquery, $db_connection);
		    if (!$lastrs) {
		      die('Invalid query: ' . mysql_error());
		    }

		    $sexrs = mysql_query($sexquery, $db_connection);
		    if (!$sexrs) {
		      die('Invalid query: ' . mysql_error());
		    }

		    $dobrs = mysql_query($dobquery, $db_connection);
		    if (!$dobrs) {
		      die('Invalid query: ' . mysql_error());
		    }

		    $dodrs = mysql_query($dodquery, $db_connection);
		    if (!$dodrs) {
		      die('Invalid query: ' . mysql_error());
		    }

		    $actorrs = mysql_query($actorquery, $db_connection);
		    if (!$actorrs) {
		      die('Invalid query: ' . mysql_error());
		    }

		    while($row = mysql_fetch_row($firstrs))
		    {
		    	foreach($row as $data)
		    		$first = $data;
		    }

		    while($row = mysql_fetch_row($lastrs))
		    {
		    	foreach($row as $data)
		    		$last = $data;
		    }

			while($row = mysql_fetch_row($sexrs))
		    {
		    	foreach($row as $data)
		    		$sex = $data;
		    }

		    while($row = mysql_fetch_row($dobrs))
		    {
		    	foreach($row as $data)
		    		$dob = $data;
		    }

		    while($row = mysql_fetch_row($dodrs))
		    {
		    	foreach($row as $data)
		    		$dod = $data;
		    }

		    if($dod == NULL)
		    	$dod = "Still Alive";

		    echo "<h2>" . $first . " " . $last . "</h2>";

		    echo "Name: " . $first . " " . $last . "<br/>";
		    echo "Sex: " . $sex . "<br/>";
		    echo "Date of Birth: " . $dob . "<br/>";
		    echo "Date of Death: " . $dod . "<br/>";

		    echo "<br/>";

		    echo "<h2> Acted In: </echo><br/></h2>";

		    while($row = mysql_fetch_row($actorrs))
		    {
		        echo "<a href = './browseMovie.php?mid=" . $row[2] . "'> " . $row[1] . "</a> as " . $row[0] . "<br/>";
			}

			mysql_close($db_connection);
		  }
		  else
		      echo "Invalid Actor ID (Only numeric values allowed). Please search again.";
		?>

		<hr/>
		<p align="center">
		  <form action="search.php" method="GET" align="center">
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