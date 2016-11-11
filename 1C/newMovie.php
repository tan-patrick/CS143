<html>
	<html>
	<head>
		<title>Add Movie</title>
	</head>	
	<body>
		<h2 align="center">Add Movie</h2>
		<form method="GET">			
			Movie Title : <input type="text" name="title" maxlength="100"><br/>
			Release Year : <input type="text" name="year" maxlength="4"><br/>
			MPAA Rating : <select name="rating">
				<option value="G">G</option>
				<option value="PG">PG</option>
				<option value="PG-13">PG-13</option>
				<option value="R">R</option>
				<option value="surrendere">surrendere</option>
				<option value="NC-17">NC-17</option>
				</select>
			<br/>
			Production Company: <input type="text" name="company" maxlength="50"><br/>
			Genre :
				<input type="checkbox" name="action" value="Action">Action</input>
				<input type="checkbox" name="adult" value="Adult">Adult</input>
				<input type="checkbox" name="adventure" value="Adventure">Adventure</input>
				<input type="checkbox" name="animation" value="Animation">Animation</input>
				<input type="checkbox" name="comedy" value="Comedy">Comedy</input>
				<input type="checkbox" name="crime" value="Crime">Crime</input>
				<input type="checkbox" name="documentary" value="Documentary">Documentary</input>
				<input type="checkbox" name="drama" value="Drama">Drama</input>
				<input type="checkbox" name="family" value="Family">Family</input>
				<input type="checkbox" name="fantasy" value="Fantasy">Fantasy</input>
				<input type="checkbox" name="horror" value="Horror">Horror</input>
				<input type="checkbox" name="musical" value="Musical">Musical</input>
				<input type="checkbox" name="mystery" value="Mystery">Mystery</input>
				<input type="checkbox" name="romance" value="Romance">Romance</input>
				<input type="checkbox" name="scifi" value="Sci-Fi">Sci-Fi</input>
				<input type="checkbox" name="short" value="Short">Short</input>
				<input type="checkbox" name="thriller" value="Thriller">Thriller</input>
				<input type="checkbox" name="war" value="War">War</input>
				<input type="checkbox" name="western" value="Western">Western</input>
			<br/>
			
			<input type="submit" value="Submit"/>
		</form>	
		<?php
		  $title = $_GET["title"];
		  $year = $_GET["year"];
		  $rating = $_GET["rating"];
		  $company = $_GET["company"];
		  $action = $_GET["action"];
		  $adult = $_GET["adult"];
		  $adventure = $_GET["adventure"];
		  $animation = $_GET["animation"];
		  $comedy = $_GET["comedy"];
		  $crime = $_GET["crime"];
		  $documentary = $_GET["documentary"];
		  $drama = $_GET["drama"];
		  $family = $_GET["family"];
		  $fantasy = $_GET["fantasy"];
		  $horror = $_GET["horror"];
		  $musical = $_GET["musical"];
		  $mystery = $_GET["mystery"];
		  $romance = $_GET["romance"];
		  $scifi = $_GET["scifi"];
		  $short = $_GET["short"];
		  $thriller = $_GET["thriller"];
		  $war = $_GET["war"];
		  $western = $_GET["western"];

		  $regexdate = "/^[1-2][0-9][0-9][0-9]+$/";

		  if(@preg_match($regexdate, $year) && $title && $year && $rating)
		  {
		    $db_connection = mysql_connect("localhost", "cs143", "");
			if(!$db_connection) {
			    $errmsg = mysql_error($db_connection);
			    echo "Connection failed: " . $errmsg . "<br />";
			    exit(1);
			}
		    mysql_select_db("CS143", $db_connection);

			$title = mysql_real_escape_string($title);
			$year = mysql_real_escape_string($year);
			$company = mysql_real_escape_string($company);

		    $incrquery = "UPDATE MaxMovieID SET id = id + 1";

			$incrrs = mysql_query($incrquery, $db_connection);
		    if (!$incrrs) {
		      die('Invalid query: ' . mysql_error());
		    }

			$idquery = "SELECT id FROM MaxMovieID";

		    $idrs = mysql_query($idquery, $db_connection);
		    if (!$idrs) {
		      die('Invalid query: ' . mysql_error());
		    }

			while($row = mysql_fetch_row($idrs))
		    {
		    	foreach($row as $data)
		    		$id = $data;
		    }

			$insertquery = "INSERT INTO Movie VALUES
				(" . $id . ", '" . $title . "', " . $year . ", '" . $rating . "', '" . $company . "')";

		    $insertrs = mysql_query($insertquery, $db_connection);
		    if (!$insertrs) {
		      die('Invalid query: ' . mysql_error());
		    }

		    echo "Success: Added " . $title . " to database.";

			if($action)
			{
				$actionquery = "INSERT INTO MovieGenre VALUES
								(" . $id . ", '" . $action . "')";

				$actionrs = mysql_query($actionquery, $db_connection);
		    	if (!$actionrs) {
		      		die('Invalid query: ' . mysql_error());
		    	}				
			}

			if($adult)
			{
				$adultquery = "INSERT INTO MovieGenre VALUES
								(" . $id . ", '" . $adult . "')";

				$adultrs = mysql_query($adultquery, $db_connection);
		    	if (!$adultrs) {
		      		die('Invalid query: ' . mysql_error());
		    	}				
			}

			if($adventure)
			{
				$adventurequery = "INSERT INTO MovieGenre VALUES
								(" . $id . ", '" . $adventure . "')";

				$adventurers = mysql_query($adventurequery, $db_connection);
		    	if (!$adventurers) {
		      		die('Invalid query: ' . mysql_error());
		    	}				
			}

			if($animation)
			{
				$animationquery = "INSERT INTO MovieGenre VALUES
								(" . $id . ", '" . $animation . "')";

				$animationrs = mysql_query($animationquery, $db_connection);
		    	if (!$animationrs) {
		      		die('Invalid query: ' . mysql_error());
		    	}				
			}

			if($comedy)
			{
				$comedyquery = "INSERT INTO MovieGenre VALUES
								(" . $id . ", '" . $comedy . "')";

				$comedyrs = mysql_query($comedyquery, $db_connection);
		    	if (!$comedyrs) {
		      		die('Invalid query: ' . mysql_error());
		    	}				
			}

			if($crime)
			{
				$crimequery = "INSERT INTO MovieGenre VALUES
								(" . $id . ", '" . $crime . "')";

				$crimers = mysql_query($crimequery, $db_connection);
		    	if (!$crimers) {
		      		die('Invalid query: ' . mysql_error());
		    	}				
			}

			if($documentary)
			{
				$documentaryquery = "INSERT INTO MovieGenre VALUES
								(" . $id . ", '" . $documentary . "')";

				$documentaryrs = mysql_query($documentaryquery, $db_connection);
		    	if (!$documentaryrs) {
		      		die('Invalid query: ' . mysql_error());
		    	}				
			}

			if($drama)
			{
				$dramaquery = "INSERT INTO MovieGenre VALUES
								(" . $id . ", '" . $drama . "')";

				$dramars = mysql_query($dramaquery, $db_connection);
		    	if (!$dramars) {
		      		die('Invalid query: ' . mysql_error());
		    	}				
			}

			if($family)
			{
				$familyquery = "INSERT INTO MovieGenre VALUES
								(" . $id . ", '" . $family . "')";

				$familyrs = mysql_query($familyquery, $db_connection);
		    	if (!$familyrs) {
		      		die('Invalid query: ' . mysql_error());
		    	}				
			}

			if($fantasy)
			{
				$fantasyquery = "INSERT INTO MovieGenre VALUES
								(" . $id . ", '" . $fantasy . "')";

				$fantasyrs = mysql_query($fantasyquery, $db_connection);
		    	if (!$fantasyrs) {
		      		die('Invalid query: ' . mysql_error());
		    	}				
			}

			if($horror)
			{
				$horrorquery = "INSERT INTO MovieGenre VALUES
								(" . $id . ", '" . $horror . "')";

				$horrorrs = mysql_query($horrorquery, $db_connection);
		    	if (!$horrorrs) {
		      		die('Invalid query: ' . mysql_error());
		    	}				
			}

			if($musical)
			{
				$musicalquery = "INSERT INTO MovieGenre VALUES
								(" . $id . ", '" . $musical . "')";

				$musicalrs = mysql_query($musicalquery, $db_connection);
		    	if (!$musicalrs) {
		      		die('Invalid query: ' . mysql_error());
		    	}				
			}

			if($mystery)
			{
				$mysteryquery = "INSERT INTO MovieGenre VALUES
								(" . $id . ", '" . $mystery . "')";

				$mysteryrs = mysql_query($mysteryquery, $db_connection);
		    	if (!$mysteryrs) {
		      		die('Invalid query: ' . mysql_error());
		    	}				
			}

			if($romance)
			{
				$romancequery = "INSERT INTO MovieGenre VALUES
								(" . $id . ", '" . $romance . "')";

				$romancers = mysql_query($romancequery, $db_connection);
		    	if (!$romancers) {
		      		die('Invalid query: ' . mysql_error());
		    	}				
			}

			if($scifi)
			{
				$scifiquery = "INSERT INTO MovieGenre VALUES
								(" . $id . ", '" . $scifi . "')";

				$scifirs = mysql_query($scifiquery, $db_connection);
		    	if (!$scifirs) {
		      		die('Invalid query: ' . mysql_error());
		    	}				
			}

			if($short)
			{
				$shortquery = "INSERT INTO MovieGenre VALUES
								(" . $id . ", '" . $short . "')";

				$shortrs = mysql_query($shortquery, $db_connection);
		    	if (!$shortrs) {
		      		die('Invalid query: ' . mysql_error());
		    	}				
			}

			if($thriller)
			{
				$thrillerquery = "INSERT INTO MovieGenre VALUES
								(" . $id . ", '" . $thriller . "')";

				$thrillerrs = mysql_query($thrillerquery, $db_connection);
		    	if (!$thrillerrs) {
		      		die('Invalid query: ' . mysql_error());
		    	}				
			}

			if($war)
			{
				$warquery = "INSERT INTO MovieGenre VALUES
								(" . $id . ", '" . $war . "')";

				$warrs = mysql_query($warquery, $db_connection);
		    	if (!$warrs) {
		      		die('Invalid query: ' . mysql_error());
		    	}				
			}

			if($western)
			{
				$westernquery = "INSERT INTO MovieGenre VALUES
								(" . $id . ", '" . $western . "')";

				$westernrs = mysql_query($westernquery, $db_connection);
		    	if (!$westernrs) {
		      		die('Invalid query: ' . mysql_error());
		    	}				
			}

			mysql_close($db_connection);
		  }
		  else
		  	echo "Please fill out form completely to add movie."
		?>
		<hr/>
		<a href="index.php">
			<button>
		   		Back to Home
			</button>
		</a>	
	</body>
</html>