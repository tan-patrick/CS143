<html>
	<html>
	<head>
		<title>Browse Movie</title>
	</head>	
	<body>
		<h2 align="center">Browse Movie Information</h2>

		<?php
		  $mid = $_GET["mid"];
		  $regexp = "/^[0-9]+$/";
	      if($mid)
	      {
		      if(@preg_match($regexp, $mid))
			  {
			    $db_connection = mysql_connect("localhost", "cs143", "");
				if(!$db_connection) {
				    $errmsg = mysql_error($db_connection);
				    echo "Connection failed: " . $errmsg . "<br />";
				    exit(1);
				}
			    mysql_select_db("CS143", $db_connection);

			    $titlequery = 	"SELECT title
			    				FROM Movie
			    				WHERE id = " . $mid;
			    $yearquery =	"SELECT year
			    				FROM Movie
			    				WHERE id = " . $mid;
			    $ratingquery = 	"SELECT rating
			    				FROM Movie
			    				WHERE id = " . $mid;
			    $companyquery = "SELECT company
			    				FROM Movie
			    				WHERE id = " . $mid;
			    $directorquery ="SELECT first, last
								FROM Movie, Director, MovieDirector
								WHERE Director.id = MovieDirector.did AND Movie.id = MovieDirector.mid AND Movie.id = " . $mid;
				$genrequery = 	"SELECT genre
								FROM MovieGenre, Movie
								WHERE MovieGenre.mid = Movie.id AND Movie.id = " . $mid;
				$actorquery = 	"SELECT first, last, role, aid
								FROM Actor, Movie, MovieActor
								WHERE Movie.id = MovieActor.mid AND MovieActor.aid = Actor.id AND Movie.id = " . $mid;
				$averagequery = "";
				$commentquery = "";

			    $titlers = mysql_query($titlequery, $db_connection);
			    if (!$titlers) {
			      die('Invalid query: ' . mysql_error());
			    }

			    $yearrs = mysql_query($yearquery, $db_connection);
			    if (!$yearrs) {
			      die('Invalid query: ' . mysql_error());
			    }

			    $ratingrs = mysql_query($ratingquery, $db_connection);
			    if (!$ratingrs) {
			      die('Invalid query: ' . mysql_error());
			    }

			    $companyrs = mysql_query($companyquery, $db_connection);
			    if (!$companyrs) {
			      die('Invalid query: ' . mysql_error());
			    }

				$directorrs = mysql_query($directorquery, $db_connection);
			    if (!$directorrs) {
			      die('Invalid query: ' . mysql_error());
			    }

			   	$genrers = mysql_query($genrequery, $db_connection);
			    if (!$genrers) {
			      die('Invalid query: ' . mysql_error());
			    }

				$actorrs = mysql_query($actorquery, $db_connection);
			    if (!$actorrs) {
			      die('Invalid query: ' . mysql_error());
			    }

			    while($row = mysql_fetch_row($titlers))
			    {
			    	foreach($row as $data)
			    		$title = $data;
			    }

			    while($row = mysql_fetch_row($yearrs))
			    {
			    	foreach($row as $data)
			    		$year = $data;
			    }

				while($row = mysql_fetch_row($ratingrs))
			    {
			    	foreach($row as $data)
			    		$rating = $data;
			    }

			    while($row = mysql_fetch_row($companyrs))
			    {
			    	foreach($row as $data)
			    		$company = $data;
			    }

			    echo "<h2>" . $title . "</h2>";

			    echo "Title: " . $title . "<br/>";
			    echo "Release Year: " . $year . "<br/>";
			    echo "MPAA Rating: " . $rating . "<br/>";
			    echo "Production Company: " . $company . "<br/>";


			    echo "Director: ";
			   	$one = true;
			   	while($row = mysql_fetch_row($directorrs))
			    {
			    	if(!$one)
			    		echo ", ";
			    	echo $row[0] . " " . $row[1];
			    	$one = false;
			    }
			    echo "<br/>";

			    echo "Genre: ";
			    $one = true;
				while($row = mysql_fetch_row($genrers))
			    {
			    	if(!$one)
			    		echo ", ";
			    	echo $row[0];
			    	$one = false;
			    }
			    echo "<br/>";

			    echo "<h2> Actors In " . $title . ":</echo><br/></h2>";

			    while($row = mysql_fetch_row($actorrs))
			    {
			        echo "<a href = './browseActor.php?aid=" . $row[3] . "'> " . $row[0] . " " . $row[1] . "</a> as " . $row[2] . "<br/>";
				}

				$reviewquery = "SELECT name, rating, comment
								FROM Review
								WHERE mid = " . $mid;

				$reviewrs = mysql_query($reviewquery, $db_connection);
			    if (!$reviewrs) {
			      die('Invalid query: ' . mysql_error());
			    }

				echo "<h2> Reviews:</echo><br/></h2>";

				$names = array();
				$reviews = array();

				$i = 0;
				$ratingTotal = 0;
				$numReviews = mysql_num_rows($reviewrs);

			    if($numReviews)
			    {
				    while($row = mysql_fetch_row($reviewrs))
				    {
				    	$reviews[$i] = $row[2];
				    	$names[$i] = $row[0];
				    	$ratingTotal = $ratingTotal + $row[1];
				    	$i++;
				    }
				    echo "Average Rating: " . $ratingTotal/$numReviews . "<br/><br/>";
				    echo "User Comments: <br/><br/>";
				    for($i = 0; $i < count($reviews); $i++)
				    {
				    	echo $names[$i] . ": ";
				    	if($reviews[$i] == '')
				    		echo "No comment";
				    	else
				    		echo $reviews[$i] . "<br/>";
				    }
				    echo "<br/>";
			    }
			    else
			    	echo "There are no reviews yet. Click below to add the first review!";
			    echo "<a href='newReview.php'>
						<button>
					   		Add a Review
						</button>
					</a>";
					
				mysql_close($db_connection);
			}
		  else
		      echo "Invalid Movie ID (Only numeric values allowed). Please search again.";
		  }
		?>

		<br/>

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