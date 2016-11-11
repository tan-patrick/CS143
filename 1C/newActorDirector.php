<html>
	<html>
	<head>
		<title>Add Actor/Director</title>
		</style>
	</head>	
	<body>
		<h2 align="center">Add Actor or Director</h2>
		<form method="GET">
			Add Actor or Director:	<input type="radio" name="individual" value="Actor">Actor
						<input type="radio" name="individual" value="Director">Director<br/>
			First Name:	<input type="text" name="first" maxlength="20"><br/>
			Last Name:	<input type="text" name="last" maxlength="20"><br/>
			Note: All names will be considered valid, but characters will be removed using mysql_real_escape_string.<br/>
			Sex (Can ignore for Director):		<input type="radio" name="sex" value="Male">Male
						<input type="radio" name="sex" value="Female">Female<br/>
			Date of Birth:	<input type="text" name="dob"><br/>
			Date of Death (Blank if still alive):	<input type="text" name="dod"><br/>
			Please input dates as YYYY-MM-DD format. (i.e. 2014-02-08 for February 8, 2014)<br/>
			<input type="submit" value="Submit"/>
		</form>

		<?php
		  $individual = $_GET["individual"];
		  $first = $_GET["first"];
		  $last = $_GET["last"];
		  $sex = $_GET["sex"];
		  $dob = $_GET["dob"];
		  $dod = $_GET["dod"];

		  $regexdate = "/^([0-9]{4})-([0-9]{2})-([0-9]{2})+$/";

		  $testdod = false;
		  $testinputs = false;

		  if($dob)
		  {
			  if(@preg_match($regexdate, $dob, $dates))
			  {
				$dob = $dates[1] . $dates[2] . $dates[3];
			  	if(checkdate($dates[2], $dates[3], $dates[1]))
			  		{
			  			if($dod)
			  			{
				  			if(@preg_match($regexdate, $dod, $ddates))
				  			{
				  				$dod = $ddates[1] . $ddates[2] . $ddates[3];
				  				if(checkdate($ddates[2], $ddates[3], $ddates[1]))
			  						if($dob < $dod)
				  						$testdod = true;
				  					else
				  						echo "Date of birth must be an earlier date than date of death";
				  				else
				  					echo "Please check that date of death is an actual date.";
				  			}
				  			else
				  				echo "Invalid date of death. Please use YYYY-MM-DD format";
				  		}
			  			if($testdod || !$dod)
			  			{
			  				if($individual)
			  					if($first)
			  						if($last)
					  					if($sex || $individual == "Director")
					  						$testinputs = true;
					  					else
					  						echo "Please choose individual's sex";
					  				else
					  					echo "Please input a last name";
					  			else
						  			echo "Please input a first name";
				  			else
				  				echo "Please choose Actor or Director";
			  			}
			  		}
			  	else
			  		echo "Please check that date of birth is an actual date.";
			  }
			  else
			  	echo "Invalid date of birth. Please use YYYY-MM-DD format.";
		  }

	      if($testinputs)
		  {
		    $db_connection = mysql_connect("localhost", "cs143", "");
			if(!$db_connection) {
			    $errmsg = mysql_error($db_connection);
			    echo "Connection failed: " . $errmsg . "<br />";
			    exit(1);
			}
		    mysql_select_db("CS143", $db_connection);

			$individual = mysql_real_escape_string($individual);
			$first = mysql_real_escape_string($first);
			$last = mysql_real_escape_string($last);
			$sex = mysql_real_escape_string($sex);
			$dob = mysql_real_escape_string($dob);
			$dod = mysql_real_escape_string($dod);

			if(!$dod)
				$dod = "NULL";

		    $incrquery = "UPDATE MaxPersonID SET id = id +1";

			$incrrs = mysql_query($incrquery, $db_connection);
		    if (!$incrrs) {
		      die('Invalid query: ' . mysql_error());
		    }

			$idquery = "SELECT id FROM MaxPersonID";

		    $idrs = mysql_query($idquery, $db_connection);
		    if (!$idrs) {
		      die('Invalid query: ' . mysql_error());
		    }

			while($row = mysql_fetch_row($idrs))
		    {
		    	foreach($row as $data)
		    		$id = $data;
		    }

			if($individual == "Actor")
				$insertquery = "INSERT INTO Actor VALUES
								(" . $id . ", '" . $last . "', '" . $first . "', '" . $sex . "', " . $dob . ", " . $dod . ")";
			elseif($individual == "Director")
				$insertquery = "INSERT INTO Director VALUES
								(" . $id . ", '" . $last . "', '" . $first . "', '" . $dob . "', " . $dod . ")";

		    $insertrs = mysql_query($insertquery, $db_connection);
		    if (!$insertrs) {
		      die('Invalid query: ' . mysql_error());
		    }

		    echo "Success: Added " . $first . " " . $last . " to database.";

			mysql_close($db_connection);
		  }
		?>

		<hr/>
		<a href="index.php">
			<button>
		   		Back to Home
			</button>
		</a>
	</body>
</html>
